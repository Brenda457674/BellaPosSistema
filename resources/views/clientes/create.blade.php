@extends('layouts.panel')

@section('titulo', 'Nuevo cliente')
@section('encabezado', 'Nuevo cliente')
@section('subtitulo', 'Los campos con * son obligatorios.')

@push('styles')
    @include('partials.ui')
@endpush

@section('contenido')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('clientes.store') }}">
                @csrf
                @include('clientes._form')
                <div class="form-pie">
                    <a class="btn btn-claro" href="{{ route('clientes.index') }}">Cancelar</a>
                    <button class="btn btn-rosa" type="submit">Guardar cliente</button>
                </div>
            </form>
        </div>
    </div>
@endsection
