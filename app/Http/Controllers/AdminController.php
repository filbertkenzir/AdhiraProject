<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // 1. Total Penjualan Hari Ini
        $totalSalesToday = Transaction::whereDate('created_at', $today)->sum('total_amount');

        // 2. Total Pengunjung / Transaksi Hari Ini
        $totalVisitorsToday = Transaction::whereDate('created_at', $today)->count();

        // 3. Metode Terbanyak Hari Ini (atau Overall jika hari ini belum ada)
        $topMethodTodayQuery = TransactionItem::select('name', DB::raw('SUM(qty) as total_qty'))
            ->whereDate('created_at', $today)
            ->groupBy('name')
            ->orderByDesc('total_qty')
            ->first();

        if (!$topMethodTodayQuery) {
            $topMethodTodayQuery = TransactionItem::select('name', DB::raw('SUM(qty) as total_qty'))
                ->groupBy('name')
                ->orderByDesc('total_qty')
                ->first();
        }

        $topMethodName = $topMethodTodayQuery ? $topMethodTodayQuery->name : 'Belum Ada Transaksi';

        // 4. Grafik Penjualan (7 Hari Terakhir)
        $chartLabels = [];
        $chartData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartLabels[] = $date->translatedFormat('d M');

            $dailyTotal = Transaction::whereDate('created_at', $date)->sum('total_amount');
            $chartData[] = (float) $dailyTotal;
        }

        // 5. Keperluan Pengunjung (Persentase Breakdown)
        $totalItemsCount = TransactionItem::sum('qty');
        $visitorNeeds = [];

        if ($totalItemsCount > 0) {
            $needsBreakdown = TransactionItem::select('name', DB::raw('SUM(qty) as item_qty'))
                ->groupBy('name')
                ->orderByDesc('item_qty')
                ->get();

            foreach ($needsBreakdown as $item) {
                $percentage = round(($item->item_qty / $totalItemsCount) * 100);
                $visitorNeeds[] = [
                    'name' => $item->name,
                    'percentage' => (int) $percentage,
                ];
            }
        } else {
            $visitorNeeds = [
                ['name' => 'Print Hitam Putih', 'percentage' => 0],
                ['name' => 'Print Berwarna', 'percentage' => 0],
                ['name' => 'Fotocopy Hitam Putih', 'percentage' => 0],
                ['name' => 'Fotocopy Berwarna', 'percentage' => 0],
            ];
        }

        return view('admin', compact(
            'totalSalesToday',
            'totalVisitorsToday',
            'topMethodName',
            'chartLabels',
            'chartData',
            'visitorNeeds'
        ));
    }
}
