@extends('adminlte::page')

@section('title', 'Editar Keyword')

@section('content_header')
    <h1>Editar Keyword: {{ $keyword->keyword }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('keywords.update', $keyword) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="site_id">Sitio *</label>
                    <select name="site_id" id="site_id" class="form-control @error('site_id') is-invalid @enderror" required>
                        @foreach($sites as $site)
                            <option value="{{ $site->id }}" {{ old('site_id', $keyword->site_id) == $site->id ? 'selected' : '' }}>
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
                           value="{{ old('keyword', $keyword->keyword) }}"
                           required>
                    @error('keyword')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="target_url">URL Objetivo (Opcional)</label>
                    <input type="url"
                           name="target_url"
                           id="target_url"
                           class="form-control @error('target_url') is-invalid @enderror"
                           value="{{ old('target_url', $keyword->target_url) }}">
                    @error('target_url')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="notes">Notas (Opcional)</label>
                    <textarea name="notes"
                              id="notes"
                              class="form-control @error('notes') is-invalid @enderror"
                              rows="3">{{ old('notes', $keyword->notes) }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox"
                               name="is_active"
                               id="is_active"
                               class="form-check-input"
                               value="1"
                               {{ old('is_active', $keyword->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Keyword activa
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Actualizar Keyword
                    </button>
                    <a href="{{ route('keywords.index', ['site_id' => $keyword->site_id]) }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@stop

