<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_variant_id',
        'product_name',
        'variant_details',
        'quantity',
        'unit_price',
        'subtotal',
    ];

    protected $casts = [
        'variant_details' => 'array',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function getVariantDetailsStringAttribute(): string
    {
        $details = [];
        
        if (!empty($this->variant_details['size'])) {
            $details[] = 'Size: ' . $this->variant_details['size'];
        }
        
        if (!empty($this->variant_details['color'])) {
            $details[] = 'Màu: ' . $this->variant_details['color'];
        }
        
        return implode(', ', $details);
    }
}