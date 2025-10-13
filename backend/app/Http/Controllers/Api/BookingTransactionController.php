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
    //
    public function store(StoreBookingTransactionRequest $request) {
        try {

            // Validate request data
            $validatedData = $request->validated();

            // Handle file upload
            if($request->hasFile('proof')){
                $filePath = $request->file('proof')->store('proofs ', 'public');
                $validatedData['proof'] = $filePath; 
            }

            // Retrieve products and calculate total quantities and prices
            $products = $request->input('cosmetic_ids');
            $totalQuantity = 0;
            $totalPrice = 0;

            $cosmeticIds = array_column($products, 'id');
            $cosmetics = Cosmetic::whereIn('id', $cosmeticIds)->get();

            foreach ($products as $product){
                $cosmetic = $cosmetics->firstWhere('id', $product['id']);
                $totalQuantity += $product['quantity'];
                $totalPrice += $cosmetic->price * $product['quantity'];
            }

            $tax = 0.11 * $totalPrice;
            $grandTotal = $totalPrice + $tax;

            // Populate booking transaction data
            $validatedData['total_amount'] = $grandTotal;
            $validatedData['total_tax_amount'] = $tax;
            $validatedData['sub_total_amount'] = $totalPrice;
            $validatedData['is_paid'] = false;
            $validatedData['booking_trx_id'] = BookingTransaction::generateUniqueTrxId();

            // Save total quantity in booking transactions
            $validatedData['quantity'] = $totalQuantity;

            $bookingTransaction = BookingTransaction::create($validatedData);

            // Create transaction details for each product
            foreach ($products as $product){
                $cosmetic = $cosmetics->firstWhere('id', $product['id']);
                $bookingTransaction->transactionDetails()->create([
                    'cosmetic_id' => $product['id'],
                    'quantity' => $product['quantity'],
                    'price' => $cosmetic->price,
                ]);
            }

            return new BookingTransactionApiResource($bookingTransaction->load('transactionDetails'));

        } catch (\Exception $e){
            return response()->json(['message' => 'An error occured', 'error' => $e->getMessage()], 500);
        }
    }
}
