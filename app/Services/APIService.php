<?php

namespace App\Services;

use App\Models\Assets\Asset;
use App\Models\User;
use Illuminate\Support\Facades\Http;


class APIService
{
    public $assets;
    private $apiValues;
    public $processedData;
    public $totalValues;

    public function __construct(User $user)
    {
        $this->assets = Asset::where('user_id', $user->id)->get();
        $this->apiValues = $this->fetchData();
        $this->processedData = $this->checkApiValues(); //dd($this->processedData);
    }

    private function fetchData()
    {
        $tickers = $this->getAssetTicker();
        if (!empty($tickers)) {
            $response = Http::get("https://brapi.dev/api/quote/$tickers?range=1d&interval=1d&fundamental=false");
            if ($response->successful()) {
                return $response->json()['results'];
            } else {
                return false;
            }
        } else {
            return null;
        }
    }

    private function buildArrayWithAllValues(): array
    {
        $processedData = [];

        $currentPrice = $this->getCurrentPrice();
        $dailyVariation = $this->getDailyVariation();
        $dailyMoneyVariation = $this->getDailyMoneyVariation();
        $totalPercentVariation = $this->getTotalPercentVariation();
        $totalMoneyVariation = $this->getTotalMoneyVariation();
        $patrimony = $this->getPatrimony();
        $logo = $this->getAssetLogo();
        $this->totalValues = $this->getTotalValues($dailyMoneyVariation, $patrimony);

        $processedData = array_map(function ($current, $dailyVar, $dailyMoneyVar, $totalPercentVar, $totalMoneyVar, $patrimony, $logo) {
            return [
                'current_price' => $current,
                'daily_variation' => $dailyVar,
                'daily_money_variation' => $dailyMoneyVar,
                'total_percent_variation' => $totalPercentVar,
                'total_money_variation' => $totalMoneyVar,
                'patrimony' => $patrimony,
                'asset_logo' => $logo ? $logo : "https://s3-symbol-logo.tradingview.com/fii--big.svg" //default logo for reit if not found
            ];
        }, $currentPrice, $dailyVariation, $dailyMoneyVariation, $totalPercentVariation, $totalMoneyVariation, $patrimony, $logo);

        return $processedData;
    }

    private function getAssetTicker(): string
    {
        return $this->assets->pluck('code')->implode('%2C');
    }

    private function getCurrentPrice(): array
    {
        return collect($this->apiValues)->pluck('regularMarketPrice')->all();
    }

    private function getDailyVariation(): array
    {
        return collect($this->apiValues)
        ->pluck('regularMarketChangePercent')
        ->map(function ($price) {
            return round($price, 2);
        })
        ->all();
    }

    private function getDailyMoneyVariation(): array
    {
        $assetQuantity = $this->assets->pluck('quantity');
        foreach ($this->apiValues as $key => $value) {
            if ($value['regularMarketOpen'] != 0) {
                $daily[] = number_format((($value['regularMarketPrice'] - $value['regularMarketOpen']) * $assetQuantity[$key]), 2, ',', '.');
            } else {
                $daily[] = number_format((($value['regularMarketPrice'] - $value['regularMarketPreviousClose']) * $assetQuantity[$key]), 2, ',', '.');
            }
            
        }
        return $daily;
    }

    private function getTotalPercentVariation(): array
    {
        $averagePrice = $this->assets->pluck('average_price')->all();
        $currentPrice = collect($this->apiValues)->pluck('regularMarketPrice')->all();

        $result = array_map(function ($a, $b) {
            return round((($b - $a) / $a) * 100, 2);
        }, $averagePrice, $currentPrice);

        return $result;
    }
    
    private function getTotalMoneyVariation(): array
    {
        $currentPrice = collect($this->apiValues)->pluck('regularMarketPrice')->all();
        $assetQuantity = $this->assets->pluck('quantity')->all();
        $initialValue = $this->assets->pluck('average_price')->all();

        $result = array_map(function ($current, $qty, $initial) {
            return number_format(($current - $initial) * $qty, 2, ',', '.');
        }, $currentPrice, $assetQuantity, $initialValue);

        return $result;
    }

    private function getPatrimony(): array
    {
        $currentPrice = collect($this->apiValues)->pluck('regularMarketPrice')->all();
        $assetQuantity = $this->assets->pluck('quantity')->all();

        $result = array_map(function ($a, $b) {
            return number_format($a * $b, 2, ',', '.'); //round($a * $b, 2);
        }, $currentPrice, $assetQuantity);

        return $result;
    }

    public function getTotalValues($totalMoneyVariation, $patrimony): array
    {
        $totalMoney = number_format(array_sum($totalMoneyVariation), 2, ',', '.');
        $patrim = number_format(array_sum($patrimony), 2, ',', '.');

        return [$totalMoney, $patrim];
    }

    private function getAssetLogo(): array
    {
        return collect($this->apiValues)->pluck('logourl')->all();
    }

    private function checkApiValues()
    {
        switch ($this->apiValues) {
            case null:
                return "no assets";
                break;
            case false:
                return "error";
                break;
            default:
                return $this->buildArrayWithAllValues();
                break;
        }
    }
}
