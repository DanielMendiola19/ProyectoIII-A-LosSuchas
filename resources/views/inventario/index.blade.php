@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/inventario/inventario.css') }}">

{{-- Asegúrate de tener esto en tu layout <head>:  <meta name="csrf-token" content="{{ csrf_token() }}"> --}}

<div class="tabla-contenedor">
  <h1 class="titulo-inventario">Gestión de Inventario</h1>
  <div id="inv-alert" class="inv-alert" role="status" aria-live="polite"></div>

  <table class="tabla-inventario">
    <thead>
      <tr>
        <th>Imagen</th>
        <th>Producto</th>
        <th>Cantidad actual</th>
        <th>Agregar / Quitar</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($productos as $producto)
        @php
          $maxAumentar = 50 - $producto->stock;
          $maxDisminuir = $producto->stock;
        @endphp
        <tr data-id="{{ $producto->id }}">
          <td>
            @if ($producto->imagen)
              <img src="{{ asset('storage/' . $producto->imagen) }}" alt="{{ $producto->nombre }}" class="img-prod">
            @else
              <div class="img-vacia"></div>
            @endif
          </td>

          <td class="nombre-prod">{{ $producto->nombre }}</td>

          <td>
            <span class="badge-cant" data-stock>{{ $producto->stock }}</span>
          </td>

          <td>
            <div class="contador">
              <button type="button" class="btn-cnt menos">−</button>
              <input type="number" class="input-cant"
                     value="1" min="1" step="1"
                     data-max-add="{{ $maxAumentar }}"
                     data-max-sub="{{ $maxDisminuir }}">
              <button type="button" class="btn-cnt mas">+</button>
            </div>
          </td>

          <td class="acciones-cell">
            <button
              class="btn-accion aumentar js-accion"
              data-action="aumentar"
              data-name="{{ $producto->nombre }}"
              data-url="{{ route('inventario.aumentar', $producto) }}"
            >Aumentar</button>

            <button
              class="btn-accion disminuir js-accion"
              data-action="disminuir"
              data-name="{{ $producto->nombre }}"
              data-url="{{ route('inventario.disminuir', $producto) }}"
            >Disminuir</button>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>

{{-- overlay y confirm centrado --}}
<div id="inv-overlay" class="confirm-overlay hidden"></div>

<div id="inv-confirm" class="confirm-pop hidden" role="dialog" aria-modal="true">
  <div class="confirm-title">Confirmar acción</div>
  <div class="confirm-text"></div>
  <div class="confirm-actions">
    <button type="button" class="btn-pop btn-cancel">Cancelar</button>
    <button type="button" class="btn-pop btn-ok">OK</button>
  </div>
</div>

{{-- toast para mensajes rápidos (déjalo si ya lo tienes) --}}
<div id="toast" class="toast-mini"></div>


{{-- tu JS externo --}}
<script defer src="{{ asset('js/inventario/inventario.js') }}"></script>
@endsection
