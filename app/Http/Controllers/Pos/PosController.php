<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Services\ActivityLogger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $categoryId = $request->get('category', '');

        $categories = Category::where('is_active', true)->orderBy('name')->get();

        $products = Product::with(['variants' => fn ($q) => $q->where('is_active', true)->where('stock', '>', 0)])
            ->where('is_active', true)
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->when($search, function ($q) use ($search) {
                $keywords = explode(' ', $search);
                $q->where(function ($subQ) use ($keywords, $search) {
                    $subQ->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('sku', 'LIKE', "%{$search}%");

                    foreach ($keywords as $keyword) {
                        $keyword = trim($keyword);
                        if (! empty($keyword)) {
                            $subQ->orWhereHas('variants', fn ($vq) => $vq->where('sku', 'LIKE', "%{$keyword}%"));
                        }
                    }
                });
            })
            ->orderBy('name', 'desc')
            ->get();

        $productsData = $products->map(fn ($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'sku' => $p->sku,
            'price' => $p->base_price,
            'category_id' => $p->category_id,
            'stock' => $p->variants->sum('stock'),
            'image' => $p->image ? asset('storage/'.$p->image) : null,
        ])->toArray();

        return view('pos.index', compact('productsData', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'discount' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'transaction_time' => 'nullable|date',
        ]);

        return DB::transaction(function () use ($validated) {
            $subtotal = 0;
            $transactionTime = $validated['transaction_time']
                ? Carbon::parse($validated['transaction_time'])
                : now();

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['id']);
                $variant = $product->variants->first();

                if (! $variant || $variant->stock < $item['qty']) {
                    throw new \Exception("Stok tidak cukup untuk produk {$product->name}");
                }

                $itemTotal = $product->base_price * $item['qty'];
                $subtotal += $itemTotal;
                $variant->decrement('stock', $item['qty']);
            }

            $total = $subtotal - ($validated['discount'] ?? 0);

            ActivityLogger::pos(auth()->user()->name.' melakukan transaksi POS sebesar Rp '.number_format($total), [
                'total' => $total,
                'items_count' => count($validated['items']),
                'transaction_time' => $transactionTime->format('Y-m-d H:i:s'),
            ]);

            return response()->json([
                'success' => true,
                'transaction_id' => 'TRX-'.time(),
                'transaction_time' => $transactionTime->format('d M Y H:i:s'),
            ]);
        });
    }
}
