@extends('Template-administrador')

@section('title', 'Sistema de Eventos')

@section('content')
<head>
    <link rel="stylesheet" href="{{ asset('Css/reporte.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<div class="wrapper">
    <div class="main-content">
        <!-- Loading spinner -->
        <div id="loadingSpinner" class="text-center d-none">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
        </div>

        <!-- Filtros de Reportes -->
        <div class="filtros-container mb-4">
            <!-- Filtros arriba -->
            <div class="filtros-top mb-3 d-flex justify-content-end">
                <div class="dropdown">
                    <button class="btn btn-filtros dropdown-toggle" type="button" id="dropdownFiltros" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-funnel me-2"></i>
                        Filtros
                    </button>
                    <ul class="dropdown-menu filtros-dropdown dropdown-menu-end" aria-labelledby="dropdownFiltros">
                        <li class="filtro-header">Estado del Reporte</li>
                        <li>
                            <a class="dropdown-item filtro-item" href="#" data-filtro="" data-tipo="estado">
                                <i class="bi bi-list-ul me-2"></i>
                                Todos los estados
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item filtro-item" href="#" data-filtro="en_espera" data-tipo="estado">
                                <i class="bi bi-clock-fill me-2 text-warning"></i>
                                En Espera
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item filtro-item" href="#" data-filtro="en_proceso" data-tipo="estado">
                                <i class="bi bi-gear-fill me-2 text-info"></i>
                                En Proceso
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item filtro-item" href="#" data-filtro="completado" data-tipo="estado">
                                <i class="bi bi-check-circle-fill me-2 text-success"></i>
                                Completado
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li class="filtro-header">Ordenar por</li>
                        <li>
                            <a class="dropdown-item filtro-item" href="#" data-filtro="desc" data-tipo="orden">
                                <i class="bi bi-sort-down me-2"></i>
                                Más recientes
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item filtro-item" href="#" data-filtro="asc" data-tipo="orden">
                                <i class="bi bi-sort-up me-2"></i>
                                Más antiguos
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Barra de búsqueda extensa -->
            <div class="search-section mb-4">
                <div class="search-bar d-flex align-items-center">
                    <form id="busquedaForm" method="GET" action="{{ route('evento.index') }}" class="w-100 position-relative">
                        <input type="hidden" name="estado" value="{{ request('estado') }}">
                        <input type="hidden" name="orden" value="{{ request('orden') }}">
                        <span class="search-icon">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" 
                            class="form-control" 
                            placeholder="Buscar reportes..." 
                            name="busqueda"
                            value="{{ request('busqueda') }}" 
                            id="inputBusqueda" 
                            autocomplete="off">
                    </form>
                </div>
            </div>
            
            <!-- Estadísticas -->
            <div class="filtros-stats mt-2">
                <small class="text-muted">
                    Mostrando: <strong id="contadorReportes">{{ $eventos->where('condicion', 1)->count() }}</strong> reportes
                    <span id="estadoActual"></span>
                </small>
            </div>
        </div>

        <!-- Tabla de eventos -->
        <div id="tabla-reportes" class="tabla-contenedor shadow-sm rounded">
            <!-- Encabezados -->
            <div class="header-row text-white" style="background-color: #134496;">
                <div class="col-docente">Docente</div>
                <div class="col-recinto">Recinto</div>
                <div class="col-fecha">Fecha</div>
                <div class="col-hora">Hora</div>
                <div class="col-institucion">Institución</div>
                <div class="col-prioridad">Prioridad</div>
                <div class="col-estado">Estado</div>
                <div class="col-detalles">Detalles</div>
            </div>

            <!-- Contenedor para datos asíncronos -->
            <div id="eventos-container">            
                @foreach ($eventos as $evento)
                    @if ($evento->condicion == 1)
                        <div class="record-row hover-effect">
                            <div data-label="Docente">{{ $evento->usuario->name ?? 'N/A' }}</div>
                            <div data-label="Recinto">{{ $evento->horario->recinto->nombre ?? '' }}</div>
                            <div data-label="Fecha">{{ \Carbon\Carbon::parse($evento->fecha)->format('d/m/Y') }}</div>
                            <div data-label="Hora">{{ \Carbon\Carbon::parse($evento->hora_envio)->format('H:i') }}</div>
                            <div class="evento-field" data-label="Institución">
                                    <span class="field-label d-lg-none">Institución:</span>
                                    <span class="field-value">{{ $evento->institucion->nombre ?? '' }}</span>
</div>
                            <div data-label="Prioridad">
                                <span class="badge bg-secondary">
                                    {{ ucfirst($evento->prioridad) }}
                                </span>
                            </div>
                            <div data-label="Estado">
                                @if($evento->estado == 'en_espera')
                                    <span class="badge estado-en-espera" style="background-color: #ffc107 !important; color: #000 !important;">
                                        <i class="bi bi-clock-fill me-1" style="color: #fff !important; font-size: 0.7rem !important;"></i>En Espera
                                    </span>
                                @elseif($evento->estado == 'en_proceso')
                                    <span class="badge estado-en-proceso" style="background-color: #17a2b8 !important; color: #fff !important;">
                                        <i class="bi bi-gear-fill me-1" style="font-size: 0.7rem !important;"></i>En Proceso
                                    </span>
                                @elseif($evento->estado == 'completado')
                                    <span class="badge estado-completado" style="background-color: #28a745 !important; color: #fff !important;">
                                        <i class="bi bi-check-circle-fill me-1" style="font-size: 0.7rem !important;"></i>Completado
                                    </span>
                                @elseif(empty($evento->estado))
                                    <span class="badge bg-warning text-dark" style="background-color: #ffc107 !important; color: #000 !important;">
                                        <i class="bi bi-exclamation-triangle me-1" style="font-size: 0.7rem !important;"></i>Sin Estado
                                    </span>
                                @else
                                    <span class="badge bg-secondary text-white" style="background-color: #6c757d !important; color: #fff !important;">
                                        <i class="bi bi-question-circle me-1" style="font-size: 0.7rem !important;"></i>{{ ucfirst($evento->estado) }}
                                    </span>
                                @endif
                            </div>
                            <div data-label="Detalles">
                                <button class="btn btn-sm rounded-pill px-3" 
                                        style="background-color: #134496; color: white;"
                                        onclick="abrirModal({{ $evento->id }})">
                                    <i class="bi bi-eye me-1"></i> Ver Más
                                </button>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Modales existentes sin cambios -->
@foreach ($eventos as $evento)
    @if ($evento->condicion == 1)
        <div id="modalDetalles-{{ $evento->id }}" class="modal">
            <div class="modal-contenido">
                <div class="modal-encabezado">
                    <span class="icono-atras" onclick="cerrarModal({{ $evento->id }})">
                        <i>
                            <img width="40" height="40" src="https://img.icons8.com/external-solid-adri-ansyah/64/FAB005/external-ui-basic-ui-solid-adri-ansyah-26.png" alt="icono volver"/>
                        </i>
                    </span>
                    <h1 class="titulo">Detalles</h1>
                </div>

                <div class="modal-cuerpo">
                    <div class="row">
                        <div class="col">
                            <label>Docente:</label>
                            <input type="text" value="{{ $evento->usuario->name ?? 'N/A' }}" disabled>

                            <label>Institución:</label>
                            <input type="text" value="{{ $evento->institucion->nombre ?? '' }}" disabled>

                            <label>Recinto:</label>
                            <input type="text" value="{{ $evento->horario->recinto->nombre ?? '' }}" disabled>

                            <label>SubÁrea:</label>
                            <input type="text" value="{{ $evento->subarea->nombre ?? '' }}" disabled>

                            <label>Sección:</label>
                            <input type="text" value="{{ $evento->seccion->nombre ?? '' }}" disabled>

                            <label>Especialidad:</label>
                            <input type="text" value="{{ $evento->subarea->especialidad->nombre ?? '' }}" disabled>
                        </div>

                        <div class="col">
                            <label>Fecha:</label>
                            <input type="text" value="{{ \Carbon\Carbon::parse($evento->fecha)->format('d/m/Y') }}" disabled>
                            
                            <label>Hora:</label>
                            <input type="text" value="{{ \Carbon\Carbon::parse($evento->hora_envio)->format('H:i') }}" disabled>
                            
                            <label>Recinto:</label>
                            <input type="text" value="{{ $evento->horario->recinto->nombre ?? '' }}" disabled>

                            <label>Prioridad:</label>
                            <input type="text" value="{{ ucfirst($evento->prioridad) }}" disabled>

                            <label>Estado:</label>
                            <input type="text" value="{{ 
                                $evento->estado == 'en_espera' ? 'En espera' : 
                                ($evento->estado == 'en_proceso' ? 'En proceso' : 
                                ($evento->estado == 'completado' ? 'Completado' : 
                                ucfirst($evento->estado ?? 'Sin estado'))) 
                            }}" disabled>
                        </div>
                    </div>

                    <div class="observaciones">
                        <label>Observaciones:</label>
                        <textarea disabled>{{ $evento->observacion }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach

@endsection

@push('styles')
<style>
.hover-effect {
    transition: all 0.3s ease;
}

.hover-effect:hover {
    background-color: rgba(0,0,0,0.02);
    transform: translateY(-1px);
}

.badge {
    font-weight: 500;
    padding: 0.5em 0.8em;
}

.tabla-contenedor {
    border: 1px solid rgba(0,0,0,0.1);
    background: white;
}

.header-row {
    font-weight: 500;
}

.record-row {
    border-bottom: 1px solid rgba(0,0,0,0.05);
}

.form-control, .form-select, .input-group-text {
    border-radius: 20px;
}

.input-group .form-select {
    border-start-start-radius: 0;
    border-end-start-radius: 0;
}

/* Update color variables */
:root {
    --primary-blue: #134496;
}

.bg-primary {
    background-color: var(--primary-blue) !important;
}

.btn-primary {
    background-color: var(--primary-blue) !important;
    border-color: var(--primary-blue) !important;
}

/* Modal styling updates */
.modal-contenido {
    background: none;
    box-shadow: none;
}

.modal-cuerpo {
    background: white;
    border-radius: 8px;
    padding: 20px;
}

.swal2-popup {
    background: transparent !important;
    box-shadow: none !important;
}

.swal2-content {
    background: white;
    border-radius: 8px;
    padding: 20px !important;
}

/* Update spinner color */
.spinner-border.text-primary {
    color: var(--primary-blue) !important;
}

/* Añadir transición suave para actualizaciones */
#eventos-container {
    transition: opacity 0.15s ease-in-out;
}

@media (max-width: 768px) {
    .record-row {
        grid-template-columns: 1fr;
        gap: 0.5rem;
        padding: 1rem;
    }

    .record-row > div {
        padding: 0.5rem;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }

    .record-row > div:last-child {
        border-bottom: none;
    }

    [data-label]:before {
        content: attr(data-label);
        font-weight: 600;
        display: inline-block;
        width: 120px;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const loadingSpinner = document.getElementById('loadingSpinner');
const eventosContainer = document.getElementById('eventos-container');
let currentTimestamp = '{{ $eventos->max('updated_at') }}';

async function cargarEventos() {
    try {
        const response = await fetch(`{{ route('eventos.load') }}?timestamp=${currentTimestamp}`, {
            headers: { 
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        if (!response.ok) throw new Error('Error en la red');

        const data = await response.json();
        
        if (data.success && data.hasNewData) {
            loadingSpinner.classList.remove('d-none');
            eventosContainer.style.opacity = '0.6';
            
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = data.html;
            
            const newEventosContainer = tempDiv.querySelector('#eventos-container');
            if (newEventosContainer) {
                eventosContainer.innerHTML = newEventosContainer.innerHTML;
                currentTimestamp = data.timestamp;
            }
            
            eventosContainer.style.opacity = '1';
            loadingSpinner.classList.add('d-none');
        }
    } catch (error) {
        console.error('Error:', error);
        loadingSpinner.classList.add('d-none');
    }
}

// Comprobar cambios cada 3 segundos
const intervalId = setInterval(cargarEventos, 3000);

// Función para abrir modal
function abrirModal(id) {
    Swal.fire({
        html: document.getElementById('modalDetalles-' + id).innerHTML,
        width: '80%',
        showConfirmButton: false,
        showCloseButton: false,
        customClass: {
            container: 'modal-detalles-container',
            popup: 'bg-transparent',
            content: 'bg-transparent'
        }
    });
}

function cerrarModal(id) {
    Swal.close();
}

// Limpiar intervalo cuando se abandona la página
window.addEventListener('beforeunload', () => {
    clearInterval(intervalId);
});

// Cargar datos iniciales
document.addEventListener('DOMContentLoaded', function() {
    cargarEventos();
    initializeFiltros();
});

// Variables globales para filtros
let filtrosActivos = {
    estado: '',
    orden: 'desc',
    busqueda: ''
};

function initializeFiltros() {
    // Event listeners para filtros dropdown
    document.querySelectorAll('.filtro-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const filtro = this.getAttribute('data-filtro');
            const tipo = this.getAttribute('data-tipo');
            
            // Actualizar filtro activo
            filtrosActivos[tipo] = filtro;
            
            // Actualizar UI
            updateActiveFilters();
            aplicarFiltros();
            
            // Cerrar dropdown
            bootstrap.Dropdown.getInstance(document.getElementById('dropdownFiltros')).hide();
        });
    });
    
    // Event listener para búsqueda
    document.getElementById('inputBusqueda').addEventListener('input', function() {
        filtrosActivos.busqueda = this.value;
        aplicarFiltros();
    });
    
    // Event listener para el formulario de búsqueda
    document.getElementById('busquedaForm').addEventListener('submit', function(e) {
        e.preventDefault();
        filtrosActivos.busqueda = document.getElementById('inputBusqueda').value;
        aplicarFiltros();
    });
}

function updateActiveFilters() {
    // Función simplificada - ya no mostramos chips de filtros activos
    // Solo mantenemos para compatibilidad con el código existente
}

function removeFilter(tipo) {
    if (tipo === 'orden') {
        filtrosActivos[tipo] = 'desc'; // Volver al default
    } else {
        filtrosActivos[tipo] = '';
    }
    
    if (tipo === 'busqueda') {
        document.getElementById('inputBusqueda').value = '';
    }
    
    updateActiveFilters();
    aplicarFiltros();
}

function limpiarTodosFiltros() {
    filtrosActivos = {
        estado: '',
        orden: 'desc',
        busqueda: ''
    };
    
    document.getElementById('inputBusqueda').value = '';
    updateActiveFilters();
    aplicarFiltros();
}

function aplicarFiltros() {
    const loadingSpinner = document.getElementById('loadingSpinner');
    const eventosContainer = document.getElementById('eventos-container');
    
    // Mostrar spinner
    loadingSpinner.classList.remove('d-none');
    eventosContainer.style.opacity = '0.5';
    
    // Construir URL con parámetros
    const params = new URLSearchParams();
    if (filtrosActivos.estado) params.append('estado', filtrosActivos.estado);
    if (filtrosActivos.orden) params.append('orden', filtrosActivos.orden);
    if (filtrosActivos.busqueda) params.append('busqueda', filtrosActivos.busqueda);
    
    // Hacer petición AJAX
    fetch(`{{ route('evento.index') }}?${params.toString()}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            eventosContainer.innerHTML = data.html;
            updateStats();
        }
        loadingSpinner.classList.add('d-none');
        eventosContainer.style.opacity = '1';
    })
    .catch(error => {
        console.error('Error al filtrar:', error);
        loadingSpinner.classList.add('d-none');
        eventosContainer.style.opacity = '1';
    });
}

function updateStats() {
    const rows = document.querySelectorAll('.record-row').length;
    document.getElementById('contadorReportes').textContent = rows;
    
    const estadoActual = document.getElementById('estadoActual');
    let estadoText = '';
    
    if (filtrosActivos.estado) {
        const estadoTexts = {
            'en_espera': 'En Espera',
            'en_proceso': 'En Proceso',
            'completado': 'Completado'
        };
        estadoText = ` con estado: <strong>${estadoTexts[filtrosActivos.estado]}</strong>`;
    }
    
    estadoActual.innerHTML = estadoText;
}
</script>
@endpush
