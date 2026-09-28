@if($aboutPage)
    <section class="home-reveal mx-auto grid w-full max-w-7xl gap-8 px-4 py-16 sm:px-6 lg:grid-cols-[.8fr_1.2fr] lg:px-8">
        <div>
            <x-badge variant="primary">Quiénes somos</x-badge>
            <h2 class="mt-4 font-display text-headline-xl-mobile font-bold tracking-tight text-slate-900 sm:text-headline-xl">{{ $aboutPage->title }}</h2>
            @if($aboutPage->subtitle)<p class="mt-4 text-body-lg text-slate-600">{{ $aboutPage->subtitle }}</p>@endif
            <a href="{{ route('about.public') }}" class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-secondary hover:underline">Conoce a USF Tech Solutions <span aria-hidden="true">→</span></a>
        </div>
        <div class="grid gap-5 sm:grid-cols-2">
            @if($aboutPage->mission)<x-card title="Nuestra misión"><p class="text-sm leading-6 text-slate-600">{{ $aboutPage->mission }}</p></x-card>@endif
            @if($aboutPage->vision)<x-card title="Nuestra visión"><p class="text-sm leading-6 text-slate-600">{{ $aboutPage->vision }}</p></x-card>@endif
        </div>
    </section>
@endif

@if($posts->isNotEmpty())
    <section class="home-reveal border-y border-slate-200 bg-surface-container-low">
        <div class="mx-auto w-full max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div><x-badge variant="primary">Ideas y perspectivas</x-badge><h2 class="mt-4 font-display text-headline-xl-mobile font-bold tracking-tight text-slate-900 sm:text-headline-xl">Lo último en tecnología</h2></div>
                <a href="{{ route('posts.public.index') }}" class="text-sm font-semibold text-secondary hover:underline">Ver todas las noticias <span aria-hidden="true">→</span></a>
            </div>
            <div class="mt-8 grid gap-5 md:grid-cols-3">
                @foreach($posts as $post)
                    <x-card padding="none" class="flex h-full flex-col overflow-hidden">
                        @if($post->cover_path)<a href="{{ route('posts.public.show', ['post' => $post->slug]) }}"><img src="{{ Storage::disk('public')->url($post->cover_path) }}" alt="{{ $post->cover_alt }}" class="aspect-[16/9] w-full object-cover" loading="lazy"></a>@endif
                        <div class="flex flex-1 flex-col p-5">@if($post->category)<x-badge variant="info">{{ $post->category->name }}</x-badge>@endif
                        <h3 class="mt-4 font-display text-headline-md font-semibold text-slate-900"><a class="hover:text-secondary" href="{{ route('posts.public.show', ['post' => $post->slug]) }}">{{ $post->title }}</a></h3>
                        <p class="mt-3 flex-1 text-sm leading-6 text-slate-600">{{ $post->excerpt }}</p>
                        <p class="mt-5 text-xs text-slate-500">{{ $post->published_at?->format('d/m/Y') }}</p></div>
                    </x-card>
                @endforeach
            </div>
        </div>
    </section>
@endif

@if($videos->isNotEmpty())
    <section class="home-reveal mx-auto w-full max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-end justify-between gap-4"><div><x-badge variant="primary">En acción</x-badge><h2 class="mt-4 font-display text-headline-xl-mobile font-bold tracking-tight text-slate-900 sm:text-headline-xl">Conoce nuestras ideas</h2></div><a href="{{ route('videos.public.index') }}" class="text-sm font-semibold text-secondary hover:underline">Ver galería de videos <span aria-hidden="true">→</span></a></div>
        <div class="mt-8 grid gap-5 md:grid-cols-2">
            @foreach($videos as $video)
                <x-card padding="none" class="overflow-hidden"><div class="aspect-video bg-slate-100"><iframe class="h-full w-full" src="{{ $video->embed_url }}" title="{{ $video->title }}" loading="lazy" referrerpolicy="strict-origin-when-cross-origin" allow="accelerometer; encrypted-media; picture-in-picture; fullscreen" allowfullscreen sandbox="allow-scripts allow-same-origin allow-presentation"></iframe></div><div class="p-5"><h3 class="font-display text-headline-md font-semibold text-slate-900">{{ $video->title }}</h3><p class="mt-2 text-sm leading-6 text-slate-600">{{ $video->description }}</p></div></x-card>
            @endforeach
        </div>
    </section>
@endif

@if($teamMembers->isNotEmpty())
    <section class="home-reveal border-y border-slate-200 bg-surface-container-low">
        <div class="mx-auto w-full max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-end justify-between gap-4"><div><x-badge variant="primary">Personas que hacen posible el cambio</x-badge><h2 class="mt-4 font-display text-headline-xl-mobile font-bold tracking-tight text-slate-900 sm:text-headline-xl">Un equipo, muchas perspectivas</h2></div><a href="{{ route('team.public.index') }}" class="text-sm font-semibold text-secondary hover:underline">Conoce al equipo <span aria-hidden="true">→</span></a></div>
            <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($teamMembers as $member)
                    <x-card class="text-center"><div class="mx-auto h-24 w-24 overflow-hidden rounded-full bg-secondary-fixed"><img src="{{ Storage::disk('public')->url($member->image_path) }}" alt="{{ $member->image_alt }}" class="h-full w-full object-cover" loading="lazy"></div><h3 class="mt-4 font-display text-headline-md font-semibold text-slate-900">{{ $member->name }}</h3><p class="mt-1 text-sm font-medium text-secondary">{{ $member->role }}</p><p class="mt-3 text-sm leading-6 text-slate-600">{{ $member->bio }}</p></x-card>
                @endforeach
            </div>
        </div>
    </section>
@endif
