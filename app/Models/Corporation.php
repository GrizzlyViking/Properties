<?php

namespace App\Models;

use App\Enums\Type;
use App\Trait\NodeTrait;
use Database\Factories\CorporationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Corporation extends Model implements NodeInterface
{
    /** @use HasFactory<CorporationFactory> */
    use HasFactory, NodeTrait;

    private Type $type = Type::CORPORATION;

    protected $fillable = [
        'name',
        'created_at',
        'updated_at',
    ];

    public function Buildings(): HasMany
    {
        return $this->hasMany(Building::class);
    }
}
