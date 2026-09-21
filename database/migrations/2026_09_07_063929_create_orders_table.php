<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('fullname');
            $table->string('phone', 10);
            $table->string('email');
            $table->text('address');
            $table->string('country')->default('Việt Nam');
            $table->string('province');
            $table->string('district')->nullable(); // Thêm cột lưu Tên Quận/Huyện
            $table->string('ward')->nullable();     // Thêm cột lưu Tên Phường/Xã
            $table->text('note')->nullable();
            $table->string('payment_method'); // cod, banking
            $table->decimal('total_price', 15, 2);
            $table->string('status')->default('pending'); // pending, processing, completed, cancelled

            // Bổ sung các trường GHN
            $table->string('shipping_status')->default('not_shipped');
            $table->string('ghn_order_code')->nullable()->index();
            $table->integer('ghn_total_fee')->default(0);
            $table->integer('to_district_id')->nullable();
            $table->string('to_ward_code')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};