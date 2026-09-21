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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('gateway'); // Phân biệt các cổng thanh toán (ví dụ: momo)[cite: 1]
            $table->string('gateway_order_id')->nullable()->index(); // ID đơn hàng từ cổng thanh toán[cite: 1]
            $table->string('transaction_id')->nullable()->index(); // ID giao dịch từ cổng thanh toán[cite: 1]
            $table->decimal('amount', 15, 2); // Số tiền thanh toán[cite: 1]
            $table->string('status')->default('pending'); // Trạng thái giao dịch[cite: 1]
            $table->integer('result_code')->nullable(); // Mã kết quả trả về[cite: 1]
            $table->string('message')->nullable(); // Thông điệp từ cổng thanh toán[cite: 1]
            $table->json('request_payload')->nullable(); // Dữ liệu yêu cầu gửi đi[cite: 1]
            $table->json('response_payload')->nullable(); // Dữ liệu phản hồi nhận về[cite: 1]
            $table->timestamp('paid_at')->nullable(); // Thời điểm thanh toán thành công[cite: 1]
            $table->timestamps();
            
            $table->unique(['gateway', 'gateway_order_id']); // Đảm bảo tính duy nhất[cite: 1]
            $table->index(['order_id', 'status']); // Chỉ mục tối ưu truy vấn[cite: 1]
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};