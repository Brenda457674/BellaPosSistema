@extends('layouts.auth')

@section('title', 'Verifica tu correo - Bella Flor')

@section('content')
    <h2>Verifica tu correo</h2>
    <p class="sub">
        Te enviamos un enlace a <strong>{{ auth()->user()->email }}</strong>.
        Ábrelo para activar tu cuenta. Si no lo ves, revisa la carpeta de spam.
    </p>

    @if (session('status') === 'verification-link-sent')
        <div class="aviso aviso-ok">Listo, te enviamos un nuevo enlace de verificación.</div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="btn">Reenviar correo de verificación</button>
    </form>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn btn-suave">Cerrar sesión</button>
    </form>
@endsection
