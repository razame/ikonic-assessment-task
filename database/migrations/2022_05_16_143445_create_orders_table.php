<?php

use App\Models\Order;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use \Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained();
            $table->foreignId('affiliate_id')->nullable()->constrained();
            // TODO: Replace floats with the correct data types (very similar to affiliates table)
            $table->decimal('subtotal', 10)->default(0.00);
            $table->decimal('commission_owed',5)->default(0.00);
            $table->string('payout_status')->default(Order::STATUS_UNPAID);
            $table->string('customer_email')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('discount_code')->default(Str::uuid());
            $table->string('external_order_id')->default(Str::uuid());
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
