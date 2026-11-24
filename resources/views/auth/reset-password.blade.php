@extends('layouts.auth')

@section('title', 'Nueva contraseña - Bella Flor')

@section('content')
    <h2>Crea tu nueva contraseña</h2>
    <p class="sub">Usa mínimo 8 caracteres. Después podrás entrar con ella.</p>

    @if ($errors->any())
        <div class="aviso aviso-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="campo">
            <label for="email">Correo electrónico</label>
            <div class="entrada">
                <input type="email" id="email" name="email" value="{{ old('email', $email) }}" required readonly>
            </div>
        </div>

        <div class="campo">
            <label for="password">Nueva contraseña</label>
            <div class="entrada con-ojo">
                <input type="password" id="password" name="password" required minlength="8" autofocus autocomplete="new-password">
                @include('auth._ojo', ['para' => 'password'])
            </div>
        </div>

        <div class="campo">
            <label for="password_confirmation">Repite la contraseña</label>
            <div class="entrada con-ojo">
                <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8" autocomplete="new-password">
                @include('auth._ojo', ['para' => 'password_confirmation'])
            </div>
        </div>

        <button type="submit" class="btn">Cambiar contraseña</button>
    </form>
@endsection
