<?php

namespace App\Http\Controllers\Assets;

use App\Http\Controllers\Controller;
use App\Models\Assets\Asset;
use App\Services\APIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssetController extends Controller
{
    public function __construct(protected APIService $service)
    {
    }

    public function index()
    {
        $assets = Asset::all();
        $api = $this->service->processedData; //dd($api);
        return view('assets.index')->with(['assets' => $assets, 'api' => $api]);
    }

    public function show(string $id)
    {
        $asset = Asset::findOrFail($id);
        return view('assets.show', compact('asset'));
    }

    public function create()
    {
        return view('assets.create');
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $data['user_id'] = Auth::user()->id;
        $data['status'] = 1;
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
