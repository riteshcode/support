<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->integer('categories_id', true);
            $table->text('categories_image', 65535)->nullable();
            $table->text('categories_icon', 65535)->nullable();
            $table->integer('business_id')->unsigned();
            $table->foreign('business_id')->references('business_id')->on('business')->onDelete('cascade');
            $table->integer('parent_id')->default(0)->index('idx_categories_parent_id');
            $table->integer('sort_order')->nullable();
            $table->integer('created_by')->unsigned();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->dateTime('date_added')->nullable();
            $table->dateTime('last_modified')->nullable();
            $table->string('categories_slug', 191);
            $table->boolean('categories_status')->default(1);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('categories_details', function (Blueprint $table) {
            $table->integer('categories_description_id', true);
            $table->unsignedBigInteger('categories_id')->default(0);
            $table->unsignedBigInteger('language_id')->nullable();
            $table->string('category_name')->nullable();
            $table->longText('description')->nullable();


            $table->foreign('category_id')->references('categories_id')->on('categories');
            $table->foreign('language_id')->references('id')->on('languages');

        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
}

