@if($services->isNotEmpty())
    <section id="servicios" class="mx-auto w-full max-w-7xl px-4 py-16 sm:px-6 lg:px-8" aria-labelledby="services-heading">
        <div class="max-w-2xl">
            <h2 id="services-heading" class="font-display text-headline-xl-mobile font-bold tracking-tight text-slate-900 sm:text-headline-xl">Servicios</h2>
        </div>
        <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($services as $service)
                <x-card class="flex h-full flex-col">
                    <h3 class="font-display text-headline-md font-semibold text-slate-900">{{ $service->title }}</h3>
                    <p class="mt-3 font-medium text-slate-700">{{ $service->summary }}</p>
                    <p class="mt-3 flex-1 whitespace-pre-line text-sm leading-6 text-slate-600">{{ $service->description }}</p>
                    @if($service->cta_label && $service->cta_url)<a href="{{ $service->cta_url }}" class="mt-5 inline-flex w-fit items-center gap-2 text-sm font-semibold text-secondary hover:underline">{{ $service->cta_label }} <span aria-hidden="true">→</span></a>@endif
                </x-card>
            @endforeach
        </div>
    </section>
@endif
