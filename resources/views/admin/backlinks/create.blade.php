@extends('adminlte::page')

@section('title', 'Agregar Backlink')

@section('content_header')
    <h1><i class="fas fa-plus"></i> Agregar Nuevo Backlink</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('backlinks.store') }}" method="POST">
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
                    <label for="source_url">URL Fuente (que enlaza) *</label>
                    <input type="url"
                           name="source_url"
                           id="source_url"
                           class="form-control @error('source_url') is-invalid @enderror"
                           value="{{ old('source_url') }}"
                           placeholder="https://ejemplo.com/pagina"
                           required>
                    @error('source_url')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">URL completa del sitio que enlaza a tu contenido</small>
                </div>

                <div class="form-group">
                    <label for="target_url">URL Destino (tu sitio) *</label>
                    <input type="url"
                           name="target_url"
                           id="target_url"
                           class="form-control @error('target_url') is-invalid @enderror"
                           value="{{ old('target_url') }}"
                           placeholder="https://tusitio.com/pagina"
                           required>
                    @error('target_url')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">URL de tu sitio que recibe el enlace</small>
                </div>

                <div class="form-group">
                    <label for="anchor_text">Anchor Text</label>
                    <input type="text"
                           name="anchor_text"
                           id="anchor_text"
                           class="form-control @error('anchor_text') is-invalid @enderror"
                           value="{{ old('anchor_text') }}"
                           placeholder="Texto del enlace"
                           maxlength="500">
                    @error('anchor_text')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Texto visible del enlace (opcional)</small>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="link_type">Tipo de Enlace *</label>
                            <select name="link_type" id="link_type" class="form-control @error('link_type') is-invalid @enderror" required>
                                <option value="dofollow" {{ old('link_type', 'dofollow') == 'dofollow' ? 'selected' : '' }}>Dofollow</option>
                                <option value="nofollow" {{ old('link_type') == 'nofollow' ? 'selected' : '' }}>Nofollow</option>
                                <option value="sponsored" {{ old('link_type') == 'sponsored' ? 'selected' : '' }}>Sponsored</option>
                                <option value="ugc" {{ old('link_type') == 'ugc' ? 'selected' : '' }}>UGC (User Generated Content)</option>
                            </select>
                            @error('link_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="first_seen">Fecha Primera Detección</label>
                            <input type="date"
                                   name="first_seen"
                                   id="first_seen"
                                   class="form-control @error('first_seen') is-invalid @enderror"
                                   value="{{ old('first_seen', date('Y-m-d')) }}">
                            @error('first_seen')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="domain_authority">Domain Authority (0-100)</label>
                            <input type="number"
                                   name="domain_authority"
                                   id="domain_authority"
                                   class="form-control @error('domain_authority') is-invalid @enderror"
                                   value="{{ old('domain_authority') }}"
                                   min="0"
                                   max="100">
                            @error('domain_authority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="page_authority">Page Authority (0-100)</label>
                            <input type="number"
                                   name="page_authority"
                                   id="page_authority"
                                   class="form-control @error('page_authority') is-invalid @enderror"
                                   value="{{ old('page_authority') }}"
                                   min="0"
                                   max="100">
                            @error('page_authority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="notes">Notas</label>
                    <textarea name="notes"
                              id="notes"
                              class="form-control @error('notes') is-invalid @enderror"
                              rows="3"
                              maxlength="1000">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Backlink
                    </button>
                    <a href="{{ route('backlinks.index', ['site_id' => $siteId]) }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@stop

