<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->string('product_slug')->unique();
            $table->string('product_category')->nullable();
            $table->longText('product_short_description')->nullable();
            $table->longText('product_description')->nullable();
            $table->string('product_image')->nullable();
            $table->longText('product_image_gallery')->nullable();
            $table->string('product_sku')->unique();
            $table->enum('product_price_type', ['single', 'bulk'])->default('single');
            $table->decimal('product_price', $precision = 10, $scale = 2)->nullable();
            $table->decimal('product_offer_price', $precision = 10, $scale = 2)->nullable();
            
            for ($i=1; $i <=6 ; $i++) { 
                $product_qty = 'product_qty_'.$i;
                $product_price = 'product_price_'.$i;
                $table->bigInteger($product_qty)->nullable();    
                $table->decimal($product_price, $precision = 10, $scale = 2)->nullable();
            }
            
            $table->decimal('product_weight', $precision = 10, $scale = 1)->default(0);
            $table->bigInteger('product_width')->default(0);
            $table->bigInteger('product_length')->default(0);
            $table->bigInteger('product_height')->default(0);
            $table->bigInteger('product_girth')->default(0);
            
            $table->bigInteger('flash_deals')->default(0);
            $table->string('flash_deal_start_date')->nullable();
            $table->string('flash_deal_end_date')->nullable();
            $table->bigInteger('product_stock')->default(0);

            $table->string('product_meta_tag')->nullable();
            $table->string('product_meta_title')->nullable();
            $table->string('product_meta_keyword')->nullable();
            $table->string('product_meta_desc')->nullable();
            
            $table->enum('product_status', ['1', '0'])->default('1')->comment('0-deactive, 1-active');
            $table->bigInteger('user_id')->nullable()->comment('vendor id, currently userid is vendor..');
            $table->bigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('product_attributes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('attribute_id')->default(0);
            $table->bigInteger('product_id')->default(0);
            $table->bigInteger('language_id')->default(1);
            $table->string('attribute_value');
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('category_name');
            $table->string('category_slug')->unique();
            $table->string('category_image')->nullable();
            $table->bigInteger('category_parent_id')->default(0)->nullable();
            $table->string('category_meta_title')->nullable();
            $table->text('category_meta_desc')->nullable();
            $table->bigInteger('category_status')->default(1);
            $table->bigInteger('category_sort_order')->default(0)->nullable();
            $table->bigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->string('attribute_name')->comment('Storage,RAM,ROM,etc..');
            $table->bigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('categorie_attributes', function (Blueprint $table) {
            $table->bigInteger('categorie_id');
            $table->bigInteger('attribute_id');
        });

        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('brand_name');
            $table->string('brand_slug')->unique();
            $table->string('brand_image')->nullable();
            $table->bigInteger('created_by')->nullable();
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
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_attributes');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('attributes');
        Schema::dropIfExists('categorie_attributes');
        Schema::dropIfExists('brands');

    }
}
