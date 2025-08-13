<?php

namespace App\Models;

use App\Enums\Type;
use App\Trait\NodeTrait;
use Database\Factories\TenantFactory;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model implements NodeInterface
{
    /** @use HasFactory<TenantFactory> */
    use HasFactory, NodeTrait, SoftDeletes;

    private Type $type = Type::TENANT;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'comments',
    ];

    public function tenancyPeriods(): HasMany
    {
        return $this->hasMany(TenancyPeriod::class);
    }

    public function properties(): BelongsToMany
    {
        return $this->belongsToMany(
            Property::class,
            'tenancy_periods',
            'tenant_id',
            'property_id'
        )->withPivot(['start_date', 'end_date']);
    }
}
