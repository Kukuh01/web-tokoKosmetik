<?php

namespace App\Http\Controllers\Api;

use App\Models\Cosmetic;
use Illuminate\Http\Request;
use App\Models\BookingTransaction;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingTransactionRequest;
use App\Http\Resources\Api\BookingTransactionApiResource;

class BookingTransactionController extends Controller
{
    /**
     * Store booking transaction
     *
     * @param StoreBookingTransactionRequest $request
     * @return \Illuminate\Http\JsonResponse|BookingTransactionApiResource
     */
    public function store(StoreBookingTransactionRequest $request) {
        try {

            // Ambil data request yang sudah divalidasi
            $validatedData = $request->validated();

            // Upload file bukti pembayaran jika ada
            if($request->hasFile('proof')){
                // Simpan file ke storage/public/proofs
                $filePath = $request->file('proof')->store('proofs', 'public');
                $validatedData['proof'] = $filePath; 
            }

            // Ambil daftar produk yang dipesan (id & quantity)
            $products = $request->input('cosmetic_ids');
            $totalQuantity = 0;
            $totalPrice = 0;

            // Ambil hanya array ID kosmetik
            $cosmeticIds = array_column($products, 'id');

            // Ambil data produk dari database
            $cosmetics = Cosmetic::whereIn('id', $cosmeticIds)->get();

            // Hitung total kuantitas & harga
            foreach ($products as $product){
                $cosmetic = $cosmetics->firstWhere('id', $product['id']);
                $totalQuantity += $product['quantity'];
                $totalPrice += $cosmetic->price * $product['quantity'];
            }

            // Hitung pajak 11% dan total akhir
            $tax = 0.11 * $totalPrice;
            $grandTotal = $totalPrice + $tax;

            // Isi data transaksi
            $validatedData['total_amount'] = $grandTotal;
            $validatedData['total_tax_amount'] = $tax;
            $validatedData['sub_total_amount'] = $totalPrice;
            $validatedData['is_paid'] = false; // default belum dibayar
            $validatedData['booking_trx_id'] = BookingTransaction::generateUniqueTrxId();
            $validatedData['quantity'] = $totalQuantity;

            // Simpan transaksi utama
            $bookingTransaction = BookingTransaction::create($validatedData);

            // Simpan detail tiap produk
            foreach ($products as $product){
                $cosmetic = $cosmetics->firstWhere('id', $product['id']);
                $bookingTransaction->transactionDetails()->create([
                    'cosmetic_id' => $product['id'],
                    'quantity' => $product['quantity'],
                    'price' => $cosmetic->price,
                ]);
            }

            // Return response dengan detail transaksi
            return new BookingTransactionApiResource($bookingTransaction->load(['transactionDetails','transactionDetails.cosmetics']));

        } catch (\Exception $e) {
            // Response error jika terjadi exception
            return response()->json([
                'message' => 'An error occured',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get booking details by email & trx id
     */
    public function booking_details(Request $request){
        // Validasi input
        $request->validate([
            'email' => 'required|string',
            'booking_trx_id' => 'required|string',
        ]);

        // Cari transaksi berdasarkan email dan ID transaksi
        $booking = BookingTransaction::where('email', $request->email)
            ->where('booking_trx_id', $request->booking_trx_id)
            ->with(['transactionDetails', 'transactionDetails.cosmetics'])
            ->first();

        // Jika tidak ditemukan
        if(!$booking){
            return response()->json(['message' => 'Booking not found'], 404);
        }

        // Return detail transaksi
        return new BookingTransactionApiResource($booking);
    }
}
