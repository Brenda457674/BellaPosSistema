@extends('layouts.panel')

@section('titulo', 'Nuevo usuario')
@section('encabezado', 'Crear nuevo usuario')
@section('subtitulo', 'Registra una nueva cuenta y asígnale su rol en el sistema.')

@push('styles')
    @include('partials.ui')
@endpush

@section('contenido')
    <div class="card" style="max-width:750px; margin:0 auto;">
        <div class="card-body">
            <form method="POST" action="{{ route('usuarios.store') }}">
                @csrf
                @include('usuarios._form')
                <div class="form-pie">
                    <a class="btn btn-claro" href="{{ route('usuarios.index') }}">Cancelar</a>
                    <button class="btn btn-rosa" type="submit">Crear usuario y enviar correo</button>
                </div>
            </form>
        </div>
    </div>
@endsection
