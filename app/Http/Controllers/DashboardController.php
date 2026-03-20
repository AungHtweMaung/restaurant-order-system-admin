<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use IcehouseVentures\LaravelChartjs\Facades\Chartjs;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();

        $startOfToday = $now->copy()->startOfDay();
        $endOfToday   = $now->copy()->endOfDay();

        $startOfCurrentWeek = $now->copy()->startOfWeek(Carbon::MONDAY);
        $endOfCurrentWeek   = $now->copy()->endOfWeek(Carbon::SUNDAY);

        $orderItems = OrderItem::select(
                'menus.id as menu_id',
                'menus.eng_name',
                'menus.mm_name',
                DB::raw('SUM(order_items.quantity) as total_sold_quantity')
            )
            ->join('menus', 'menus.id', '=', 'order_items.menu_id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', 'completed')
            ->whereNotNull('orders.payment_verified_at')
            ->whereBetween('orders.created_at', [$startOfToday, $endOfToday])
            ->groupBy('menus.id', 'menus.eng_name', 'menus.mm_name')
            ->orderByDesc('total_sold_quantity')
            ->limit(10)
            ->get();

        // Default = today
        $todayOrderItemCount = OrderItem::whereHas('order', function ($query) use ($now) {
                $query->whereDate('created_at', $now->toDateString())
                    ->where('status', 'completed')
                    ->whereNotNull('payment_verified_at');
            })
            ->sum('quantity');

        $todayTotalRevenue = Order::whereDate('created_at', $now->toDateString())
            ->where('status', 'completed')
            ->whereNotNull('payment_verified_at')
            ->sum('total_price');

        $todayDineInCount = Order::whereDate('created_at', $now->toDateString())
            ->where('status', 'completed')
            ->whereNotNull('payment_verified_at')
            ->where('order_type', 'dine_in')
            ->count();

        $todayTakeawayCount = Order::whereDate('created_at', $now->toDateString())
            ->where('status', 'completed')
            ->whereNotNull('payment_verified_at')
            ->where('order_type', 'take_away')
            ->count();

        $dailyOrderCounts = Order::whereBetween('created_at', [$startOfCurrentWeek, $endOfCurrentWeek])
            ->where('status', 'completed')
            ->whereNotNull('payment_verified_at')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date')
            ->toArray();

        $labels = [];
        $data   = [];

        for ($i = 0; $i < 7; $i++) {
            $date = $startOfCurrentWeek->copy()->addDays($i);
            $labels[] = $date->format('l') . "\n" . $date->format('M d');
            $data[]   = $dailyOrderCounts[$date->toDateString()] ?? 0;
        }

        $chart = Chartjs::build()
            ->name('weeklyOrders')
            ->type('bar')
            ->size(['width' => 400, 'height' => 200])
            ->labels($labels)
            ->datasets([
                [
                    'label' => 'Completed Orders',
                    'data' => $data,
                    'backgroundColor' => 'rgba(255, 206, 86, 0.6)',
                    'borderColor' => 'rgb(141, 136, 136)',
                    'borderWidth' => 1,
                ]
            ])
            ->options([
                'responsive' => true,
                'maintainAspectRatio' => false,
                'scales' => [
                    'y' => [
                        'beginAtZero' => true,
                        'ticks' => [
                            'precision' => 0,
                            'stepSize' => 1,
                        ],
                    ],
                ],
            ]);

        return view('dashboard.index', compact(
            'orderItems',
            'todayOrderItemCount',
            'todayTotalRevenue',
            'todayDineInCount',
            'todayTakeawayCount',
            'chart'
        ));
    }

    public function getData($period, Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $orderItems = $this->getOrderItemsByPeriod($period, $startDate, $endDate);

        return response()->json([
            'orderItems' => $orderItems
        ]);
    }

    public function getSummaryData($period, Request $request)
    {
        if (!empty($request->start_date) && !empty($request->end_date)) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate   = Carbon::parse($request->end_date)->endOfDay();
        } else {
            [$startDate, $endDate] = $this->getDateRangeByPeriod($period);
        }

        $orderItemCount = OrderItem::whereHas('order', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate])
                    ->where('status', 'completed')
                    ->whereNotNull('payment_verified_at');
            })
            ->sum('quantity');

        $totalRevenue = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->whereNotNull('payment_verified_at')
            ->sum('total_price');

        $dineInCount = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->whereNotNull('payment_verified_at')
            ->where('order_type', 'dine_in')
            ->count();

        $takeawayCount = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->whereNotNull('payment_verified_at')
            ->where('order_type', 'take_away')
            ->count();

        return response()->json([
            'orderItemCount' => $orderItemCount,
            'totalRevenue'   => $totalRevenue,
            'orderTypes'     => [
                'DineIn'   => $dineInCount,
                'Takeaway' => $takeawayCount,
            ],
        ]);
    }

    private function getOrderItemsByPeriod($period, $startDate = null, $endDate = null)
    {
        if (!empty($startDate) && !empty($endDate)) {
            $startDate = Carbon::parse($startDate)->startOfDay();
            $endDate = Carbon::parse($endDate)->endOfDay();
        } else {
            [$startDate, $endDate] = $this->getDateRangeByPeriod($period);
        }

        return OrderItem::select(
                'menus.id as menu_id',
                'menus.eng_name',
                'menus.mm_name',
                DB::raw('SUM(order_items.quantity) as total_sold_quantity')
            )
            ->join('menus', 'menus.id', '=', 'order_items.menu_id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', 'completed')
            ->whereNotNull('orders.payment_verified_at')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('menus.id', 'menus.eng_name', 'menus.mm_name')
            ->orderByDesc('total_sold_quantity')
            ->limit(10)
            ->get();
    }

    private function getDateRangeByPeriod($period, $customStart = null, $customEnd = null)
    {
        $now = Carbon::now();

        if ($customStart && $customEnd) {
            return [
                Carbon::parse($customStart)->startOfDay(),
                Carbon::parse($customEnd)->endOfDay()
            ];
        }

        if ($period === 'today') {
            $startDate = $now->copy()->startOfDay();
            $endDate   = $now->copy()->endOfDay();
        } elseif ($period === 'weekly') {
            $startDate = $now->copy()->startOfWeek(Carbon::MONDAY);
            $endDate   = $now->copy()->endOfWeek(Carbon::SUNDAY);
        } elseif ($period === 'monthly') {
            $startDate = $now->copy()->startOfMonth();
            $endDate   = $now->copy()->endOfMonth();
        } else {
            $startDate = $now->copy()->startOfDay();
            $endDate   = $now->copy()->endOfDay();
        }

        return [$startDate, $endDate];
    }

    public function getChartData($period, Request $request)
    {
        [$startDate, $endDate] = $this->getDateRangeByPeriod($period, $request->start_date, $request->end_date);

        $dailyOrderCounts = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->whereNotNull('payment_verified_at')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date')
            ->toArray();

        $dates = collect(range(0, $endDate->diffInDays($startDate)))
            ->map(function ($i) use ($startDate) {
                return $startDate->copy()->addDays($i);
            });

        $labels = $dates->map(function ($date) {
            return $date->format('l') . "\n" . $date->format('M d');
        })->toArray();

        $data = [];

        foreach ($dates as $date) {
            $data[] = $dailyOrderCounts[$date->toDateString()] ?? 0;
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'period' => $period
        ]);
    }
}
