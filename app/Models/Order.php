<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'fullname',
        'phone',
        'email',
        'address',
        'country',
        'province',
        'note',
        'payment_method',
        'total_price',
        'status',
        // Bổ sung các trường dữ liệu GHN
        'shipping_status',
        'ghn_order_code',
        'ghn_total_fee',
        'to_district_id',
        'to_ward_code',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Alias để khớp với các service xử lý items
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}