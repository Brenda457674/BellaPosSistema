@extends('layouts.panel')

@section('titulo', 'Editar proveedor')
@section('encabezado', 'Editar proveedor')
@section('subtitulo', $proveedor->razon_social)

@push('styles')
    @include('partials.ui')
@endpush

@section('contenido')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('proveedores.update', $proveedor->id) }}">
                @csrf
                @method('PUT')
                @include('proveedores._form')
                <div class="form-pie">
                    <a class="btn btn-claro" href="{{ route('proveedores.index') }}">Cancelar</a>
                    <button class="btn btn-rosa" type="submit">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
@endsection
