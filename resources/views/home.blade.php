@extends('layouts.public')

@section('title', 'Inicio · CMS Core')

@section('content')
    @include('public.partials.hero-banners')
    @include('public.partials.services')
    @include('public.partials.testimonials')
    @include('public.partials.home-content')
@endsection
