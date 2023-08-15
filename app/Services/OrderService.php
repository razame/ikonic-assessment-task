<?php

namespace App\Services;

use App\Models\Merchant;
use App\Models\Order;

class OrderService
{
    public function __construct(
        protected AffiliateService $affiliateService
    ) {}

    /**
     * Process an order and log any commissions.
     * This should create a new affiliate if the customer_email is not already associated with one.
     * This method should also ignore duplicates based on order_id.
     *
     * @param  array{order_id: string, subtotal_price: float, merchant_domain: string, discount_code: string, customer_email: string, customer_name: string} $data
     * @return void
     */
    public function processOrder(array $data)
    {
        // TODO: Complete this method
        $merchant = Merchant::where('domain', $data['merchant_domain'])->orderBy('id', 'desc')->first();

        $affiliate = $this->affiliateService->register($merchant, $data['customer_email'], $data['customer_name'], $merchant->default_commission_rate);

        Order::updateOrCreate(
            [
                'external_order_id' => $data['order_id'],
            ],
            [
                'external_order_id' => $data['order_id'],
                'subtotal' => $data['subtotal_price'],
                'affiliate_id' => $affiliate->id,
                'merchant_id' => $merchant->id,
                'commission_owed' => $data['subtotal_price'] * $affiliate->commission_rate,
                'customer_email' => $data['customer_email'],
                'customer_name' => $data['customer_name'],
                'payout_status' => Order::STATUS_PAID,
                'discount_code' => $data['discount_code']
            ]
        );

    }
}
