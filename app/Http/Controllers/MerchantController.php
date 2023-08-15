<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\MerchantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class MerchantController extends Controller
{
    public function __construct(
        MerchantService $merchantService
    ) {}

    /**
     * Useful order statistics for the merchant API.
     *
     * @param Request $request Will include a from and to date
     * @return JsonResponse Should be in the form {count: total number of orders in range, commission_owed: amount of unpaid commissions for orders with an affiliate, revenue: sum order subtotals}
     */
    public function orderStats(Request $request): JsonResponse
    {
        // TODO: Complete this method

        $from = Carbon::parse($request->get('from'));
        $to = Carbon::parse($request->get('to'));

        $count = Order::whereBetween('created_at', [$from, $to])->count();
        $revenue = Order::whereBetween('created_at', [$from, $to])->sum('subtotal');
        $total_commissions = Order::whereBetween('created_at', [$from, $to])
            ->where('payout_status', Order::STATUS_UNPAID)
            ->get()->sum('commission_owed');
        $commission_owed_by_none = Order::whereBetween('created_at', [$from, $to])
            ->whereNull('affiliate_id')
            ->where('payout_status', Order::STATUS_UNPAID)
            ->get()->sum('commission_owed');
        return response()->json(["count" => $count, "revenue" => $revenue, "commissions_owed" => $total_commissions - $commission_owed_by_none]);
    }
}
