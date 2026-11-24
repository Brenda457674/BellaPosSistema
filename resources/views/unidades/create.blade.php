@extends('layouts.panel')

@section('titulo', 'Nueva unidad de medida')
@section('encabezado', 'Nueva unidad de medida')
@section('subtitulo', 'Ingresa el nombre, abreviatura y regla de fraccionamiento.')

@push('styles')
    @include('partials.ui')
@endpush

@section('contenido')
    <div class="card" style="max-width:650px; margin:0 auto;">
        <div class="card-body">
            <form method="POST" action="{{ route('unidades.store') }}">
                @csrf
                @include('unidades._form')
                <div class="form-pie">
                    <a class="btn btn-claro" href="{{ route('unidades.index') }}">Cancelar</a>
                    <button class="btn btn-rosa" type="submit">Guardar unidad</button>
                </div>
            </form>
        </div>
    </div>
@endsection
