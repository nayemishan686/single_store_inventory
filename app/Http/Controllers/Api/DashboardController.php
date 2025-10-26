<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function overview()
    {
        $today       = now()->toDateString();
        $monthStart  = now()->startOfMonth()->toDateString();

        // Totals
        $totals = [
            'products'  => Product::count(),
            'customers' => Customer::count(),
            'invoices'  => Invoice::count(),
        ];

        // Sales sums
        $sales = [
            'today'       => (float) Invoice::whereDate('created_at', $today)->sum('total'),
            'this_month'  => (float) Invoice::whereDate('created_at', '>=', $monthStart)->sum('total'),
            'lifetime'    => (float) Invoice::sum('total'),
        ];

        // Stock alerts
        $alerts = [
            'low_stock_products' => Product::where('quantity', '<', 5)->count(),
        ];

        // 🔹 Top 5 products by sold qty (from invoice_items)
        $topProducts = DB::table('invoice_items')
            ->join('products', 'products.id', '=', 'invoice_items.product_id')
            ->select('products.name', DB::raw('SUM(invoice_items.quantity) as sold_qty'))
            ->groupBy('products.name')
            ->orderByDesc('sold_qty')
            ->limit(5)
            ->get();

        // 🔹 Last 7 days sales chart data
        $chartData = Invoice::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total) as total')
        )
            ->where('created_at', '>=', now()->subDays(6))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'totals'        => $totals,
            'sales'         => $sales,
            'alerts'        => $alerts,
            'top_products'  => $topProducts,
            'chart'         => $chartData,
            'generated_at'  => now()->toDateTimeString(),
        ]);
    }
}
