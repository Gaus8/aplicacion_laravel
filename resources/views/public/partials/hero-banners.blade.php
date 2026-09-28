@if($banners->isNotEmpty())
    @php($hasCarousel = $banners->count() > 1)
    <section class="mx-auto w-full max-w-7xl px-4 pt-6 sm:px-6 lg:px-8" aria-label="{{ $hasCarousel ? 'Banners destacados' : 'Banner destacado' }}">
        <div class="relative overflow-hidden rounded-xl bg-primary-container shadow-card" data-hero-carousel>
            @foreach($banners as $index => $banner)
                <article class="relative min-h-[26rem] items-center overflow-hidden {{ $index === 0 ? 'flex' : 'hidden' }}" data-hero-slide aria-hidden="{{ $index === 0 ? 'false' : 'true' }}" aria-roledescription="diapositiva" aria-label="{{ $index + 1 }} de {{ $banners->count() }}">
                    <img src="{{ Storage::disk('public')->url($banner->image_path) }}" alt="{{ $banner->image_alt }}" class="home-hero-image absolute inset-0 h-full w-full object-cover" @if($index !== 0) loading="lazy" @endif>
                    <div class="absolute inset-0 bg-gradient-to-r from-primary-container/95 via-primary-container/70 to-transparent"></div>
                    <div class="relative z-10 max-w-3xl px-6 py-14 text-white sm:px-12 sm:py-20">
                        <h2 class="font-display text-headline-xl-mobile font-bold tracking-tight sm:text-headline-xl">{{ $banner->title }}</h2>
                        @if($banner->subtitle)<p class="mt-4 max-w-2xl text-base leading-7 text-primary-fixed sm:text-lg">{{ $banner->subtitle }}</p>@endif
                        @if($banner->cta_label && $banner->cta_url)<a href="{{ $banner->cta_url }}" class="mt-7 inline-flex items-center gap-2 rounded-md bg-secondary px-5 py-2.5 text-sm font-semibold text-on-secondary shadow-sm transition hover:bg-secondary-container">{{ $banner->cta_label }} <span aria-hidden="true">→</span></a>@endif
                    </div>
                </article>
            @endforeach
            @if($hasCarousel)
                <div class="absolute inset-x-0 bottom-4 z-20 flex items-center justify-center gap-3" aria-label="Controles del carrusel">
                    <button type="button" data-hero-prev aria-label="Banner anterior" class="grid h-10 w-10 place-items-center rounded-full bg-white/90 text-slate-900 shadow hover:bg-white">‹</button>
                    <div class="flex gap-2" role="group" aria-label="Seleccionar banner">@foreach($banners as $index => $banner)<button type="button" data-hero-dot="{{ $index }}" aria-label="Mostrar banner {{ $index + 1 }}" aria-current="{{ $index === 0 ? 'true' : 'false' }}" class="h-2.5 w-2.5 rounded-full bg-white/60 ring-2 ring-white/60 aria-[current=true]:bg-white"></button>@endforeach</div>
                    <button type="button" data-hero-next aria-label="Banner siguiente" class="grid h-10 w-10 place-items-center rounded-full bg-white/90 text-slate-900 shadow hover:bg-white">›</button>
                </div>
            @endif
        </div>
    </section>
@endif
