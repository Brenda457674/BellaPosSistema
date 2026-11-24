@extends('layouts.panel')

@section('titulo', 'Nuevo proveedor')
@section('encabezado', 'Nuevo proveedor')
@section('subtitulo', 'Los campos con * son obligatorios.')

@push('styles')
    @include('partials.ui')
@endpush

@section('contenido')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('proveedores.store') }}">
                @csrf
                @include('proveedores._form')
                <div class="form-pie">
                    <a class="btn btn-claro" href="{{ route('proveedores.index') }}">Cancelar</a>
                    <button class="btn btn-rosa" type="submit">Guardar</button>
                </div>
            </form>
        </div>
    </div>
@endsection
