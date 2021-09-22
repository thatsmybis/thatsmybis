<?php

namespace App;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany, HasMany};

class BaseModel extends Model
{
    // TODO: Enable when caching is ready for production.
    //       - Need to get it working with CharacterItem and other stuff.
    //       - Need to make sure it works between guilds.
    // use Cachable;

    protected $cachePrefix = '';
}
