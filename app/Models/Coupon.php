<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'min_order_amount',
        'max_discount',
        'usage_limit',
        'used_count',
        'starts_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'coupon_users')
            ->withPivot('order_id', 'used_at')
            ->withTimestamps();
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'coupon_users')
            ->withPivot('user_id', 'used_at')
            ->withTimestamps();
    }

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeNotExpired(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    public function scopeValid(Builder $query): Builder
    {
        return $query->active()->notExpired();
    }

    // Methods
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function isStarted(): bool
    {
        return !$this->starts_at || $this->starts_at->isPast();
    }

    public function isAvailable(): bool
    {
        return $this->isActive() && 
               $this->isStarted() && 
               !$this->isExpired() && 
               !$this->isUsageLimitReached();
    }

    public function isUsageLimitReached(): bool
    {
        return $this->usage_limit && $this->used_count >= $this->usage_limit;
    }

    public function canUse(float $orderAmount, ?User $user = null): bool
    {
        if (!$this->isAvailable()) {
            return false;
        }

        if ($orderAmount < $this->min_order_amount) {
            return false;
        }

        // Check if user has already used this coupon
        if ($user && $this->users()->where('user_id', $user->id)->exists()) {
            return false;
        }

        return true;
    }

    public function calculateDiscount(float $orderAmount): float
    {
        if ($this->type === 'percentage') {
            $discount = ($orderAmount * $this->value) / 100;
            
            // Apply max discount limit if set
            if ($this->max_discount && $discount > $this->max_discount) {
                $discount = $this->max_discount;
            }
            
            return $discount;
        }

        // Fixed amount
        return min($this->value, $orderAmount);
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'percentage' => 'Phần trăm',
            'fixed' => 'Số tiền cố định',
            default => 'Không xác định'
        };
    }

    public function getStatusLabelAttribute(): string
    {
        if (!$this->is_active) {
            return 'Vô hiệu hóa';
        }

        if ($this->isExpired()) {
            return 'Hết hạn';
        }

        if (!$this->isStarted()) {
            return 'Chưa bắt đầu';
        }

        if ($this->isUsageLimitReached()) {
            return 'Hết lượt sử dụng';
        }

        return 'Hoạt động';
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status_label) {
            'Hoạt động' => 'green',
            'Vô hiệu hóa' => 'red',
            'Hết hạn' => 'red',
            'Chưa bắt đầu' => 'yellow',
            'Hết lượt sử dụng' => 'orange',
            default => 'gray'
        };
    }
}
