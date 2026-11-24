@extends('layouts.panel')

@section('titulo', 'Editar unidad')
@section('encabezado', 'Editar: ' . $unidad->nombre)
@section('subtitulo', 'Modifica la denominación o regla de decimales.')

@push('styles')
    @include('partials.ui')
@endpush

@section('contenido')
    <div class="card" style="max-width:650px; margin:0 auto;">
        <div class="card-body">
            <form method="POST" action="{{ route('unidades.update', $unidad->id) }}">
                @csrf
                @method('PUT')
                @include('unidades._form')
                <div class="form-pie">
                    <a class="btn btn-claro" href="{{ route('unidades.index') }}">Cancelar</a>
                    <button class="btn btn-rosa" type="submit">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
@endsection
