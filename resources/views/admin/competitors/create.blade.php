@extends('adminlte::page')

@section('title', 'Nuevo Competidor')

@section('content_header')
    <h1>Agregar Nuevo Competidor</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('competitors.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="site_id">Sitio *</label>
                    <select name="site_id" id="site_id" class="form-control @error('site_id') is-invalid @enderror" required>
                        <option value="">Seleccione un sitio</option>
                        @foreach($sites as $site)
                            <option value="{{ $site->id }}" {{ old('site_id', $siteId) == $site->id ? 'selected' : '' }}>
                                {{ $site->nombre }} ({{ $site->dominio_base }})
                            </option>
                        @endforeach
                    </select>
                    @error('site_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Sitio para el cual este es un competidor</small>
                </div>

                <div class="form-group">
                    <label for="nombre">Nombre del Competidor *</label>
                    <input type="text"
                           name="nombre"
                           id="nombre"
                           class="form-control @error('nombre') is-invalid @enderror"
                           value="{{ old('nombre') }}"
                           placeholder="Ej: Competidor Principal"
                           required>
                    @error('nombre')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="dominio_base">Dominio Base *</label>
                    <input type="text"
                           name="dominio_base"
                           id="dominio_base"
                           class="form-control @error('dominio_base') is-invalid @enderror"
                           value="{{ old('dominio_base') }}"
                           placeholder="ejemplo.com"
                           required>
                    @error('dominio_base')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Sin http:// o https://</small>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Nota:</strong> Los competidores NO necesitan credenciales de Google Search Console.
                    Las posiciones se ingresan manualmente desde el dashboard de competencia.
                </div>

                <div class="form-group">
                    <label for="gsc_property">Google Search Console Property (Opcional - Solo si tienes acceso)</label>
                    <input type="text"
                           name="gsc_property"
                           id="gsc_property"
                           class="form-control @error('gsc_property') is-invalid @enderror"
                           value="{{ old('gsc_property') }}"
                           placeholder="sc-domain:ejemplo.com o https://ejemplo.com">
                    @error('gsc_property')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Solo si tienes acceso directo a las métricas del competidor (caso especial)</small>
                </div>

                <div class="form-group">
                    <label for="gsc_credentials">Credenciales JSON de GSC (Opcional - Solo si tienes acceso)</label>
                    <textarea name="gsc_credentials"
                              id="gsc_credentials"
                              class="form-control @error('gsc_credentials') is-invalid @enderror"
                              rows="8"
                              placeholder='Pega aquí el contenido del archivo JSON de credenciales'>{!! old('gsc_credentials') !!}</textarea>
                    @error('gsc_credentials')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Solo si tienes acceso directo a las métricas del competidor (caso especial). Normalmente se deja vacío.</small>
                </div>

                <div class="form-group">
                    <label for="notes">Notas (Opcional)</label>
                    <textarea name="notes"
                              id="notes"
                              class="form-control @error('notes') is-invalid @enderror"
                              rows="3">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Competidor
                    </button>
                    <a href="{{ route('competitors.index', ['site_id' => $siteId]) }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
    <script>
        // Validar formato JSON al perder el foco
        document.getElementById('gsc_credentials')?.addEventListener('blur', function() {
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

