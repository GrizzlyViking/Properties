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

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
