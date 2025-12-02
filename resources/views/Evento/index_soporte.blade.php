@extends('Template-soporte')

@section('title', 'Sistema de Eventos')

@section('content')
<head>
    <link rel="stylesheet" href="{{ asset('Css/reporte.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
            {{-- Filtros --}}
            <div class="filtros-top mb-3">
                <div class="dropdown">
                    <button class="btn btn-outline-primary dropdown-toggle" 
                            type="button" 
                            id="filtrosDropdown" 
                            data-bs-toggle="dropdown" 
                            aria-expanded="false">
                        <i class="bi bi-funnel me-2"></i>
                        Filtros
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="filtrosDropdown" style="min-width: 250px;">
                        <!-- DEBUG: {{ $recintos ? $recintos->count() : 'null' }} recintos encontrados -->
                        <li class="filtro-header">Filtrar por Recinto</li>
                        <li>
                            <a class="dropdown-item filtro-item" href="#" data-filtro="" data-tipo="recinto">
                                <i class="bi bi-building me-2"></i>
                                Todos los recintos
                            </a>
                        </li>
                        @if(isset($recintos) && $recintos->count() > 0)
                            @foreach($recintos as $recinto)
                                        <li>
                                            <a class="dropdown-item filtro-item" href="#" 
                                            data-filtro="{{ $recinto->id }}" data-tipo="recinto">
                                                <i class="bi bi-geo-alt-fill me-2"></i>
                                                {{ $recinto->nombre }}
                                            </a>
                                        </li>
                            @endforeach
                        @else
                            <li>
                                <span class="dropdown-item text-muted">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    No hay recintos disponibles
                                </span>
                            </li>
                        @endif
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
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="#" id="limpiarFiltros" style="color: #dc3545;">
                                <i class="bi bi-x-circle me-2"></i>
                                Limpiar todo
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Barra de búsqueda extensa -->
            <div class="search-section mb-4">
                <div class="search-bar d-flex align-items-center">
                    <form id="busquedaForm" method="GET" action="{{ route('evento.index_soporte') }}" class="w-100 position-relative">
                        <input type="hidden" name="recinto" value="{{ request('recinto') }}">
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
                    Mostrando: <strong id="contadorReportes">{{ $eventos->count() }}</strong> reportes
                    <span id="recintoActual"></span>
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
                @include('Evento.partials.eventos-lista-soporte', ['eventos' => $eventos])
            </div>
        </div>
    </div>
</div>


<!-- ================= MODALES DE DETALLE Y EDICIÓN ================= -->
@foreach ($eventos as $evento)
    <!-- Modal de edición para cada evento -->
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
                        <!-- Datos del docente y evento -->
                        <label>Docente:</label>
                        <input type="text" value="{{ $evento->usuario->name ?? 'N/A' }}" disabled>

                        <label>Institución:</label>
                        <input type="text" value="{{ $evento->horario->recinto->institucion->nombre ?? '' }}" disabled>

                        <label>SubÁrea:</label>
                        <input type="text" value="{{ $evento->subarea->nombre ?? '' }}" disabled>

                        <label>Sección:</label>
                        <input type="text" value="{{ $evento->seccion->nombre ?? '' }}" disabled>

                        <label>Especialidad:</label>
                        <input type="text" value="{{ $evento->subarea->especialidad->nombre ?? '' }}" disabled>
                    </div>

                    <div class="col">
                        <!-- Datos de fecha, hora, prioridad y estado -->
                        <label>Fecha:</label>
                        <input type="text" value="{{ \Carbon\Carbon::parse($evento->fecha)->format('d/m/Y') }}" disabled>
                        
                        <label>Hora:</label>
                        <input type="text" value="{{ \Carbon\Carbon::parse($evento->hora_envio)->format('H:i') }}" disabled>
                        
                        <label>Recinto:</label>
                        <input type="text" value="{{ $evento->horario->recinto->nombre ?? '' }}" disabled>

                        <label>Prioridad:</label>
                        <input type="text" value="{{ ucfirst($evento->prioridad) }}" disabled>

                        <label>Estado:</label>
                        <select class="form-select mb-3" id="estado-{{ $evento->id }}">
                            <option value="en_espera" @if($evento->estado == 'en_espera') selected @endif>En espera</option>
                            <option value="en_proceso" @if($evento->estado == 'en_proceso') selected @endif>En proceso</option>
                            <option value="completado" @if($evento->estado == 'completado') selected @endif>Completado</option>
                        </select>
                    </div>
                </div>

                <div class="observaciones mt-3">
                    <label>Observaciones:</label>
                    <textarea disabled>{{ $evento->observacion }}</textarea>
                </div>

                <!-- Botón guardar cambios centrado y pequeño -->
                <div class="mt-4 d-flex justify-content-center">
                    <button type="button" class="btn btn-primary px-4 py-2" style="background-color:#134496; min-width:150px;" onclick="guardarEstado({{ $evento->id }})">
                        <i class="bi bi-save me-2"></i>Guardar cambios
                    </button>
                </div>
            </div>
        </div>
    </div>
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

/* Aadir transición suave para actualizaciones */
#eventos-container {
    transition: opacity 0.15s ease-in-out;
}

/* Add to your styles section */
.form-select:not([disabled]) {
    background-color: white;
    border: 1px solid #134496;
    cursor: pointer;
}

.form-select:not([disabled]):focus {
    border-color: #134496;
    box-shadow: 0 0 0 0.25rem rgba(19, 68, 150, 0.25);
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

/* SweetAlert custom styles estilo profesor */
.swal2-custom-popup {
    background-color: #ffffff !important;
    border-radius: 12px !important;
    padding: 20px !important;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2) !important;
}

.swal2-custom-popup .swal2-title {
    font-size: 1.5rem !important;
    font-weight: 600;
    color: #134496;
}

.swal2-custom-popup .form-control,
.swal2-custom-popup .form-select {
    border-radius: 10px;
    margin-bottom: 10px;
}

@media (max-width: 576px) {
    .swal2-custom-popup {
        margin: 1rem !important;
        padding: 15px !important;
    }

    .swal2-custom-popup .form-control,
    .swal2-custom-popup .form-select {
        font-size: 16px; /* Previene zoom en iOS */
    }
}
</style>
@endpush

@push('scripts')
<!-- ================= SCRIPTS PARA MODAL Y AJAX ================= -->
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
        showConfirmButton: false, //  Oculta el botón "Confirmar"
        showCloseButton: false,   // 🚫 Oculta el botón de cerrar (X), cámbialo a true si lo quieres
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

// Modal estilo profesor para soporte
async function abrirModalProfesor(evento) {
    console.log('Evento recibido:', evento);
    
    let eventoData = evento; // usar evento original por defecto
    
    try {
        // Obtener el estado actual del evento desde la base de datos
        const response = await fetch(`/evento/${evento.id}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (response.ok) {
            const eventoActualizado = await response.json();
            console.log('Evento actualizado desde BD:', eventoActualizado);
            eventoData = eventoActualizado.data || eventoActualizado;
        } else {
            console.log('Error en respuesta, usando evento original');
        }
        
    } catch (error) {
        console.error('Error al obtener evento:', error);
        // Usar evento original si hay error
    }
    
    // Mostrar modal siempre, con datos actualizados o originales
    Swal.fire({
        html: `
        <div class="modal-contenido">
            <div class="modal-encabezado">
                <span class="icono-atras" onclick="Swal.close()">
                    <i>
                        <img width="40" height="40" src="https://img.icons8.com/external-solid-adri-ansyah/64/FAB005/external-ui-basic-ui-solid-adri-ansyah-26.png" alt="icono volver"/>
                    </i>
                </span>
                <h1 class="titulo">Detalles</h1>
            </div>

            <div class="modal-cuerpo">
                <div class="row">
                    <div class="col">
                        <!-- Datos del docente y evento -->
                        <label>Docente:</label>
                        <input type="text" value="${eventoData.usuario?.name ?? 'N/A'}" disabled>

                        <label>Institución:</label>
                        <input type="text" value="${eventoData.institucion?.nombre ?? ''}" disabled>

                        <label>SubÁrea:</label>
                        <input type="text" value="${eventoData.subarea?.nombre ?? ''}" disabled>

                        <label>Sección:</label>
                        <input type="text" value="${eventoData.seccion?.nombre ?? ''}" disabled>

                        <label>Especialidad:</label>
                        <input type="text" value="${eventoData.subarea?.especialidad?.nombre ?? ''}" disabled>
                    </div>

                    <div class="col">
                        <!-- Datos de fecha, hora, prioridad y estado -->
                        <label>Fecha:</label>
                        <input type="text" value="${new Date(eventoData.fecha).toLocaleDateString('es-ES')}" disabled>
                        
                        <label>Hora:</label>
                        <input type="text" value="${new Date(eventoData.hora_envio).toLocaleTimeString('es-ES', {hour: '2-digit', minute: '2-digit'})}" disabled>
                        
                        <label>Recinto:</label>
                        <input type="text" value="${eventoData.horario?.recinto?.nombre ?? ''}" disabled>

                        <label>Prioridad:</label>
                        <input type="text" value="${eventoData.prioridad?.charAt(0).toUpperCase() + eventoData.prioridad?.slice(1)}" disabled>

                        <label>Estado:</label>
                        <select class="form-select mb-3" id="estadoInputProfesor">
                            <option value="en_espera" ${eventoData.estado?.trim() === 'en_espera' ? 'selected' : ''}>En espera</option>
                            <option value="en_proceso" ${eventoData.estado?.trim() === 'en_proceso' ? 'selected' : ''}>En proceso</option>
                            <option value="completado" ${eventoData.estado?.trim() === 'completado' ? 'selected' : ''}>Completado</option>
                        </select>
                    </div>
                </div>

                <div class="observaciones mt-3">
                    <label>Observaciones:</label>
                    <textarea disabled>${eventoData.observacion || ''}</textarea>
                </div>

                <!-- Botón guardar cambios centrado y pequeño -->
                <div class="mt-4 d-flex justify-content-center">
                    <button type="button" class="btn btn-primary px-4 py-2" style="background-color:#134496; min-width:150px;" onclick="guardarCambiosProfesor(${eventoData.id})">
                        <i class="bi bi-save me-2"></i>Guardar cambios
                    </button>
                </div>
            </div>
        </div>
        `,
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

// Función auxiliar para mostrar modal con datos
function mostrarModalConDatos(eventoData) {
    Swal.fire({
        html: `
        <div class="modal-contenido">
            <div class="modal-encabezado">
                <span class="icono-atras" onclick="Swal.close()">
                    <i>
                        <img width="40" height="40" src="https://img.icons8.com/external-solid-adri-ansyah/64/FAB005/external-ui-basic-ui-solid-adri-ansyah-26.png" alt="icono volver"/>
                    </i>
                </span>
                <h1 class="titulo">Detalles</h1>
            </div>

            <div class="modal-cuerpo">
                <div class="row">
                    <div class="col">
                        <!-- Datos del docente y evento -->
                        <label>Docente:</label>
                        <input type="text" value="${eventoData.usuario?.name ?? 'N/A'}" disabled>

                        <label>Institución:</label>
                        <input type="text" value="${eventoData.institucion?.nombre ?? ''}" disabled>

                        <label>SubÁrea:</label>
                        <input type="text" value="${eventoData.subarea?.nombre ?? ''}" disabled>

                        <label>Sección:</label>
                        <input type="text" value="${eventoData.seccion?.nombre ?? ''}" disabled>

                        <label>Especialidad:</label>
                        <input type="text" value="${eventoData.subarea?.especialidad?.nombre ?? ''}" disabled>
                    </div>

                    <div class="col">
                        <!-- Datos de fecha, hora, prioridad y estado -->
                        <label>Fecha:</label>
                        <input type="text" value="${new Date(eventoData.fecha).toLocaleDateString('es-ES')}" disabled>
                        
                        <label>Hora:</label>
                        <input type="text" value="${new Date(eventoData.hora_envio).toLocaleTimeString('es-ES', {hour: '2-digit', minute: '2-digit'})}" disabled>
                        
                        <label>Recinto:</label>
                        <input type="text" value="${eventoData.horario?.recinto?.nombre ?? ''}" disabled>

                        <label>Prioridad:</label>
                        <input type="text" value="${eventoData.prioridad?.charAt(0).toUpperCase() + eventoData.prioridad?.slice(1)}" disabled>

                        <label>Estado:</label>
                        <select class="form-select mb-3" id="estadoInputProfesor">
                            <option value="en_espera" ${eventoData.estado?.trim() === 'en_espera' ? 'selected' : ''}>En espera</option>
                            <option value="en_proceso" ${eventoData.estado?.trim() === 'en_proceso' ? 'selected' : ''}>En proceso</option>
                            <option value="completado" ${eventoData.estado?.trim() === 'completado' ? 'selected' : ''}>Completado</option>
                        </select>
                    </div>
                </div>

                <div class="observaciones mt-3">
                    <label>Observaciones:</label>
                    <textarea disabled>${eventoData.observacion || ''}</textarea>
                </div>

                <!-- Botón guardar cambios centrado y pequeño -->
                <div class="mt-4 d-flex justify-content-center">
                    <button type="button" class="btn btn-primary px-4 py-2" style="background-color:#134496; min-width:150px;" onclick="guardarCambiosProfesor(${eventoData.id})">
                        <i class="bi bi-save me-2"></i>Guardar cambios
                    </button>
                </div>
            </div>
        </div>
        `,
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
async function guardarCambiosProfesor(id) {
    try {
        // Obtener el valor actual del select
        const estadoSelect = document.getElementById('estadoInputProfesor');
        const nuevoEstado = estadoSelect.value;
        
        console.log('Guardando cambios para evento:', id, 'nuevo estado:', nuevoEstado);
        
        const requestData = {
            estado: nuevoEstado
        };

        const response = await fetch(`/evento/${id}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(requestData)
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();
        console.log('Respuesta del servidor:', result);

        if (result.success) {
            // Cerrar el modal primero
            Swal.close();
            
            // Mostrar notificación de éxito
            Swal.fire({
                icon: 'success',
                title: 'Estado actualizado',
                text: 'El estado del evento se ha actualizado correctamente.',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2500
            });
            
            // Actualizar solo el estado en la tabla sin recargar todo
            console.log('Actualizando estado en tabla a:', nuevoEstado);
            updateEstadoEnTabla(id, nuevoEstado);
        } else {
            throw new Error(result.message || 'Error al actualizar el estado');
        }

    } catch (error) {
        console.error('Error completo:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message || 'Error al procesar la solicitud'
        });
    }
}

// Función para actualizar solo el estado en la tabla
function updateEstadoEnTabla(eventoId, nuevoEstado) {
    console.log('Actualizando estado en tabla:', eventoId, 'nuevo estado:', nuevoEstado);
    
    // Buscar la fila del evento en la tabla
    const eventosRows = document.querySelectorAll('.record-row');
    
    eventosRows.forEach(row => {
        const botonEditar = row.querySelector('button[onclick*="' + eventoId + '"]');
        if (botonEditar) {
            console.log('Fila encontrada para evento:', eventoId);
            
            // Encontrar el div del estado en esta fila
            const estadoDiv = row.querySelector('[data-label="Estado"]');
            if (estadoDiv) {
                console.log('Estado div encontrado, actualizando a:', nuevoEstado);
                
                // Actualizar el contenido del estado con los nuevos badges
                let estadoBadge = '';
                switch(nuevoEstado) {
                    case 'en_espera':
                        estadoBadge = '<span class="badge estado-en-espera" style="background-color: #ffc107 !important; color: #000 !important;"><i class="bi bi-clock-fill me-1" style="color: #fff !important; font-size: 0.7rem !important;"></i>En Espera</span>';
                        break;
                    case 'en_proceso':
                        estadoBadge = '<span class="badge estado-en-proceso" style="background-color: #17a2b8 !important; color: #fff !important;"><i class="bi bi-gear-fill me-1" style="font-size: 0.7rem !important;"></i>En Proceso</span>';
                        break;
                    case 'completado':
                        estadoBadge = '<span class="badge estado-completado" style="background-color: #28a745 !important; color: #fff !important;"><i class="bi bi-check-circle-fill me-1" style="font-size: 0.7rem !important;"></i>Completado</span>';
                        break;
                    default:
                        estadoBadge = '<span class="badge bg-secondary text-white" style="background-color: #6c757d !important; color: #fff !important;"><i class="bi bi-question-circle me-1" style="font-size: 0.7rem !important;"></i>' + nuevoEstado.charAt(0).toUpperCase() + nuevoEstado.slice(1) + '</span>';
                }
                
                console.log('Aplicando badge:', estadoBadge);
                estadoDiv.innerHTML = estadoBadge;
                
                console.log('Estado actualizado correctamente en la tabla');
            } else {
                console.log('No se encontró el div del estado');
            }
        }
    });
}

// Limpiar intervalo cuando se abandona la página
window.addEventListener('beforeunload', () => {
    clearInterval(intervalId);
});

async function guardarEstado(id) {
    const nuevoEstado = document.getElementById(`estado-${id}`).value;
    
    try {
        const response = await fetch(`/evento/${id}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
            body: JSON.stringify({ estado: nuevoEstado })
        });
        if (!response.ok) throw new Error('No se pudo guardar el estado');
        const data = await response.json();
        console.log('Respuesta backend:', data); // <-- Depuración
        if (data.success) {
            Swal.fire({
                title: '¡Guardado!',
                text: 'El estado del evento se actualizó correctamente.',
                icon: 'success',
            }).then(() => {
                window.location.reload(); // Recarga toda la página
            });
        } else {
            Swal.fire('Error', data.message || 'No se pudo guardar el estado.', 'error');
        }
    } catch (error) {
        console.log('Error en fetch:', error); // <-- Depuración
        Swal.fire('Error', error.message, 'error');
    }
}

// Variables para filtros
let filtrosActivos = {
    recinto: '{{ request("recinto") }}',
    orden: '{{ request("orden", "desc") }}',
    busqueda: '{{ request("busqueda") }}'
};

// Inicializar cuando el DOM y Bootstrap estén listos
document.addEventListener('DOMContentLoaded', function() {
    // Esperar a que Bootstrap esté disponible
    const checkBootstrap = () => {
        if (typeof bootstrap !== 'undefined') {
            console.log('Bootstrap está disponible, inicializando filtros...');
            initializeFilters();
        } else {
            console.log('Esperando a Bootstrap...');
            setTimeout(checkBootstrap, 50);
        }
    };
    checkBootstrap();
});

function initializeFilters() {
    console.log('Inicializando filtros...');
    
    // Verificar que los elementos existen
    const filtrosDropdown = document.getElementById('filtrosDropdown');
    const inputBusqueda = document.getElementById('inputBusqueda');
    const busquedaForm = document.getElementById('busquedaForm');
    const limpiarFiltros = document.getElementById('limpiarFiltros');
    
    console.log('Elementos encontrados:', {
        filtrosDropdown: !!filtrosDropdown,
        inputBusqueda: !!inputBusqueda,
        busquedaForm: !!busquedaForm,
        limpiarFiltros: !!limpiarFiltros
    });
    
    // Event listeners para filtros del dropdown
    const filtroItems = document.querySelectorAll('.filtro-item');
    console.log('Filtro items encontrados:', filtroItems.length);
    
    filtroItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Filtro clickeado:', this.getAttribute('data-filtro'), this.getAttribute('data-tipo'));
            
            const filtro = this.getAttribute('data-filtro');
            const tipo = this.getAttribute('data-tipo');
            
            filtrosActivos[tipo] = filtro;
            aplicarFiltros();
        });
    });
    
    // Event listener para búsqueda
    if (inputBusqueda) {
        inputBusqueda.addEventListener('input', function() {
            filtrosActivos.busqueda = this.value;
            aplicarFiltros();
        });
    }
    
    // Event listener para el formulario de búsqueda
    if (busquedaForm) {
        busquedaForm.addEventListener('submit', function(e) {
            e.preventDefault();
            filtrosActivos.busqueda = document.getElementById('inputBusqueda').value;
            aplicarFiltros();
        });
    }
    
    // Event listener para limpiar filtros
    if (limpiarFiltros) {
        limpiarFiltros.addEventListener('click', function(e) {
            e.preventDefault();
            limpiarTodosFiltros();
        });
    }
}

function limpiarTodosFiltros() {
    filtrosActivos = {
        recinto: '',
        orden: 'desc',
        busqueda: ''
    };
    
    document.getElementById('inputBusqueda').value = '';
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
    if (filtrosActivos.recinto) params.append('recinto', filtrosActivos.recinto);
    if (filtrosActivos.orden) params.append('orden', filtrosActivos.orden);
    if (filtrosActivos.busqueda) params.append('busqueda', filtrosActivos.busqueda);
    
    // Hacer petición AJAX
    fetch(`{{ route('evento.index_soporte') }}?${params.toString()}`, {
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
    
    const recintoActual = document.getElementById('recintoActual');
    let recintoText = '';
    
    if (filtrosActivos.recinto) {
        // Obtener el nombre del recinto seleccionado del dropdown
        const recintoElement = document.querySelector(`[data-filtro="${filtrosActivos.recinto}"][data-tipo="recinto"]`);
        if (recintoElement) {
            recintoText = ` filtrados por: ${recintoElement.textContent.trim()}`;
        }
    }
    
    recintoActual.textContent = recintoText;
}
</script>
@endpush
