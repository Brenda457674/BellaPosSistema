@extends('layouts.panel')

@section('titulo', 'Editar cliente')
@section('encabezado', 'Editar: ' . $cliente->nombres . ' ' . $cliente->apellidos)
@section('subtitulo', 'Actualiza los datos de contacto y facturación del cliente.')

@push('styles')
    @include('partials.ui')
@endpush

@section('contenido')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('clientes.update', $cliente->id) }}">
                @csrf
                @method('PUT')
                @include('clientes._form')
                <div class="form-pie">
                    <a class="btn btn-claro" href="{{ route('clientes.index') }}">Cancelar</a>
                    <button class="btn btn-rosa" type="submit">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
@endsection
