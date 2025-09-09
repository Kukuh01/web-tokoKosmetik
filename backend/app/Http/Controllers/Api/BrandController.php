<?php

namespace App\Http\Controllers\Api;

use App\Models\Brand;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\BrandApiResource;

class BrandController extends Controller
{
    //
    public function index(Request $request) {
        $brands = Brand::withCount(['cosmetics']);

        if($request->has('limit')){
            $brands->limit($request->input('limit'));
        }

        return BrandApiResource::collection($brands->get());
    }

    public function show(Brand $brand) {
        $brand->load(['cosmetics', 'popularCosmetics']);
        $brand->loadCount(['cosmetics']);

        return new BrandApiResource($brand);
    }
}
