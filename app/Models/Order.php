<?php

namespace App\Models;

use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $order_number
 * @property string $status
 * @property int $subtotal
 * @property int $discount
 * @property int $shipping_cost
 * @property int $total
 * @property string|null $payment_method
 * @property array<string, mixed>|null $shipping_address
 * @property string|null $notes
 * @property Carbon|null $paid_at
 */
#[Fillable([
    'user_id',
    'order_number',
    'status',
    'subtotal',
    'discount',
    'shipping_cost',
    'total',
    'payment_method',
    'shipping_address',
    'notes',
    'paid_at',
])]
class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_PAID = 'paid';

    public const STATUS_SHIPPED = 'shipped';

    public const STATUS_DELIVERED = 'delivered';

    public const STATUS_CANCELLED = 'cancelled';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'subtotal' => 'integer',
            'discount' => 'integer',
            'shipping_cost' => 'integer',
            'total' => 'integer',
            'shipping_address' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    /**
     * The customer who placed this order.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Line items on this order.
     *
     * @return HasMany<OrderItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Products on this order (via order_items pivot).
     *
     * @return BelongsToMany<Product, $this, Pivot>
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'order_items')
            ->withPivot(['product_variant_id', 'product_name', 'variant_name', 'quantity', 'unit_price', 'total_price'])
            ->withTimestamps();
    }
}
