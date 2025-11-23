@extends('adminlte::page')

@section('title', 'Nuevo Sitio')

@section('content_header')
    <div class="row">
        <div class="col-md">
            <h1>Nuevo Sitio Web</h1>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('sites.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nombre">Nombre del Sitio <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="nombre"
                                   id="nombre"
                                   class="form-control @error('nombre') is-invalid @enderror"
                                   value="{{ old('nombre') }}"
                                   placeholder="Ej: Mi Sitio Web"
                                   required>
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="dominio_base">Dominio Base <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="dominio_base"
                                   id="dominio_base"
                                   class="form-control @error('dominio_base') is-invalid @enderror"
                                   value="{{ old('dominio_base') }}"
                                   placeholder="Ej: ejemplo.com"
                                   required>
                            <small class="form-text text-muted">Sin http:// o https://</small>
                            @error('dominio_base')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="gsc_property">Google Search Console Property</label>
                            <input type="text"
                                   name="gsc_property"
                                   id="gsc_property"
                                   class="form-control @error('gsc_property') is-invalid @enderror"
                                   value="{{ old('gsc_property') }}"
                                   placeholder="sc-domain:ejemplo.com o https://ejemplo.com">
                            <small class="form-text text-muted">Formato: sc-domain:ejemplo.com o https://ejemplo.com</small>
                            @error('gsc_property')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="estado">Estado</label>
                            <div class="form-check">
                                <input type="checkbox"
                                       name="estado"
                                       id="estado"
                                       class="form-check-input"
                                       value="1"
                                       {{ old('estado', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="estado">
                                    Activo
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="gsc_credentials">Credenciales JSON de Google Search Console</label>
                    <textarea name="gsc_credentials"
                              id="gsc_credentials"
                              class="form-control @error('gsc_credentials') is-invalid @enderror"
                              rows="8"
                              placeholder='Pega aquí el contenido del archivo JSON de credenciales'>{!! old('gsc_credentials') !!}</textarea>
                    <small class="form-text text-muted">Pega el contenido completo del archivo JSON de credenciales de Google Cloud Platform</small>
                    @error('gsc_credentials')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <hr>

                <div class="form-group">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Guardar Sitio
                    </button>
                    <a href="{{ route('sites.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        // Validar formato JSON al perder el foco
        document.getElementById('gsc_credentials').addEventListener('blur', function() {
            const value = this.value.trim();
            if (value) {
                try {
                    JSON.parse(value);
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } catch (e) {
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                    alert('El JSON no es válido. Por favor, verifica el formato.');
                }
            }
        });
    </script>
@stop

