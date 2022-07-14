<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommonTable extends Migration
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
            $table->string('order_id')->unique();
            $table->bigInteger('user_id');
            $table->string('total_amount');
            $table->bigInteger('total_quantity');
            $table->string('discount')->nullable();
            $table->string('transaction_id')->unique()->nullable();
            $table->string('payment_name')->nullable()->comment('cod, paytm,etc');
            $table->string('payment_type')->nullable()->comment('cod, onine');
            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid');
            $table->enum('order_status', ['pending', 'cancel','complete'])->default('pending');
            $table->enum('shipping_status', ['pending', 'process','complete'])->default('pending');
            $table->bigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('order_products', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id')->comment('in this filed we store only insertid so our join can work easily');
            $table->bigInteger('product_id');
            $table->string('price')->nullable();
            $table->bigInteger('quantity');
            $table->enum('is_order', ['yes', 'no'])->default('yes')->comment('yes,no, for future single product cancel');
            $table->timestamps();
        });

        Schema::create('order_shippings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id')->comment('in this filed we store only insertid so our join can work easily');
            $table->enum('address_type', ['billing', 'shipping'])->default('billing');
            $table->string('full_address')->nullable();
            $table->string('full_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->bigInteger('pincode')->nullable();
            $table->timestamps();
        });
        

        Schema::create('galleries', function (Blueprint $table) {
            $table->id();
            $table->string('image_path');
            $table->enum('image_for', ['category', 'product'])->default('product');
            $table->timestamps();
        });

        Schema::create('website_setting', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->longText('value')->nullable();
            $table->timestamps();
        });

        Schema::create('payment_setting', function (Blueprint $table) {
            $table->id();
            $table->string('payment_name');
            $table->string('payment_type');
            $table->longText('payment_credentials')->nullable();
            $table->enum('payment_mode', ['test', 'live'])->default('test');
            $table->enum('payment_status', ['0', '1'])->default('1')->comment('0-disable,1-enable');
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
        Schema::dropIfExists('order_products');
        Schema::dropIfExists('order_shippings');
        Schema::dropIfExists('galleries');
        Schema::dropIfExists('website_setting');
        Schema::dropIfExists('payment_setting');
    }
}
