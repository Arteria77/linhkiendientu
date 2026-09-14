<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            
            // 1. Mã & Định danh
            $table->string('code')->unique()->nullable();          // Mã linh kiện / SKU
            $table->string('name');                                // Tên linh kiện đầy đủ
            
            // 2. Phân loại & Thương hiệu
            $table->string('category_type')->nullable();           // Chủng loại (CPU, RAM, Mainboard, VGA, SSD, PSU...)
            $table->string('brand')->nullable();                   // Thương hiệu (Intel, AMD, Asus, MSI, Gigabyte, Kingston...)
            $table->string('origin')->nullable();                  // Xuất xứ (Chính hãng, Nhập khẩu...)
            
            // 3. Thông số kỹ thuật chi tiết
            $table->string('socket_type')->nullable();             // Chipset/Socket (LGA1700, AM5, DDR4, DDR5, PCIe 4.0...)
            $table->text('specifications');                        // Thông số kỹ thuật tóm tắt (dạng ngắn)
            $table->longText('description')->nullable();           // Mô tả chi tiết / Bài viết đánh giá (HTML)

            // 4. Giá bán & Kho hàng
            $table->decimal('price', 12, 2);                       // Giá bán chính thức
            $table->integer('stock')->default(0);                  // Số lượng tồn kho
            $table->integer('warranty_months')->default(12);       // Thời hạn bảo hành (Tháng)
            $table->string('condition')->default('Mới 100%');      // Tình trạng (Mới 100% Fullbox, Trôi bảo hành...)

            // 5. Hình ảnh
            $table->string('image')->nullable();                   // Ảnh đại diện
            $table->json('gallery')->nullable();                   // Mảng album ảnh chi tiết

            // 6. Trạng thái & Thống kê
            $table->boolean('is_active')->default(true);           // Ẩn / Hiện sản phẩm
            $table->boolean('is_featured')->default(false);        // Sản phẩm HOT / Nổi bật
            $table->integer('view_count')->default(0);             // Lượt xem
            $table->integer('sold_count')->default(0);             // Lượt đã bán
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};