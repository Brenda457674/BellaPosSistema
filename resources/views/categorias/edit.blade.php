@extends('layouts.panel')

@section('titulo', 'Editar categoría')
@section('encabezado', 'Editar: ' . $categoria->nombre)
@section('subtitulo', 'Modifica el nombre o estado de la categoría.')

@push('styles')
    @include('partials.ui')
@endpush

@section('contenido')
    <div class="card" style="max-width:650px; margin:0 auto;">
        <div class="card-body">
            <form method="POST" action="{{ route('categorias.update', $categoria->id) }}">
                @csrf
                @method('PUT')
                @include('categorias._form')
                <div class="form-pie">
                    <a class="btn btn-claro" href="{{ route('categorias.index') }}">Cancelar</a>
                    <button class="btn btn-rosa" type="submit">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
@endsection
