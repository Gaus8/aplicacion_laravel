<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VideosModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_manage_videos(): void
    {
        $this->get(route('admin.videos.index'))->assertRedirect(route('login'));
    }

    public function test_youtube_and_vimeo_are_embedded_from_validated_ids_only(): void
    {
        $this->actingAs(User::factory()->create());
        $this->post(route('admin.videos.store'), $this->payload('https://youtu.be/AbCdEfGh123'))->assertRedirect(route('admin.videos.index'));
        $this->post(route('admin.videos.store'), $this->payload('https://vimeo.com/987654321', ['title' => 'Vimeo', 'position' => 1]))->assertRedirect(route('admin.videos.index'));

        $youtube = Video::where('provider', 'youtube')->firstOrFail();
        $vimeo = Video::where('provider', 'vimeo')->firstOrFail();
        $this->assertSame('https://www.youtube-nocookie.com/embed/AbCdEfGh123', $youtube->embed_url);
        $this->assertSame('https://player.vimeo.com/video/987654321', $vimeo->embed_url);
        $this->get(route('videos.public.index'))->assertOk()->assertSee('youtube-nocookie.com/embed/AbCdEfGh123', false)->assertSee('player.vimeo.com/video/987654321', false);
    }

    public function test_nonapproved_host_or_invalid_video_id_is_rejected(): void
    {
        $this->actingAs(User::factory()->create())->from(route('admin.videos.create'))
            ->post(route('admin.videos.store'), $this->payload('https://attacker.example/embed/AbCdEfGh123'))
            ->assertRedirect(route('admin.videos.create'))->assertSessionHasErrors('video_url');

        $this->from(route('admin.videos.create'))->post(route('admin.videos.store'), $this->payload('https://youtube.com/watch?v=bad'))
            ->assertRedirect(route('admin.videos.create'))->assertSessionHasErrors('video_url');
        $this->assertDatabaseCount('videos', 0);
    }

    private function payload(string $url, array $overrides = []): array
    {
        return array_replace(['title' => 'Video de prueba', 'description' => 'Descripción del video', 'video_url' => $url, 'position' => 0, 'active' => '1'], $overrides);
    }
}
