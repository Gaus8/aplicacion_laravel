<?php

namespace Tests\Feature;

use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestimonialsModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_manage_testimonials(): void
    {
        $this->get(route('admin.testimonials.index'))->assertRedirect(route('login'));
    }

    public function test_testimonial_crud_and_active_public_display(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post(route('admin.testimonials.store'), [
            'person_name' => 'María López', 'role' => 'Directora', 'organization' => 'Organización',
            'quote' => 'El servicio ha sido excelente y confiable.', 'position' => 0, 'active' => '1',
        ])->assertRedirect(route('admin.testimonials.index'));
        $testimonial = Testimonial::query()->firstOrFail();
        $this->assertSame($user->id, $testimonial->creator->id);
        $this->get(route('home'))->assertSeeText('El servicio ha sido excelente y confiable.')->assertSeeText('María López');

        $this->put(route('admin.testimonials.update', $testimonial), [
            'person_name' => 'María López', 'quote' => 'Opinión actualizada y validada.', 'position' => 0, 'active' => '0',
        ])->assertRedirect(route('admin.testimonials.index'));
        $this->get(route('home'))->assertDontSeeText('Opinión actualizada y validada.');

        $this->delete(route('admin.testimonials.destroy', $testimonial))->assertRedirect(route('admin.testimonials.index'));
        $this->assertDatabaseMissing('testimonials', ['id' => $testimonial->id]);
    }
}
