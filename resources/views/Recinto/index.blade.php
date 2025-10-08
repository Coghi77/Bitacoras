@extends('Template-administrador')


@section('title', 'Gestión de Recintos')


@section('content')
<div class="wrapper">
    <div class="main-content">
        {{-- Búsqueda + botón agregar + filtros activos/inactivos --}}
        <div class="row align-items-end mb-4">
            <div class="search-bar-wrapper mb-4 d-flex align-items-center">
                <div class="search-bar flex-grow-1">
                    <form id="busquedaForm" method="GET" action="{{ route('recinto.index') }}" class="w-100 position-relative">
                        <span class="search-icon">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" class="form-control" placeholder="Buscar recinto..." name="busquedaRecinto" value="{{ request('busquedaRecinto') }}" id="inputBusqueda" autocomplete="off">
                        @if(request('busquedaRecinto'))
                        <button type="button" class="btn btn-outline-secondary border-0 position-absolute end-0 top-50 translate-middle-y me-2" id="limpiarBusqueda" title="Limpiar búsqueda" style="background: transparent;">
                            <i class="bi bi-x-circle"></i>
                        </button>
                        @endif
                        @if(request('inactivos'))
                            <input type="hidden" name="inactivos" value="1">
                        @endif
                    </form>
                </div>
                @can('create_recintos')
                    <button class="btn btn-primary rounded-pill px-4 d-flex align-items-center ms-3 btn-agregar"
                        data-bs-toggle="modal" data-bs-target="#modalAgregarRecinto"
                        title="Agregar Recinto" style="background-color: #134496; font-size: 1.2rem; @if(Auth::user() && Auth::user()->hasRole('director')) display: none; @endif">
                        Agregar <i class="bi bi-plus-circle ms-2"></i>
                    </button>
                @endcan
            </div>
        </div>
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        <div class="mb-3">
            <a href="{{ route('recinto.index', ['inactivos' => 1]) }}" class="btn btn-warning me-2">
                Mostrar inactivos
            </a>
            <a href="{{ route('recinto.index') }}" class="btn btn-primary me-2">
                Mostrar activos
            </a>
        </div>
     
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
        @foreach($recintos as $recinto)
            @if ($recinto->condicion == 1)
                @if(!request('tipo') || $recinto->tipo == request('tipo'))
                    <div class="col d-flex">
                        <div class="card flex-fill h-100 border rounded-4 p-2" style="font-size: 0.92em; min-width: 0;">
                            <div class="card-body pb-2 p-2">
                                <h5 class="card-title fw-bold mb-2" style="font-size:1em;">{{ $recinto->nombre }}</h5>
                                <div class="mb-1 d-flex align-items-center gap-2">
                                    <span class="text-secondary" style="font-size:0.93em;">Estado:</span>
                                    <span class="badge px-2 py-1 rounded-pill text-dark"
                                            style="font-size:0.9em; background-color: {{ $recinto->estadoRecinto ? $recinto->estadoRecinto->color : '#ccc' }};">
                                            {{ $recinto->estadoRecinto ? $recinto->estadoRecinto->nombre : 'Sin estado' }}
                                    </span>
                                </div>
                                <div class="mb-1 text-secondary" style="font-size:0.93em;">
                                <i class="fas fa-key me-1"></i>Número de llave: {{ $recinto->llave->nombre}}
                                </div>
                                <div class="mb-1 text-secondary" style="font-size:0.93em;">
                                <i class="fas fa-building me-1"></i>Instituciones: 
                                @if($recinto->instituciones->count() > 0)
                                    {{ $recinto->instituciones->pluck('nombre')->join(', ') }}
                                @else
                                    Sin instituciones
                                @endif
                                </div>
                                <div class="mb-1 text-secondary" style="font-size:0.93em;">
                                <i class="fas fa-building me-1"></i>Tipo: {{ $recinto->tipoRecinto ? $recinto->tipoRecinto->nombre : 'Sin tipo' }}                                
                                </div>
                                </div>
                                <div class="card-footer bg-white border-0 pt-0 d-flex flex-row justify-content-end align-items-stretch gap-2 p-2">
                                <!--<button class="btn btn-outline-info btn-sm rounded-5 d-flex align-items-center justify-content-center"
                                data-bs-toggle="modal" data-bs-target="#modalDevolucionLlave-{{ $recinto->id }}">
                                <i class="bi bi-key"></i>
                                </button>-->
                                <button class="btn btn-outline-secondary btn-sm rounded-5 d-flex align-items-center justify-content-center ms-0 ms-sm-2"
                                data-bs-toggle="modal" data-bs-target="#modalEditarRecinto-{{ $recinto->id }}">
                                <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('recinto.destroy', $recinto->id) }}" method="POST" >
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-outline-danger btn-sm rounded-5 ms-2" data-bs-toggle="modal" data-bs-target="#modalConfirmacionEliminar-{{ $recinto->id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <div class="col d-flex">
                    <div class="card flex-fill h-100 border rounded-4 p-2" style="font-size: 0.92em; min-width: 0;">
                        <div class="card-body pb-2 p-2">
                            <div class="d-flex align-items-center mb-2 gap-2 flex-wrap">
                            <span class="badge bg-light text-dark border border-secondary d-flex align-items-center gap-1 px-2 py-1 rounded-pill" style="font-size:0.9em;">
                            {{ ucfirst($recinto->tipo) }}
                            </span>
                            <span class="badge px-2 py-1 rounded-pill text-dark"
                                    style="font-size:0.9em; background-color: {{ $recinto->estadoRecinto ? $recinto->estadoRecinto->color : '#ccc' }};">
                                    {{ $recinto->estadoRecinto ? $recinto->estadoRecinto->nombre : 'Sin estado' }}
                            </span>
                            </div>
                            <h5 class="card-title fw-bold mb-2" style="font-size:1em;">{{ $recinto->nombre }}</h5>
                            <div class="mb-1 text-secondary" style="font-size:0.93em;">
                            <i class="fas fa-key me-1"></i>Número de llave: {{ $recinto->llave->nombre}}
                            </div>
                            <div class="mb-1 text-secondary" style="font-size:0.93em;">
                            <i class="fas fa-building me-1"></i>Instituciones: 
                            @if($recinto->instituciones->count() > 0)
                                {{ $recinto->instituciones->pluck('nombre')->join(', ') }}
                            @else
                                Sin instituciones
                            @endif
                            </div>
                            <div class="mb-1 text-secondary" style="font-size:0.93em;">
                            <i class="fas fa-building me-1"></i>Tipo: {{ $recinto->tipoRecinto ? $recinto->tipoRecinto->nombre : 'Sin tipo' }}                                
                            </div>
                            </div>
                            <div class="card-footer bg-white border-0 pt-0 d-flex flex-row justify-content-end align-items-stretch gap-2 p-2">
                            <!--<button class="btn btn-outline-info btn-sm rounded-5 d-flex align-items-center justify-content-center"
                            data-bs-toggle="modal" data-bs-target="#modalDevolucionLlave-{{ $recinto->id }}">
                            <i class="bi bi-key"></i>
                            </button>-->

                            <form action="{{ route('recinto.destroy', $recinto->id) }}" method="POST" >
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-outline-danger btn-sm rounded-5 ms-2" data-bs-toggle="modal" data-bs-target="#modalConfirmacionReactivar-{{ $recinto->id }}">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Modal de eliminacion de recinto --}}
            <div class="modal fade" id="modalConfirmacionEliminar-{{ $recinto->id }}" tabindex="-1" aria-labelledby="modalRecintoEliminarLabel-{{ $recinto->id }}"
            aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content custom-modal">
                        <div class="modal-body text-center">
                            <div class="icon-container">
                                <div class="circle-icon">
                                <i class="bi bi-exclamation-circle"></i>
                                </div>
                            </div>
                            <p class="modal-text">¿Desea Eliminar el Recinto?</p>
                            <div class="btn-group-custom">
                                <form action="{{ route('recinto.destroy', ['recinto' => $recinto->id]) }}" method="post">
                                    @method('DELETE')
                                    @csrf
                                    <button type="submit" class="btn btn-custom {{ $recinto->condicion == 1 }}">Sí</button>
                                    <button type="button" class="btn btn-custom" data-bs-dismiss="modal">No</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal de reactivacion de recinto --}}
            <div class="modal fade" id="modalConfirmacionReactivar-{{ $recinto->id }}" tabindex="-1" aria-labelledby="modalRecintoReactivarLabel-{{ $recinto->id }}"
            aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content custom-modal">
                        <div class="modal-body text-center">
                            <div class="icon-container">
                                <div class="circle-icon">
                                <i class="bi bi-exclamation-circle"></i>
                                </div>
                            </div>
                            <p class="modal-text">¿Desea reactivar el Recinto?</p>
                            <div class="btn-group-custom">
                                <form action="{{ route('recinto.destroy', ['recinto' => $recinto->id]) }}" method="post">
                                    @method('DELETE')
                                    @csrf
                                    <button type="submit" class="btn btn-custom {{ $recinto->condicion == 1 }}">Sí</button>
                                    <button type="button" class="btn btn-custom" data-bs-dismiss="modal">No</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Editar Recinto -->
           
            <div class="modal fade" id="modalEditarRecinto-{{ $recinto->id }}" tabindex="-1" aria-labelledby="modalEditarRecintoLabel-{{ $recinto->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 rounded-0" id="modalEditarRecintoContent">
                <div class="modal-header rounded-0 custom-header">
                    <button type="button" class="btn p-0 me-3" data-bs-dismiss="modal" aria-label="Volver" style="color: #FFD600; font-size: 1.5rem; background: none; border: none;">
                    <span class="icono-atras">
                        <i><img width="40" height="40" src="https://img.icons8.com/external-solid-adri-ansyah/64/FAB005/external-ui-basic-ui-solid-adri-ansyah-26.png" alt="external-ui-basic-ui-solid-adri-ansyah-26"/></i>
                    </span>
                    </button>
                    <h3 class="flex-grow-1">Editar Recinto</h3>
                </div>
                <div class="modal-body pb-0" style="border-bottom: 8px solid #003366;">
                    <form id="formEditarRecinto-{{ $recinto->id }}" action="{{ route('recinto.update', $recinto->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                   
                    <div class="mb-3">
                        <label for="nombreRecinto-{{ $recinto->id }}" class="form-label mb-1">Nombre del Recinto</label>
                        <input type="text" class="form-control" id="nombreRecinto-{{ $recinto->id }}" name="nombre" value="{{ $recinto->nombre }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="tipoRecinto-{{ $recinto->id }}" class="form-label mb-1">Tipo de Recinto</label>
                        <select data-size="4" title="Seleccione un Tipo de Recinto" data-live-search="true" name="tipoRecinto_id" id="tipoRecinto_id" class="form-control selectpicker show-tick">
                            @if(isset($tiposRecinto))
                                @foreach ($tiposRecinto as $tipoRecinto)
                                    <option value="{{$tipoRecinto->id}}"
                                        {{ (isset($recinto) && $recinto->tipoRecinto_id == $tipoRecinto->id) || old('tipoRecinto_id') == $tipoRecinto->id ? 'selected' : '' }}>
                                        {{$tipoRecinto->nombre}}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="estadoRecinto-{{ $recinto->id }}" class="form-label mb-1">Estado del Recinto</label>
                        <select data-size="4" title="Seleccione un Estado de Recinto" data-live-search="true" name="estadoRecinto_id" id="editarEstadoRecinto" class="form-control selectpicker show-tick">
                            @if(isset($estadosRecinto))
                                @foreach ($estadosRecinto as $estadoRecinto)
                                    <option value="{{$estadoRecinto->id}}"
                                        {{ (isset($recinto) && $recinto->estadoRecinto_id == $estadoRecinto->id) || old('estadoRecinto_id') == $estadoRecinto->id ? 'selected' : '' }}>
                                        {{$estadoRecinto->nombre}}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="llaveRecinto-{{ $recinto->id }}" class="form-label mb-1">Número de Llave</label>
                        <select data-size="4" title="Seleccione una Llave" data-live-search="true" name="llave_id" id="llave_id" class="form-control selectpicker show-tick">
                            @if(isset($llaves))
                                @foreach ($llaves as $llave)
                                    <option value="{{$llave->id}}"
                                        {{ (isset($recinto) && $recinto->llave_id == $llave->id) || old('llave_id') == $llave->id ? 'selected' : '' }}>
                                        {{$llave->nombre}}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="institucionRecinto-{{ $recinto->id }}" class="form-label mb-1">Instituciones</label>
                        
                        <div id="instituciones-editar-{{ $recinto->id }}">
                            <div class="input-group dynamic-group">
                                <select id="selectInstitucionEditar-{{ $recinto->id }}" class="form-select">
                                    <option value="">Seleccione una institución</option>
                                    @foreach ($instituciones as $institucion)
                                        <option value="{{ $institucion->id }}" data-nombre="{{ $institucion->nombre }}">{{ $institucion->nombre }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-success d-flex align-items-center justify-content-center" onclick="agregarInstitucionEditar('{{ $recinto->id }}')" style="height: 9%; min-width: 38px; padding: 0;">
                                    <i class="bi bi-plus" style="height: 49px;"></i>
                                </button>
                            </div>

                            <!-- Instituciones seleccionadas -->
                            <div id="institucionesSeleccionadasEditar-{{ $recinto->id }}" class="mt-2">
                                @foreach($recinto->instituciones as $institucion)
                                    <div class="input-group mb-2">
                                        <input type="hidden" name="institucion_id[]" value="{{ $institucion->id }}">
                                        <input type="text" class="form-control" value="{{ $institucion->nombre }}" readonly>
                                        <button type="button" class="btn btn-outline-danger" onclick="quitarInstitucionEditar('{{ $institucion->id }}', '{{ $recinto->id }}')">
                                            <i class="bi bi-dash"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Validación -->
                            <div id="mensajeValidacionInstitucionEditar-{{ $recinto->id }}" class="alert alert-danger d-none mt-2" role="alert">
                                <i class="bi bi-exclamation-triangle"></i> <span id="textoMensajeInstitucionEditar-{{ $recinto->id }}"></span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-4 mb-2">
                        <button type="button" class="btn btn-outline-danger rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-guardar rounded-pill px-4">Guardar</button>
                    </div>
                    </form>
                </div>
                </div>
            </div>
            </div>
        @endforeach
            <!-- Modal Agregar Recinto -->
            {{-- Mostrar errores de validación --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <div class="modal fade" id="modalAgregarRecinto" tabindex="-1" aria-labelledby="modalAgregarRecintoLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 rounded-0" id="modalAgregarRecintoContent">
                <div class="modal-header rounded-0 custom-header">
                    <button type="button" class="btn p-0 me-3" data-bs-dismiss="modal" aria-label="Volver" style="color: #FFD600; font-size: 1.5rem; background: none; border: none;">
                    <span class="icono-atras">
                        <i><img width="40" height="40" src="https://img.icons8.com/external-solid-adri-ansyah/64/FAB005/external-ui-basic-ui-solid-adri-ansyah-26.png" alt="external-ui-basic-ui-solid-adri-ansyah-26"/></i>
                    </span>
                    </button>
                    <h3 class="flex-grow-1">Crear Nuevo Recinto</h3>
                </div>
                <div class="modal-body pb-0" style="border-bottom: 8px solid #003366;">
                    <form action="{{ route('recinto.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="nombreRecinto" class="form-label mb-1">Nombre del Recinto</label>
                            <input type="text" class="form-control" id="nombreRecinto" name="nombre" placeholder="Nombre del Recinto" required>
                        </div>
                        <div class="mb-3">
                            <label for="tipoRecinto" class="form-label mb-1">Tipo de Recinto</label>


                            <select data-size="4" title="Seleccione un Tipo de Recinto" data-live-search="true" name="tipoRecinto_id" id="tipoRecinto_id" class="form-control selectpicker show-tick" required>
                                <option value="">Seleccione un Tipo de Recinto</option>
                                @foreach ($tiposRecinto as $tipoRecinto)
                                    <option value="{{$tipoRecinto->id}}" {{ old('tipoRecinto_id') == $tipoRecinto->id ? 'selected' : '' }}>{{$tipoRecinto->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                       
                        <div class="mb-3">
                            <label for="estadoRecinto" class="form-label mb-1">Estado del Recinto</label>


                            <select data-size="4" title="Seleccione un Estado de Recinto" data-live-search="true" name="estadoRecinto_id" id="estadoRecinto_id" class="form-control selectpicker show-tick" required>
                                <option value="">Seleccione un Estado de Recinto</option>
                                @foreach ($estadosRecinto as $estadoRecinto)
                                    <option value="{{$estadoRecinto->id}}" {{ old('estadoRecinto_id') == $estadoRecinto->id ? 'selected' : '' }}>{{$estadoRecinto->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="llaveRecinto" class="form-label mb-1">Número de Llave</label>
                           
                            <select data-size="4" title="Seleccione una Llave" data-live-search="true" name="llave_id" id="llave_id" class="form-control selectpicker show-tick" required>
                                <option value="">Seleccione una Llave</option>
                                @foreach ($llaves as $llave)
                                    <option value="{{$llave->id}}" {{ old('llave_id') == $llave->id ? 'selected' : '' }}>{{$llave->nombre}}</option>
                                @endforeach
                            </select>
                        </div>


 
                        <div class="mb-3">
    <label class="form-label fw-bold">Instituciones</label>
    
    @if(session('modal_crear') && $errors->has('institucion_id'))
        <div class="text-danger small mb-2">
            {{ $errors->first('institucion_id') }}
            <br><small><i class="bi bi-info-circle"></i> Debe asignar al menos una institución.</small>
        </div>
    @endif

    <div id="instituciones">
        <div class="input-group dynamic-group">
            <select id="selectInstitucion" class="form-select">
                <option value="">Seleccione una institución</option>
                @foreach ($instituciones as $institucion)
                    <option value="{{ $institucion->id }}" data-nombre="{{ $institucion->nombre }}">{{ $institucion->nombre }}</option>
                @endforeach
            </select>
            <button type="button" class="btn btn-success d-flex align-items-center justify-content-center" onclick="agregarInstitucion()" style="height: 9%; min-width: 38px; padding: 0;">
                <i class="bi bi-plus" style="height: 49px;"></i>
            </button>
        </div>

        <!-- Instituciones seleccionadas -->
        <div id="institucionesSeleccionadas" class="mt-2"></div>

        <!-- Validación -->
        <div id="mensajeValidacionInstitucion" class="alert alert-danger d-none mt-2" role="alert">
            <i class="bi bi-exclamation-triangle"></i> <span id="textoMensajeInstitucion"></span>
        </div>
    </div>
</div>
                        <div class="d-flex justify-content-end gap-2 mt-4 mb-2">
                            <button type="button" class="btn btn-outline-danger rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-guardar rounded-pill px-4">Crear</button>
                        </div>
                    </form>
                </div>
                </div>
            </div>
            </div>

            <!-- Modal de Validación de Errores -->
            <div class="modal fade" id="modalValidacionErrores" tabindex="-1" aria-labelledby="modalValidacionErroresLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
                        <div class="modal-body text-center p-4">
                            <!-- Icono de error -->
                            <div class="mb-3">
                                <div class="error-icon-container mx-auto" style="width: 80px; height: 80px; background: linear-gradient(135deg, #ff6b6b 0%, #c92a2a 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 15px rgba(201, 42, 42, 0.3);">
                                    <i class="bi bi-exclamation-triangle" style="font-size: 2.5rem; color: white;"></i>
                                </div>
                            </div>

                            <!-- Título -->
                            <h4 class="mb-3" style="color: #2c3e50; font-weight: 600;">
                                <i class="bi bi-shield-x me-2"></i>Error de Validación
                            </h4>

                            <!-- Lista de errores -->
                            <div class="alert alert-danger text-start mx-auto" style="max-width: 90%; border-radius: 12px; background-color: #ffe3e3; border: 2px solid #ff6b6b;">
                                <div class="d-flex align-items-start mb-2">
                                    <i class="bi bi-info-circle-fill me-2 mt-1" style="color: #c92a2a;"></i>
                                    <strong style="color: #c92a2a;">Por favor corrija los siguientes errores:</strong>
                                </div>
                                <ul class="mb-0 ps-4" id="listaErroresValidacion" style="color: #721c24;">
                                    <!-- Los errores se insertarán aquí -->
                                </ul>
                            </div>

                            <!-- Botón -->
                            <button type="button" class="btn btn-primary rounded-pill px-5 py-2 mt-3" data-bs-dismiss="modal" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; font-weight: 600; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3); transition: transform 0.2s;">
                                <i class="bi bi-check-circle me-2"></i>Entendido
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal Devolución de Llave -->
@foreach($recintos as $recinto)
<div class="modal fade" id="modalDevolucionLlave-{{ $recinto->id }}" tabindex="-1" aria-labelledby="modalDevolucionLlaveLabel-{{ $recinto->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-0">
            <div class="modal-header rounded-0 custom-header">
                <button type="button" class="btn p-0 me-3" data-bs-dismiss="modal" aria-label="Volver" style="color: #FFD600; font-size: 1.5rem; background: none; border: none;">
                    <span class="icono-atras">
                        <i><img width="40" height="40" src="https://img.icons8.com/external-solid-adri-ansyah/64/FAB005/external-ui-basic-ui-solid-adri-ansyah-26.png" alt="external-ui-basic-ui-solid-adri-ansyah-26"/></i>
                    </span>
                </button>
                <h3 class="flex-grow-1">Devolución de Llave</h3>
            </div>
            <div class="modal-body pb-0 text-center" style="border-bottom: 8px solid #003366;">
                <div class="mb-4">
                    <h5>{{ $recinto->nombre }}</h5>
                    <p class="text-secondary">Número de Llave: <strong>{{ $recinto->llave->nombre }}</strong></p>
                </div>
               
                <div id="qrCode-{{ $recinto->id }}" class="mb-4" style="display: none;">
                    <div class="d-flex justify-content-center">
                        <div id="qrCodeContainer-{{ $recinto->id }}"></div>
                    </div>
                    <p class="mt-2 text-success">¡Código QR generado exitosamente!</p>
                </div>


                <div class="d-flex justify-content-center gap-2 mt-4 mb-2">
                    <button type="button" class="btn btn-outline-danger rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success rounded-pill px-4" onclick="generarQRDevolucion({{ $recinto->id }}, '{{ $recinto->llave->nombre }}', '{{ $recinto->nombre }}')">
                        Realizar Devolución
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach


<style>
/* Remover flechas azules de los botones de quitar institución */
.btn-outline-danger .bi-dash::before {
    color: #dc3545 !important;
}

/* Estilo para las instituciones seleccionadas */
#institucionesSeleccionadas .input-group {
    margin-bottom: 0.5rem;
}

#institucionesSeleccionadas .form-control {
    background-color: #f8f9fa;
}

/* Quitar flechas del select de instituciones */
#selectInstitucion {
    background-image: none !important;
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    appearance: none !important;
}

/* Quitar flechas de cualquier select en el formulario de instituciones */
#instituciones select {
    background-image: none !important;
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    appearance: none !important;
}

/* Personalizar el select para que se vea bien sin flechas */
#selectInstitucion {
    background-color: white;
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
    padding: 0.375rem 0.75rem;
    color: #495057;
}

#selectInstitucion:focus {
    border-color: #86b7fe;
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

/* Estilos para el modal de validación de errores */
#modalValidacionErrores .modal-content {
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        transform: translateY(-50px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

#modalValidacionErrores .error-icon-container {
    animation: bounce 0.6s ease-out;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-10px);
    }
    60% {
        transform: translateY(-5px);
    }
}

#modalValidacionErrores .btn-primary:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4) !important;
}

#modalValidacionErrores .btn-primary:active {
    transform: translateY(0) !important;
}

#listaErroresValidacion li {
    line-height: 1.6;
    font-size: 0.95rem;
}
</style>

<style>
/* Quitar flechas del select de instituciones en crear */
#selectInstitucion {
    background-image: none !important;
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    appearance: none !important;
}

/* Quitar flechas del select de instituciones en editar */
[id^="selectInstitucionEditar-"] {
    background-image: none !important;
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    appearance: none !important;
}

/* Personalizar los selects para que se vean bien sin flechas */
#selectInstitucion,
[id^="selectInstitucionEditar-"] {
    background-color: white;
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
    padding: 0.375rem 0.75rem;
    color: #495057;
}

#selectInstitucion:focus,
[id^="selectInstitucionEditar-"]:focus {
    border-color: #86b7fe;
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}
</style>

<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
<script>
// Variables para controlar las instituciones seleccionadas
let institucionesSeleccionadas = [];

// Función para agregar una institución
function agregarInstitucion() {
    const select = document.getElementById('selectInstitucion');
    
    if (!select) {
        return;
    }
    
    const institucionId = select.value;
    const selectedOption = select.options[select.selectedIndex];
    const institucionNombre = selectedOption ? selectedOption.getAttribute('data-nombre') : null;
    
    // Validar que se haya seleccionado una institución
    if (!institucionId) {
        mostrarMensajeValidacion('Por favor seleccione una institución');
        return;
    }
    
    // Verificar que no esté ya agregada
    if (institucionesSeleccionadas.some(inst => inst.id === institucionId)) {
        mostrarMensajeValidacion('Esta institución ya ha sido agregada');
        return;
    }
    
    // Agregar a la lista
    institucionesSeleccionadas.push({
        id: institucionId,
        nombre: institucionNombre
    });
    
    // Actualizar la vista
    actualizarVista();
    
    // Limpiar selección
    select.value = '';
    
    // Ocultar mensaje de validación
    ocultarMensajeValidacion();
}

// Función para quitar una institución
function quitarInstitucion(institucionId) {
    institucionesSeleccionadas = institucionesSeleccionadas.filter(inst => inst.id !== institucionId);
    actualizarVista();
}

// Función para actualizar la vista de instituciones seleccionadas
function actualizarVista() {
    const container = document.getElementById('institucionesSeleccionadas');
    
    if (!container) {
        return;
    }
    
    container.innerHTML = '';
    
    institucionesSeleccionadas.forEach(institucion => {
        const div = document.createElement('div');
        div.className = 'input-group mb-2';
        div.innerHTML = `
            <input type="hidden" name="institucion_id[]" value="${institucion.id}">
            <input type="text" class="form-control" value="${institucion.nombre}" readonly>
            <button type="button" class="btn btn-outline-danger" onclick="quitarInstitucion('${institucion.id}')">
                <i class="bi bi-dash"></i>
            </button>
        `;
        container.appendChild(div);
    });
}

// Función para mostrar mensaje de validación
function mostrarMensajeValidacion(mensaje) {
    const mensajeDiv = document.getElementById('mensajeValidacionInstitucion');
    const textoSpan = document.getElementById('textoMensajeInstitucion');
    textoSpan.textContent = mensaje;
    mensajeDiv.classList.remove('d-none');
}

// Función para ocultar mensaje de validación
function ocultarMensajeValidacion() {
    const mensajeDiv = document.getElementById('mensajeValidacionInstitucion');
    mensajeDiv.classList.add('d-none');
}

// Variables para controlar las instituciones seleccionadas en editar
let institucionesSeleccionadasEditar = {};

// Función para agregar una institución en modal de editar
function agregarInstitucionEditar(recintoId) {
    const select = document.getElementById(`selectInstitucionEditar-${recintoId}`);
    
    if (!select) {
        return;
    }
    
    const institucionId = select.value;
    const selectedOption = select.options[select.selectedIndex];
    const institucionNombre = selectedOption ? selectedOption.getAttribute('data-nombre') : null;
    
    // Validar que se haya seleccionado una institución
    if (!institucionId) {
        mostrarMensajeValidacionEditar('Por favor seleccione una institución', recintoId);
        return;
    }
    
    // Inicializar array para este recinto si no existe
    if (!institucionesSeleccionadasEditar[recintoId]) {
        institucionesSeleccionadasEditar[recintoId] = [];
    }
    
    // Verificar que no esté ya agregada
    if (institucionesSeleccionadasEditar[recintoId].some(inst => inst.id === institucionId)) {
        mostrarMensajeValidacionEditar('Esta institución ya ha sido agregada', recintoId);
        return;
    }
    
    // Verificar que no esté ya en el DOM
    const container = document.getElementById(`institucionesSeleccionadasEditar-${recintoId}`);
    const existingInputs = container.querySelectorAll('input[name="institucion_id[]"]');
    for (let input of existingInputs) {
        if (input.value === institucionId) {
            mostrarMensajeValidacionEditar('Esta institución ya ha sido agregada', recintoId);
            return;
        }
    }
    
    // Agregar a la lista
    institucionesSeleccionadasEditar[recintoId].push({
        id: institucionId,
        nombre: institucionNombre
    });
    
    // Actualizar la vista
    actualizarVistaEditar(recintoId);
    
    // Limpiar selección
    select.value = '';
    
    // Ocultar mensaje de validación
    ocultarMensajeValidacionEditar(recintoId);
}

// Función para quitar una institución en modal de editar
function quitarInstitucionEditar(institucionId, recintoId) {
    // Quitar del array
    if (institucionesSeleccionadasEditar[recintoId]) {
        institucionesSeleccionadasEditar[recintoId] = institucionesSeleccionadasEditar[recintoId].filter(inst => inst.id !== institucionId);
    }
    
    // Quitar del DOM
    const container = document.getElementById(`institucionesSeleccionadasEditar-${recintoId}`);
    const inputs = container.querySelectorAll('input[name="institucion_id[]"]');
    inputs.forEach(input => {
        if (input.value === institucionId) {
            input.parentElement.remove();
        }
    });
}

// Función para actualizar la vista de instituciones seleccionadas en editar
function actualizarVistaEditar(recintoId) {
    if (!institucionesSeleccionadasEditar[recintoId]) return;
    
    const container = document.getElementById(`institucionesSeleccionadasEditar-${recintoId}`);
    
    if (!container) {
        return;
    }
    
    institucionesSeleccionadasEditar[recintoId].forEach(institucion => {
        const div = document.createElement('div');
        div.className = 'input-group mb-2';
        div.innerHTML = `
            <input type="hidden" name="institucion_id[]" value="${institucion.id}">
            <input type="text" class="form-control" value="${institucion.nombre}" readonly>
            <button type="button" class="btn btn-outline-danger" onclick="quitarInstitucionEditar('${institucion.id}', '${recintoId}')">
                <i class="bi bi-dash"></i>
            </button>
        `;
        container.appendChild(div);
    });
    
    // Limpiar el array después de agregar al DOM
    institucionesSeleccionadasEditar[recintoId] = [];
}

// Función para mostrar mensaje de validación en editar
function mostrarMensajeValidacionEditar(mensaje, recintoId) {
    const mensajeDiv = document.getElementById(`mensajeValidacionInstitucionEditar-${recintoId}`);
    const textoSpan = document.getElementById(`textoMensajeInstitucionEditar-${recintoId}`);
    if (textoSpan && mensajeDiv) {
        textoSpan.textContent = mensaje;
        mensajeDiv.classList.remove('d-none');
    }
}

// Función para ocultar mensaje de validación en editar
function ocultarMensajeValidacionEditar(recintoId) {
    const mensajeDiv = document.getElementById(`mensajeValidacionInstitucionEditar-${recintoId}`);
    if (mensajeDiv) {
        mensajeDiv.classList.add('d-none');
    }
}

const inputBusqueda = document.getElementById('inputBusqueda');
const recintosList = document.getElementById('recintos-list');
const btnLimpiar = document.getElementById('limpiarBusqueda');


if (inputBusqueda && recintosList) {
    inputBusqueda.addEventListener('input', function() {
        const valor = inputBusqueda.value.trim().toLowerCase();
        const items = recintosList.querySelectorAll('.recinto-item');
        items.forEach(function(item) {
            const nombre = item.getAttribute('data-nombre');
            if (!valor || nombre.includes(valor)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });
}


if (btnLimpiar && inputBusqueda && recintosList) {
    btnLimpiar.addEventListener('click', function() {
        inputBusqueda.value = '';
        const items = recintosList.querySelectorAll('.recinto-item');
        items.forEach(function(item) {
            item.style.display = '';
        });
    });
}


function generarQRDevolucion(recintoId, numeroLlave, nombreRecinto) {
    const qrContainer = document.getElementById(`qrCodeContainer-${recintoId}`);
    const qrDiv = document.getElementById(`qrCode-${recintoId}`);
   
    // Limpiar contenedor previo
    qrContainer.innerHTML = '';
   
    // Datos para el QR
    const datosDevolucion = {
        tipo: 'devolucion_llave',
        recinto: nombreRecinto,
        llave: numeroLlave,
        fecha: new Date().toISOString(),
        id: recintoId
    };
   
    // Generar QR
    QRCode.toCanvas(JSON.stringify(datosDevolucion), {
        width: 200,
        height: 200,
        margin: 2,
    }, function (error, canvas) {
        if (error) {
            console.error(error);
            alert('Error al generar el código QR');
            return;
        }
       
        qrContainer.appendChild(canvas);
        qrDiv.style.display = 'block';
    });
}

// Mostrar modal de errores si hay errores de validación
document.addEventListener('DOMContentLoaded', function() {
    @if($errors->any())
        const errores = [
            @foreach($errors->all() as $error)
                "{{ $error }}",
            @endforeach
        ];
        
        if (errores.length > 0) {
            const listaErrores = document.getElementById('listaErroresValidacion');
            listaErrores.innerHTML = '';
            
            errores.forEach(error => {
                const li = document.createElement('li');
                li.textContent = error;
                li.style.marginBottom = '8px';
                listaErrores.appendChild(li);
            });
            
            const modalValidacion = new bootstrap.Modal(document.getElementById('modalValidacionErrores'));
            modalValidacion.show();
            
            @if(session('modal_crear'))
                // Si el error vino del modal de crear, volver a abrirlo cuando se cierre el modal de errores
                document.getElementById('modalValidacionErrores').addEventListener('hidden.bs.modal', function () {
                    const modalCrear = new bootstrap.Modal(document.getElementById('modalAgregarRecinto'));
                    modalCrear.show();
                }, { once: true });
            @elseif(session('modal_editar_id'))
                // Si el error vino del modal de editar, volver a abrirlo cuando se cierre el modal de errores
                document.getElementById('modalValidacionErrores').addEventListener('hidden.bs.modal', function () {
                    const modalEditar = new bootstrap.Modal(document.getElementById('modalEditarRecinto-{{ session("modal_editar_id") }}'));
                    modalEditar.show();
                }, { once: true });
            @endif
        }
    @endif
});

// Agregar efecto hover al botón
document.addEventListener('DOMContentLoaded', function() {
    const btnEntendido = document.querySelector('#modalValidacionErrores .btn-primary');
    if (btnEntendido) {
        btnEntendido.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        btnEntendido.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    }
});
</script>
@endsection


