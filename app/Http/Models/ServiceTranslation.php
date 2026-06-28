<?php // app/Http/Models/ServiceTranslation.php
namespace App\Http\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;

class ServiceTranslation extends Model
{
    use Cachable;

    protected $fillable = [
        'service_id', 'locale', 'title', 'slug', 'icon', 'excerpt', 'content',
        'thumbnail', 'meta_description', 'meta_keywords', 'canonical_url',
        'meta_noindex', 'status', 'created_at', 'updated_at',
    ];

    protected $casts = [
        'meta_noindex' => 'boolean',
    ];
}
