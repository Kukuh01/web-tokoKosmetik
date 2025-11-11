<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CategoryApiResource;

/**
 * CategoryController
 *
 * Controller ini digunakan untuk menangani permintaan API terkait data kategori.
 * 
 * Endpoint yang disediakan:
 * - GET /api/categories       → mengambil semua kategori
 * - GET /api/categories/{id}  → mengambil detail satu kategori
 */
class CategoryController extends Controller
{
    /**
     * Menampilkan daftar semua kategori.
     * 
     * Mendukung query parameter:
     * - limit: membatasi jumlah kategori yang diambil (opsional)
     * 
     * Contoh: GET /api/categories?limit=5
     */
    public function index(Request $request)
    {
        // Ambil semua kategori, sekaligus hitung jumlah kosmetik pada setiap kategori
        $categories = Category::withCount(['cosmetics']);

        // Jika ada parameter 'limit' pada request, batasi jumlah hasil
        if ($request->has('limit')) {
            $categories->limit($request->input('limit'));
        }

        // Gunakan Resource untuk memformat respons API
        // CategoryApiResource::collection() akan mengubah semua data menjadi format JSON standar
        return CategoryApiResource::collection($categories->get());
    }

    /**
     * Menampilkan detail satu kategori berdasarkan ID.
     * 
     * Route model binding otomatis menyediakan objek Category
     * berdasarkan parameter {category} pada route.
     * 
     * Contoh: GET /api/categories/3
     */
    public function show(Category $category)
    {
        // Muat relasi 'cosmetics' dan 'popularCosmetics'
        // agar data produk terkait kategori langsung tersedia
        $category->load(['cosmetics', 'popularCosmetics', 'cosmetics.brand']);

        // Tambahkan hitungan jumlah kosmetik (cosmetics_count)
        $category->loadCount(['cosmetics']);

        // Kembalikan data dalam format API Resource
        return new CategoryApiResource($category);
    }
}
