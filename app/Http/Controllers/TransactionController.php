<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\Http;

class TransactionController extends Controller
{
    public function store(Request $request)
{
    $validated = $request->validate([
        'customer_name' => 'required|string',
        'customer_phone' => 'required|string',
        'amount_paid' => 'required|integer',
        'items' => 'required|array',
        'items.*.product_id' => 'required|integer',
        'items.*.quantity' => 'required|integer|min:1',
    ]);

    $total = 0;
    $itemsData = [];

    foreach ($validated['items'] as $item) {
        // Ambil data produk dari product-service
        $productResponse = Http::get("http://127.0.0.1:8001/api/products/" . $item['product_id']);
        if (!$productResponse->successful()) {
            return response()->json(['error' => 'Produk tidak ditemukan'], 404);
        }

        $product = $productResponse->json();

        if ($product['stock'] < $item['quantity']) {
            return response()->json(['error' => "Stok produk {$product['name']} tidak cukup"], 400);
        }

        $subtotal = $product['price'] * $item['quantity'];
        $total += $subtotal;

        $itemsData[] = [
            'product_id' => $product['id'],
            'product_name' => $product['name'],
            'quantity' => $item['quantity'],
            'subtotal' => $subtotal
        ];
    }

    if ($validated['amount_paid'] < $total) {
        return response()->json(['error' => 'Uang tidak cukup'], 400);
    }

    
    $transaction = Transaction::create([
        'customer_name' => $validated['customer_name'],
        'customer_phone' => $validated['customer_phone'],
        'total_price' => $total,
        'amount_paid' => $validated['amount_paid'],
        'change' => $validated['amount_paid'] - $total,
    ]);


    foreach ($itemsData as $itemData) {
        $itemData['transaction_id'] = $transaction->id;
        TransactionItem::create($itemData);

        // Kurangi stok di product-service
        Http::put("http://127.0.0.1:8001/api/products/{$itemData['product_id']}/decrease-stock", [
        'quantity' => $itemData['quantity']
    ]);

    }

    return response()->json([
        'message' => 'Transaksi berhasil',
        'data' => $transaction->load('items')
    ]);
}


    public function index()
    {
    return Transaction::with('items')->orderBy('id', 'asc')->get(); 

    }

    public function show($id)
    {
        return Transaction::with('items')->findOrFail($id);
    }

}

