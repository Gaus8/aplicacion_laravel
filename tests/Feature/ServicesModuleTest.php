<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServicesModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_service_administration(): void
    {
        $this->get(route('admin.services.index'))->assertRedirect(route('login'));
        $this->get(route('admin.services.create'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_service_list_and_form(): void
    {
        $user = User::factory()->create();
        $service = Service::create($this->modelAttributes(['created_by' => $user->id]));

        $this->actingAs($user);
        $this->get(route('admin.services.index'))->assertOk()->assertSeeText('Servicios');
        $this->get(route('admin.services.create'))->assertOk()->assertSeeText('Crear servicio');
        $this->get(route('admin.services.edit', $service))->assertOk()->assertSeeText('Editar servicio');
    }

    public function test_user_can_create_service_with_unique_slug_and_relationships(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post(route('admin.services.store'), $this->validPayload())
            ->assertRedirect(route('admin.services.index'));

        $service = Service::query()->firstOrFail();
        $this->assertSame('servicio-de-prueba', $service->slug);
        $this->assertSame($user->id, $service->creator->id);
        $this->assertSame($user->id, $service->updater->id);
        $this->assertTrue($service->active);

        $this->post(route('admin.services.store'), $this->validPayload())
            ->assertRedirect(route('admin.services.index'));
        $this->assertDatabaseHas('services', ['slug' => 'servicio-de-prueba-2']);
    }

    public function test_user_can_update_and_delete_a_service(): void
    {
        $user = User::factory()->create();
        $service = Service::create($this->modelAttributes(['created_by' => $user->id]));

        $this->actingAs($user)->put(route('admin.services.update', $service), $this->validPayload([
            'title' => 'Servicio renovado',
            'active' => '0',
        ]))->assertRedirect(route('admin.services.index'));

        $service->refresh();
        $this->assertSame('servicio-renovado', $service->slug);
        $this->assertSame($user->id, $service->updater->id);
        $this->assertFalse($service->active);

        $this->delete(route('admin.services.destroy', $service))->assertRedirect(route('admin.services.index'));
        $this->assertDatabaseMissing('services', ['id' => $service->id]);
    }

    public function test_call_to_action_fields_are_optional_but_must_be_paired_and_safe(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->from(route('admin.services.create'))
            ->post(route('admin.services.store'), $this->validPayload(['cta_label' => 'Conocer más', 'cta_url' => null]))
            ->assertRedirect(route('admin.services.create'))
            ->assertSessionHasErrors(['cta_label', 'cta_url']);

        $this->from(route('admin.services.create'))
            ->post(route('admin.services.store'), $this->validPayload(['cta_url' => 'javascript:alert(1)']))
            ->assertRedirect(route('admin.services.create'))
            ->assertSessionHasErrors('cta_url');

        $this->assertDatabaseCount('services', 0);
    }

    public function test_only_active_services_appear_on_public_homepage_in_position_order(): void
    {
        Service::create($this->modelAttributes(['title' => 'Servicio al final', 'slug' => 'servicio-final', 'position' => 5]));
        Service::create($this->modelAttributes(['title' => 'Servicio oculto', 'slug' => 'servicio-oculto', 'active' => false]));
        Service::create($this->modelAttributes(['title' => 'Servicio inicial', 'slug' => 'servicio-inicial', 'position' => 1]));

        $this->get(route('home'))
            ->assertOk()
            ->assertSeeText('Servicio inicial')
            ->assertSeeText('Servicio al final')
            ->assertDontSeeText('Servicio oculto')
            ->assertSeeInOrder(['Servicio inicial', 'Servicio al final']);
    }

    public function test_closed_service_module_is_exposed_in_the_admin_sidebar(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('admin.services.index'))
            ->assertOk()
            ->assertSee('href="'.route('admin.services.index').'"', false);
    }

    private function validPayload(array $overrides = []): array
    {
        return array_replace([
            'title' => 'Servicio de prueba',
            'summary' => 'Resumen del servicio',
            'description' => 'Descripción extensa del servicio de prueba.',
            'cta_label' => null,
            'cta_url' => null,
            'position' => 0,
            'active' => '1',
        ], $overrides);
    }

    private function modelAttributes(array $overrides = []): array
    {
        return array_replace([
            'title' => 'Servicio de prueba',
            'slug' => 'servicio-de-prueba',
            'summary' => 'Resumen del servicio',
            'description' => 'Descripción extensa del servicio de prueba.',
            'cta_label' => null,
            'cta_url' => null,
            'position' => 0,
            'active' => true,
        ], $overrides);
    }
}
