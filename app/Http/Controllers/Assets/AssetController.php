<?php

namespace App\Http\Controllers\Assets;

use App\Http\Controllers\Controller;
use App\Models\Assets\Asset;
use App\Services\APIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssetController extends Controller
{
    protected $service;

    public function index()
    {
        $this->service = new APIService(Auth::user());
        $api = $this->service->processedData;
        $stocks = Asset::where('user_id', Auth::user()->id)->where('type', 'stocks')->get(); 
        $reit = Asset::where('user_id', Auth::user()->id)->where('type', 'reit')->get();

        switch ($api) {
            case $api === "error":
                return view('assets.api-error');
                break;
            case $api == "no assets":
                return view('assets.first-in');
                break;
            default:
                return view('assets.index')->with(['assets' => $this->service->assets, 'stocks' => $stocks, 'reit' => $reit, 'api' => $api]);
                break;
        }
    }

    public function show(string $id)
    {
        $asset = Asset::findOrFail($id);
        return view('assets.show', compact('asset'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $data['user_id'] = Auth::user()->id;
        $data['status'] = 1;
        $data['name'] = 'Só teste';
        $data['average_price'] = preg_replace('/,(\d+)/', '.$1', $data['average_price']);
        Asset::create($data);

        return redirect(route('assets.index'));
    }

    public function edit(string $id)
    {
        $asset = Asset::findOrFail($id);
        
        return view('assets.edit', compact('asset'));
    }

    public function update(Request $request, string $id)
    {
        $asset = Asset::findOrFail($id);
        $asset->update($request->all());

        return redirect(route('assets.index'));
    }

    public function destroy(string $id)
    {
        Asset::findOrFail($id)->delete();

        return redirect(route('assets.index'));
    }
}
