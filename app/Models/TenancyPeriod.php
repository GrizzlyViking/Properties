<?php

namespace App\Models;

use App\Trait\NodeTrait;
use Illuminate\Database\Eloquent\Model;

class TenancyPeriod extends Model implements NodeInterface
{
    use NodeTrait;
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'tenant_id',
        'property_id',
    ];

    public function tenants()
    {
        return $this->belongsToMany(Tenant::class)
            ->withPivot(['created_at', 'updated_at'])
            ->withTimestamps();

    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
