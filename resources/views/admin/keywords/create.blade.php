@extends('adminlte::page')

@section('title', 'Nueva Keyword')

@section('content_header')
    <h1>Agregar Nueva Keyword</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('keywords.store') }}" method="POST">
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
                </div>

                <div class="form-group">
                    <label for="keyword">Keyword *</label>
                    <input type="text"
                           name="keyword"
                           id="keyword"
                           class="form-control @error('keyword') is-invalid @enderror"
                           value="{{ old('keyword') }}"
                           placeholder="Ej: hoteles en lima"
                           required>
                    @error('keyword')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">La keyword que quieres seguir en Google Search Console</small>
                </div>

                <div class="form-group">
                    <label for="target_url">URL Objetivo (Opcional)</label>
                    <input type="url"
                           name="target_url"
                           id="target_url"
                           class="form-control @error('target_url') is-invalid @enderror"
                           value="{{ old('target_url') }}"
                           placeholder="https://ejemplo.com/pagina">
                    @error('target_url')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">URL específica que quieres rankear para esta keyword</small>
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
                        <i class="fas fa-save"></i> Guardar Keyword
                    </button>
                    <a href="{{ route('keywords.index', ['site_id' => $siteId]) }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@stop

