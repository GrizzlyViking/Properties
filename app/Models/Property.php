<?php

namespace App\Models;

use App\Enums\Type;
use App\Trait\NodeTrait;
use Database\Factories\PropertyFactory;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model implements NodeInterface
{
    /** @use HasFactory<PropertyFactory> */
    use HasFactory, NodeTrait;

    private Type $type = Type::PROPERTY;

    protected $fillable = [
        'name',
        'monthly_rent',
    ];

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function tenancyPeriods(): HasMany
    {
        return $this->hasMany(TenancyPeriod::class);
    }

    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(
            Tenant::class,
            'tenancy_periods',
            'property_id',
            'tenant_id'
        )->withPivot(['start_date', 'end_date']);
    }

    public function isActive(?DateTime $date = null): bool
    {
        $date = $date ?? new DateTime();

        return $this->tenancyPeriods()
            ->where('start_date', '<=', $date)
            ->where(function ($query) use ($date) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', $date);
            })
            ->exists();
    }
}
