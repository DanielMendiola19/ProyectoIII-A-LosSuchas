@extends('layouts.app')

@section('title', 'Reportes de Ventas')

@section('content')
<link rel="stylesheet" href="{{ asset('css/reportes/reportes.css') }}"/>

<div class="rpt-wrapper">
    <h1 class="rpt-title">Reportes de Ventas</h1>
  

    {{-- Toolbar de filtros --}}
    <section class="rpt-toolbar">
                <div class="rpt-field">
            <label>Tipo de reporte</label>
            <div class="rpt-input-pill">
                <span class="rpt-pill-icon"><i class="bi bi-bar-chart"></i></span>
                <span class="rpt-pill-text">Ventas</span>
            </div>
        </div>

        <div class="rpt-field">
            <label>Desde</label>
            <div class="rpt-input-wrap">
                <span class="rpt-icon"><i class="bi bi-calendar"></i></span>
                <input type="date" id="rpt-desde">
            </div>
        </div>

        <div class="rpt-field">
            <label>Hasta</label>
            <div class="rpt-input-wrap">
                <span class="rpt-icon"><i class="bi bi-calendar"></i></span>
                <input type="date" id="rpt-hasta">
            </div>
        </div>


        <div class="rpt-actions">
            <button class="btn-ghost" id="btn-generar">Generar</button>
            <button class="btn-ghost btn-secondary" id="btn-export-pdf" disabled>Exportar PDF</button>
        </div>
    </section>

    {{-- Tabla principal de resultados --}}
    <section class="tabla-contenedor">
        <table class="tabla-inventario" id="rpt-tabla">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th>Cantidad</th>
                    <th>Total (Bs)</th>
                </tr>
            </thead>
            <tbody>
                <tr class="rpt-empty">
                    <td colspan="4">
                        Sin resultados. Selecciona un rango de fechas y haz clic en
                        <b>Generar</b>.
                    </td>
                </tr>
            </tbody>
        </table>
    </section>

    {{-- Resumen --}}
    <section class="tabla-contenedor rpt-summary">
    <table class="tabla-inventario">
        <thead>
            <tr>
                <th>Productos</th>
                <th>Cantidad total</th>
                <th>Total vendido (Bs)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td id="rpt-prod">0</td>
                <td id="rpt-cant">0</td>
                <td id="rpt-monto">0.00</td>
            </tr>
        </tbody>
    </table>
</section>

    {{-- Reportes diarios automáticos --}}
    <section class="tabla-contenedor rpt-daily">
        <h2 class="rpt-daily-title">Reportes diarios automáticos</h2>
        
        <table class="tabla-inventario">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($fechasDiarias as $f)
                    <tr>
                        <td>{{ $f->fecha }}</td>
                        <td class="rpt-daily-actions">
                            <button class="btn-ghost btn-xs"
                                    onclick="descargarDiario('{{ $f->fecha }}')">
                                Ventas PDF
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2">Sin días con pedidos registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>
</div>

<script>
(function () {
    const $ = (s, c=document) => c.querySelector(s);

    const rutaData = "{{ route('reportes.data') }}";
    const rutaPdf  = "{{ route('reportes.pdf') }}";

    const inpDesde = $('#rpt-desde');
    const inpHasta = $('#rpt-hasta');
    const btnGen   = $('#btn-generar');
    const btnPdf   = $('#btn-export-pdf');
    const tbody    = $('#rpt-tabla tbody');

    const elProd  = $('#rpt-prod');
    const elCant  = $('#rpt-cant');
    const elMonto = $('#rpt-monto');

    function setHoyPorDefecto() {
        const hoy = new Date().toISOString().slice(0, 10);
        inpDesde.value = hoy;
        inpHasta.value = hoy;
    }

    async function generar() {
        const desde = inpDesde.value;
        const hasta = inpHasta.value;

        if (!desde || !hasta) {
            alert('Selecciona las fechas Desde y Hasta.');
            return;
        }

        tbody.innerHTML = `
            <tr class="rpt-empty">
                <td colspan="4">Cargando datos...</td>
            </tr>
        `;
        elProd.textContent  = '0';
        elCant.textContent  = '0';
        elMonto.textContent = '0.00';
        btnPdf.disabled = true;

        const params = new URLSearchParams({ desde, hasta });

        try {
            const resp = await fetch(`${rutaData}?${params.toString()}`);
            const data = await resp.json();

            if (!resp.ok) {
                console.error('Error servidor:', data);
                tbody.innerHTML = `
                    <tr class="rpt-empty">
                        <td colspan="4">Error al cargar datos.</td>
                    </tr>
                `;
                return;
            }

            renderTabla(data.rows || []);
            renderResumen(data.resumen || {});

            if (data.rows && data.rows.length) {
                btnPdf.disabled = false;
                btnPdf.dataset.desde = desde;
                btnPdf.dataset.hasta = hasta;
            }
        } catch (e) {
            console.error(e);
            tbody.innerHTML = `
                <tr class="rpt-empty">
                    <td colspan="4">Error al cargar datos.</td>
                </tr>
            `;
        }
    }

    function renderTabla(rows) {
        if (!rows.length) {
            tbody.innerHTML = `
                <tr class="rpt-empty">
                    <td colspan="4">Sin resultados para el filtro seleccionado.</td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = '';
        rows.forEach(r => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="nombre-prod">${r.producto}</td>
                <td>${r.categoria}</td>
                <td><span class="badge-cant">${r.cantidad}</span></td>
                <td>${parseFloat(r.total).toFixed(2)}</td>
            `;
            tbody.appendChild(tr);
        });
    }

    function renderResumen(res) {
        elProd.textContent  = res.productos ?? 0;
        elCant.textContent  = res.cantidad  ?? 0;
        elMonto.textContent = parseFloat(res.monto ?? 0).toFixed(2);
    }

    function exportarPdf() {
        const desde = btnPdf.dataset.desde;
        const hasta = btnPdf.dataset.hasta;
        if (!desde || !hasta) return;

        const params = new URLSearchParams({ desde, hasta });
        window.open(`${rutaPdf}?${params.toString()}`, '_blank');
    }

    // Reporte diario: desde = hasta = fecha
    window.descargarDiario = function (fecha) {
        const params = new URLSearchParams({
            desde: fecha,
            hasta: fecha
        });
        window.open(`${rutaPdf}?${params.toString()}`, '_blank');
    };

    // Eventos
    btnGen.addEventListener('click', generar);
    btnPdf.addEventListener('click', exportarPdf);

    setHoyPorDefecto();
})();
</script>
@endsection
