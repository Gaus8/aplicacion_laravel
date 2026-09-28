<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoSetting extends Model
{
    protected $fillable = ['site_name', 'title_template', 'default_title', 'default_description', 'canonical_base_url', 'robots_directive', 'og_image_path'];
}
