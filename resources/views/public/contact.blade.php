@extends('layouts.public')
@section('title', 'Contacto')
@section('content')
<section class="mx-auto w-full max-w-3xl flex-1 px-4 py-12 sm:px-6 lg:px-8"><x-breadcrumb :items="[['label' => 'Inicio', 'url' => route('home')], ['label' => 'Contacto']]"/><h1 class="mt-5 font-display text-headline-xl-mobile font-bold text-slate-900 sm:text-headline-xl">Contacto</h1><p class="mt-3 text-body-lg text-slate-600">Envíanos tu consulta mediante este formulario.</p>
@if(session('success'))<div class="mt-6"><x-alert variant="success">{{ session('success') }}</x-alert></div>@endif
@if(session('notificationPending'))<div class="mt-4"><x-alert variant="warning">El mensaje quedó almacenado, pero no se pudo enviar la notificación interna.</x-alert></div>@endif
@if($errors->any())<div class="mt-6"><x-alert variant="danger"><ul class="list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></x-alert></div>@endif
<x-card class="mt-6"><form action="{{ route('contact.store') }}" method="POST" class="space-y-5">@csrf
    <div class="absolute -left-[10000px] h-px w-px overflow-hidden" aria-hidden="true"><label for="website">Deja este campo vacío</label><input id="website" name="website" type="text" tabindex="-1" autocomplete="off"></div>
    <x-input name="name" label="Nombre" maxlength="120" required autocomplete="name" :value="old('name')"/>
    <x-input name="email" label="Correo electrónico" type="email" maxlength="254" required autocomplete="email" :value="old('email')"/>
    <x-input name="subject" label="Asunto" maxlength="180" required :value="old('subject')"/>
    <x-textarea name="message" label="Mensaje" rows="7" maxlength="5000" required>{{ old('message') }}</x-textarea>
    <p class="text-xs text-slate-500">El formulario limita la frecuencia de envío y almacena el mensaje para su seguimiento.</p>
    <div class="flex justify-end"><x-button type="submit">Enviar mensaje</x-button></div>
</form></x-card></section>
@endsection
