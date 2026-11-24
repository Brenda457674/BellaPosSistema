@extends('layouts.auth')

@section('title', 'Recuperar contraseña - Bella Flor')

@section('content')
    <h2>Recuperar contraseña</h2>
    <p class="sub">Escribe el correo de tu cuenta y te enviaremos un enlace para crear una contraseña nueva.</p>

    @if (session('status'))
        <div class="aviso aviso-ok">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="aviso aviso-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="campo">
            <label for="email">Correo electrónico</label>
            <div class="entrada">
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                       placeholder="tucorreo@gmail.com" required autofocus autocomplete="email">
            </div>
        </div>
        <button type="submit" class="btn">Enviar enlace</button>
    </form>

    <p class="volver"><a class="enlace" href="{{ route('login') }}">Volver a iniciar sesión</a></p>
@endsection
