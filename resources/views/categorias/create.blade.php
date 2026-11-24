@extends('layouts.panel')

@section('titulo', 'Nueva categoría')
@section('encabezado', 'Nueva categoría de productos')
@section('subtitulo', 'Ingresa el nombre y estado de la categoría.')

@push('styles')
    @include('partials.ui')
@endpush

@section('contenido')
    <div class="card" style="max-width:650px; margin:0 auto;">
        <div class="card-body">
            <form method="POST" action="{{ route('categorias.store') }}">
                @csrf
                @include('categorias._form')
                <div class="form-pie">
                    <a class="btn btn-claro" href="{{ route('categorias.index') }}">Cancelar</a>
                    <button class="btn btn-rosa" type="submit">Guardar categoría</button>
                </div>
            </form>
        </div>
    </div>
@endsection
