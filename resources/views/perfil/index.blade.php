@extends('layouts.app')

@section('title', 'Mi Perfil - Coffeeology')

@section('content')
<link rel="stylesheet" href="{{ asset('css/perfil/perfil.css') }}">

<div class="perfil-container">
    <h1><i class="fas fa-user-circle"></i> Mi Perfil</h1>

    <div class="perfil-card">
        <div class="perfil-info">
            <p><strong>Nombre:</strong> {{ Auth::user()->nombre }} {{ Auth::user()->apellido }}</p>
            <p><strong>Correo:</strong> {{ Auth::user()->correo }}</p>
            <p><strong>Rol:</strong> {{ Auth::user()->rol->nombre }}</p>
            <p><strong>Fecha de Registro:</strong> {{ Auth::user()->created_at->format('d/m/Y') }}</p>
        </div>

        @if(Auth::user()->rol->nombre == 'Mesero' || Auth::user()->rol->nombre == 'Cajero')
            <div class="perfil-pedidos">
                <h3><i class="fas fa-shopping-bag"></i> Tus Pedidos</h3>
                <p>Total: {{ Auth::user()->pedidos->count() }}</p>
                @if(Auth::user()->pedidos->count() > 0)
                    <ul>
                        @foreach(Auth::user()->pedidos->take(3) as $pedido)
                            <li>Pedido #{{ $pedido->id }} - {{ $pedido->estado }} - {{ $pedido->created_at->format('d/m/Y H:i') }}</li>
                        @endforeach
                    </ul>
                @else
                    <p>No has realizado pedidos aún.</p>
                @endif
            </div>
        @endif

        <hr style="margin: 25px 0; border-color: rgba(230,179,37,0.3)">

        <h3><i class="fas fa-key"></i> Cambiar Contraseña</h3>
        <form id="form-cambiar-password" action="{{ route('perfil.cambiarPassword') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="current_password">Contraseña Actual</label>
                <input type="password" name="current_password" id="current_password" class="form-control" required>
                @error('current_password') <small style="color: var(--rojo-peligro)">{{ $message }}</small> @enderror
            </div>

            <div class="form-group">
                <label for="password">Nueva Contraseña</label>
                <input type="password" name="password" id="password" class="form-control" required>
                @error('password') <small style="color: var(--rojo-peligro)">{{ $message }}</small> @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirmar Nueva Contraseña</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
            </div>

            <div class="perfil-acciones">
                <button type="submit" class="btn-principal"><i class="fas fa-save"></i> Guardar Cambios</button>
            </div>
        </form>

       
    </div>
</div>
@endsection
