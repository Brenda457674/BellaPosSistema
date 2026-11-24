@extends('layouts.panel')

@section('titulo', $__env->yieldContent('title', 'Panel'))

@section('contenido')
    @yield('content')
@endsection
