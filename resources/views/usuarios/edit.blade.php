@extends('layouts.panel')

@section('titulo', 'Editar usuario')
@section('encabezado', 'Editar: ' . $usuario->name)
@section('subtitulo', 'Actualiza los datos del usuario, su rol o contraseña.')

@push('styles')
    @include('partials.ui')
@endpush

@section('contenido')
    <div class="card" style="max-width:750px; margin:0 auto;">
        <div class="card-body">
            <form method="POST" action="{{ route('usuarios.update', $usuario->id) }}">
                @csrf
                @method('PUT')
                @include('usuarios._form')
                <div class="form-pie">
                    <a class="btn btn-claro" href="{{ route('usuarios.index') }}">Cancelar</a>
                    <button class="btn btn-rosa" type="submit">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
@endsection
