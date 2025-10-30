<?php

namespace App\Http\Controllers\Api;

use App\Models\Cosmetic;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CosmeticApiResource;

/**
 * CosmeticController
 *
 * Controller ini menangani endpoint API untuk data kosmetik.
 * 
 * Endpoint yang disediakan:
 * - GET /api/cosmetics           → Menampilkan semua kosmetik (dengan filter opsional)
 * - GET /api/cosmetics/{id}      → Menampilkan detail satu kosmetik
 */
class CosmeticController extends Controller
{
    /**
     * Menampilkan daftar semua kosmetik (bisa difilter berdasarkan parameter tertentu).
     * 
     * Query parameter yang didukung:
     * - category_id : filter berdasarkan kategori kosmetik
     * - brand_id    : filter berdasarkan brand kosmetik
     * - is_popular  : filter hanya kosmetik populer (1 = ya, 0 = tidak)
     * - limit       : batasi jumlah data yang ditampilkan
     * 
     * Contoh:
     * GET /api/cosmetics?category_id=2&brand_id=5&is_popular=1&limit=10
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        // Ambil semua data kosmetik beserta relasi brand dan category
        $cosmetics = Cosmetic::with(['brand', 'category']);

        // Filter berdasarkan kategori (jika ada parameter category_id)
        if ($request->has('category_id')) {
            $cosmetics->where('category_id', $request->input('category_id'));
        }

        // Filter berdasarkan brand (jika ada parameter brand_id)
        if ($request->has('brand_id')) {
            $cosmetics->where('brand_id', $request->input('brand_id'));
        }

        // Filter berdasarkan status popularitas (is_popular = 1/0)
        if ($request->has('is_popular')) {
            $cosmetics->where('is_popular', $request->input('is_popular'));
        }

        // Batasi jumlah data jika ada parameter limit
        if ($request->has('limit')) {
            $cosmetics->limit($request->input('limit'));
        }

        // Ambil data dari database dan ubah ke format API Resource Collection
        return CosmeticApiResource::collection($cosmetics->get());
    }

    /**
     * Menampilkan detail satu kosmetik berdasarkan ID.
     * 
     * Laravel akan otomatis mencari data kosmetik berdasarkan parameter {cosmetic}
     * yang dikirim lewat route (Route Model Binding).
     * 
     * Relasi yang dimuat:
     * - category     : kategori kosmetik
     * - benefits     : daftar manfaat kosmetik
     * - testimonials : ulasan/testimoni pengguna
     * - photos       : daftar foto produk
     * - brand        : merek kosmetik
     * 
     * Contoh: GET /api/cosmetics/5
     * 
     * @param  \App\Models\Cosmetic  $cosmetic
     * @return \App\Http\Resources\Api\CosmeticApiResource
     */
    public function show(Cosmetic $cosmetic)
    {
        // Muat relasi-relasi penting agar data lengkap
        $cosmetic->load(['category', 'benefits', 'testimonials', 'photos', 'brand']);

        // Kembalikan data kosmetik dalam format API Resource tunggal
        return new CosmeticApiResource($cosmetic);
    }
}
