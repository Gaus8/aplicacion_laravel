<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\IndexTestimonialRequest;
use App\Http\Requests\Admin\StoreTestimonialRequest;
use App\Http\Requests\Admin\UpdateTestimonialRequest;
use App\Models\Testimonial;
use Illuminate\Support\Facades\Gate;

class TestimonialController extends Controller
{
    public function index(IndexTestimonialRequest $request)
    {
        $filters = $request->validated();
        $testimonials = Testimonial::query()
            ->when($filters['search'] ?? null, fn ($query, $search) => $query->where('person_name', 'like', '%'.trim($search).'%'))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('active', $status === 'active'))
            ->orderBy('position')->latest('id')->paginate(15)->withQueryString();

        return view('admin.testimonials.index', compact('testimonials', 'filters'));
    }

    public function create()
    {
        Gate::authorize('create', Testimonial::class);

        return view('admin.testimonials.form', ['testimonial' => new Testimonial(), 'isEditing' => false]);
    }

    public function store(StoreTestimonialRequest $request)
    {
        $data = $request->validated();
        $testimonial = new Testimonial();
        $testimonial->fill($this->attributes($data));
        $testimonial->created_by = $request->user()->getKey();
        $testimonial->updated_by = $request->user()->getKey();
        $testimonial->save();

        return redirect()->route('admin.testimonials.index')->with('success', 'Testimonio guardado correctamente.');
    }

    public function edit(Testimonial $testimonial)
    {
        Gate::authorize('update', $testimonial);

        return view('admin.testimonials.form', ['testimonial' => $testimonial, 'isEditing' => true]);
    }

    public function update(UpdateTestimonialRequest $request, Testimonial $testimonial)
    {
        $testimonial->fill($this->attributes($request->validated()));
        $testimonial->updated_by = $request->user()->getKey();
        $testimonial->save();

        return redirect()->route('admin.testimonials.index')->with('success', 'Testimonio actualizado correctamente.');
    }

    public function destroy(Testimonial $testimonial)
    {
        Gate::authorize('delete', $testimonial);
        $testimonial->delete();

        return redirect()->route('admin.testimonials.index')->with('success', 'Testimonio eliminado correctamente.');
    }

    private function attributes(array $data): array
    {
        return [
            'person_name' => trim($data['person_name']), 'role' => isset($data['role']) ? trim($data['role']) : null,
            'organization' => isset($data['organization']) ? trim($data['organization']) : null,
            'quote' => trim($data['quote']), 'position' => $data['position'], 'active' => (bool) ($data['active'] ?? false),
        ];
    }
}
