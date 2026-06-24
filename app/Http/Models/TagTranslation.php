<?php

namespace App\Http\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;

class TagTranslation extends Model
{
    use Cachable;

    public $timestamps = false;

    protected $fillable = [
        'tag_id',
        'locale',
        'name',
        'slug',
    ];
}
