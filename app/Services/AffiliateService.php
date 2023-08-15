<?php

namespace App\Services;

use App\Exceptions\AffiliateCreateException;
use App\Mail\AffiliateCreated;
use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Mail;

class AffiliateService
{
    public function __construct(
        protected ApiService $apiService
    ) {}

    /**
     * Create a new affiliate for the merchant with the given commission rate.
     *
     * @param  Merchant $merchant
     * @param  string $email
     * @param  string $name
     * @param  float $commissionRate
     * @return Affiliate
     */
    public function register(Merchant $merchant, string $email, string $name, float $commissionRate): Affiliate
    {
        // TODO: Complete this method
        $merchantUser = User::where('email', $email)->first();

        if ( $merchantUser ) {
            throw new AffiliateCreateException();
        }

        $user = User::updateOrCreate(
            [
                'email' => $email
            ],
            [
                'name'  => $name,
                'type'  => User::TYPE_AFFILIATE
            ]
        );

        if(!$user)
            throw new ModelNotFoundException();

        $discountCode = $this->apiService->createDiscountCode($merchant)['code'];

        $affiliate = $user->affiliate()->updateOrCreate(
            [
                'discount_code' => $discountCode,
                'merchant_id', $merchant->id
            ],
            [
                'merchant_id'  => $merchant->id,
                'commission_rate' => $commissionRate,
                'discount_code' => $discountCode
            ]
        );

        if(!$affiliate){
            throw new ModelNotFoundException();
        }

        Mail::to($user->email)->send(new AffiliateCreated($affiliate));

        return $affiliate;
    }
}
