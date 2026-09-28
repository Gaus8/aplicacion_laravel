<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostsModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_post_admin_and_edit_form_uses_categories(): void
    {
        $this->get(route('admin.posts.index'))->assertRedirect(route('login'));
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Noticias', 'slug' => 'noticias', 'active' => true]);
        $post = Post::create($this->modelAttributes($category->id));
        $this->actingAs($user)->get(route('admin.posts.index'))->assertOk()->assertSeeText('Prueba editorial');
        $this->get(route('admin.posts.edit', $post))->assertOk();
    }

    public function test_editorial_states_control_publication_and_public_slug(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Noticias', 'slug' => 'noticias', 'active' => true]);
        $this->actingAs($user)->post(route('admin.posts.store'), [
            'title' => 'Noticia en borrador', 'excerpt' => 'Resumen de la noticia', 'body' => 'Contenido editorial de la noticia.',
            'category_id' => $category->id, 'status' => 'draft', 'cover' => UploadedFile::fake()->createWithContent('cover.png', $this->pngFixture()), 'cover_alt' => 'Portada',
        ])->assertRedirect(route('admin.posts.index'));
        $draft = Post::query()->firstOrFail();
        $this->assertSame('noticia-en-borrador', $draft->slug);
        $this->assertSame($user->id, $draft->author->id);
        Storage::disk('public')->assertExists($draft->cover_path);
        $this->get(route('posts.public.index'))->assertDontSeeText('Noticia en borrador');
        $this->get(route('posts.public.show', ['post' => $draft->slug]))->assertNotFound();

        $this->put(route('admin.posts.update', $draft), [
            'title' => 'Noticia publicada', 'excerpt' => 'Resumen visible', 'body' => 'Contenido editorial publicado.',
            'category_id' => $category->id, 'status' => 'published',
        ])->assertRedirect(route('admin.posts.index'));
        $draft->refresh();
        $this->assertNotNull($draft->published_at);
        $this->get(route('posts.public.index'))->assertSeeText('Noticia publicada');
        $this->get(route('posts.public.show', ['post' => $draft->slug]))->assertOk()->assertSeeText('Contenido editorial publicado.');
    }

    public function test_invalid_cover_upload_is_rejected(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Noticias', 'slug' => 'noticias', 'active' => true]);

        $this->actingAs($user)->from(route('admin.posts.create'))->post(route('admin.posts.store'), [
            'title' => 'Noticia inválida', 'excerpt' => 'Resumen', 'body' => 'Contenido editorial.',
            'category_id' => $category->id, 'status' => 'draft', 'cover' => UploadedFile::fake()->create('fake.png', 10, 'text/plain'),
        ])->assertRedirect(route('admin.posts.create'))->assertSessionHasErrors('cover');
        $this->assertDatabaseCount('posts', 0);
    }

    private function modelAttributes(int $categoryId): array
    {
        return ['title' => 'Prueba editorial', 'slug' => 'prueba-editorial', 'excerpt' => 'Resumen editorial', 'body' => 'Texto del artículo', 'category_id' => $categoryId, 'status' => 'draft'];
    }

    private function pngFixture(): string
    {
        return base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAADUlEQVR4nGP4z8AAAAMBAQDJ/pLvAAAAAElFTkSuQmCC');
    }
}
