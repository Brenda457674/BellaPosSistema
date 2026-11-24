
@extends('layouts.panel')

@section('titulo', __('messages.edit_product'))

@section('encabezado', __('messages.edit') . ': ' . $producto->nombre)

@section('subtitulo', __('messages.code') . ': ' . $producto->codigo . ' · ' . __('messages.required_fields_note'))

@push('styles')
    @include('partials.ui')
@endpush

@section('contenido')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.productos.update', $producto->id) }}">
                @csrf
                @method('PUT')

                @include('admin.productos._form')

                <div class="form-pie">
                    <a class="btn btn-claro" href="{{ route('admin.productos.index') }}">
                        {{ __('messages.cancel') }}
                    </a>

                    <button class="btn btn-rosa" type="submit">
                        {{ __('messages.save_changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
