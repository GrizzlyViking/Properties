<?php

namespace App\Models;

use App\Enums\Type;
use App\Trait\NodeTrait;
use Database\Factories\BuildingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Building extends Model implements NodeInterface
{
    /** @use HasFactory<BuildingFactory> */
    use HasFactory, NodeTrait;

    private Type $type = Type::BUILDING;

    protected $fillable = [
        'name',
        'corporation_id',
        'address',
        'city',
        'zip_code',
    ];

    public function corporation(): BelongsTo
    {
        return $this->belongsTo(Corporation::class);
    }

    public function properties()
    {
        return $this->hasMany(Property::class);
    }
}
