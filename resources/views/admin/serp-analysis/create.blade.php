@extends('adminlte::page')

@section('title', 'Nuevo Análisis de SERP')

@section('content_header')
    <div class="row">
        <div class="col-md">
            <h1><i class="fas fa-plus"></i> Nuevo Análisis de SERP</h1>
        </div>
        <div class="col-md text-right">
            <a href="{{ route('serp-analysis.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Analizar SERP para una Keyword</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('serp-analysis.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="site_id">Sitio <span class="text-danger">*</span></label>
                    <select name="site_id" id="site_id" class="form-control @error('site_id') is-invalid @enderror" required>
                        <option value="">Seleccione un sitio</option>
                        @foreach($sites as $site)
                            <option value="{{ $site->id }}" {{ old('site_id', $siteId) == $site->id ? 'selected' : '' }}>
                                {{ $site->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('site_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="keyword_id">Keyword desde Tracking (Opcional)</label>
                    <select name="keyword_id" id="keyword_id" class="form-control">
                        <option value="">Seleccione una keyword</option>
                        @foreach($keywords as $kw)
                            <option value="{{ $kw->id }}" {{ old('keyword_id', $keywordId) == $kw->id ? 'selected' : '' }}>
                                {{ $kw->keyword }} @if($kw->current_position) (Pos: {{ $kw->current_position }}) @endif
                            </option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">Si seleccionas una keyword, se usará automáticamente</small>
                </div>

                <div class="form-group">
                    <label for="keyword">Keyword <span class="text-danger">*</span></label>
                    <input type="text"
                           name="keyword"
                           id="keyword"
                           class="form-control @error('keyword') is-invalid @enderror"
                           value="{{ old('keyword', $keyword ? $keyword->keyword : '') }}"
                           placeholder="Ej: hoteles en lima"
                           required>
                    @error('keyword')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">La keyword que quieres analizar en Google</small>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Nota:</strong> El análisis de SERP puede tardar unos segundos.
                    Google puede bloquear scraping frecuente, por lo que se recomienda usar con moderación.
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary" id="analyzeButton">
                        <i class="fas fa-search"></i> <span id="buttonText">Analizar SERP</span>
                    </button>
                    <a href="{{ route('serp-analysis.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>

            <!-- Mensaje de carga (oculto inicialmente) -->
            <div id="loadingMessage" class="alert alert-info mt-3" style="display: none;">
                <i class="fas fa-spinner fa-spin"></i>
                <strong>Analizando SERP...</strong> Por favor espera, esto puede tardar 10-30 segundos.
                <br><small>No cierres esta página mientras se procesa.</small>
            </div>
        </div>
    </div>

    <script>
        // Cargar keywords cuando se selecciona un sitio
        document.getElementById('site_id').addEventListener('change', function() {
            const siteId = this.value;
            const keywordSelect = document.getElementById('keyword_id');

            // Limpiar opciones
            keywordSelect.innerHTML = '<option value="">Seleccione una keyword</option>';

            if (siteId) {
                // Cargar keywords del sitio seleccionado
                fetch(`/admin/keywords?site_id=${siteId}&format=json`)
                    .then(response => response.json())
                    .then(data => {
                        data.keywords.forEach(keyword => {
                            const option = document.createElement('option');
                            option.value = keyword.id;
                            option.textContent = keyword.keyword + (keyword.current_position ? ` (Pos: ${keyword.current_position})` : '');
                            keywordSelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Error al cargar keywords:', error);
                    });
            }
        });

        // Auto-completar keyword si se selecciona desde tracking
        const keywordIdSelect = document.getElementById('keyword_id');
        if (keywordIdSelect) {
            keywordIdSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption && selectedOption.value) {
                    const keywordText = selectedOption.text.split(' (')[0];
                    document.getElementById('keyword').value = keywordText;
                }
            });
        }

        // Mostrar indicador de carga al enviar formulario
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const button = document.getElementById('analyzeButton');
                const buttonText = document.getElementById('buttonText');
                const loadingMessage = document.getElementById('loadingMessage');

                if (button && buttonText && loadingMessage) {
                    // Deshabilitar botón y mostrar spinner
                    button.disabled = true;
                    buttonText.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Analizando...';
                    button.classList.add('disabled');

                    // Mostrar mensaje de carga
                    loadingMessage.style.display = 'block';
                }
            });
        }
    </script>
@stop

