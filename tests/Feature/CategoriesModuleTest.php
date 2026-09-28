<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoriesModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_category_admin(): void
    {
        $this->get(route('admin.categories.index'))->assertRedirect(route('login'));
    }

    public function test_user_can_create_edit_filter_and_delete_categories(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get(route('admin.categories.create'))->assertOk();
        $this->post(route('admin.categories.store'), ['name' => 'Actualidad CMS', 'description' => 'Noticias de actualidad', 'active' => '1'])
            ->assertRedirect(route('admin.categories.index'));
        $category = Category::query()->firstOrFail();
        $this->assertSame('actualidad-cms', $category->slug);
        $this->assertSame($user->id, $category->creator->id);

        $this->post(route('admin.categories.store'), ['name' => 'Actualidad CMS', 'active' => '1'])
            ->assertSessionHasErrors('name');
        $this->put(route('admin.categories.update', $category), ['name' => 'Actualidad y cultura', 'active' => '0'])
            ->assertRedirect(route('admin.categories.index'));
        $category->refresh();
        $this->assertSame('actualidad-y-cultura', $category->slug);
        $this->assertFalse($category->active);

        $post = Post::create(['title' => 'Noticia', 'slug' => 'noticia', 'excerpt' => 'Resumen', 'body' => 'Contenido', 'category_id' => $category->id, 'status' => 'draft']);
        $this->get(route('admin.categories.index', ['search' => 'cultura', 'status' => 'inactive']))->assertOk()->assertSeeText('Actualidad y cultura');
        $this->delete(route('admin.categories.destroy', $category))->assertRedirect(route('admin.categories.index'));
        $this->assertNull($post->fresh()->category_id);
    }
}
