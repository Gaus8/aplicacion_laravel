<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('seo_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('site_name', 120);
            $table->string('title_template', 180)->default('%s | CMS Core');
            $table->string('default_title', 180);
            $table->string('default_description', 320);
            $table->string('canonical_base_url')->nullable();
            $table->string('robots_directive', 80)->default('index,follow');
            $table->string('og_image_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('seo_settings'); }
};
