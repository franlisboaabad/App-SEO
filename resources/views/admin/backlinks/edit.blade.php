@extends('adminlte::page')

@section('title', 'Editar Backlink')

@section('content_header')
    <h1><i class="fas fa-edit"></i> Editar Backlink</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('backlinks.update', $backlink->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="site_id">Sitio *</label>
                    <select name="site_id" id="site_id" class="form-control @error('site_id') is-invalid @enderror" required>
                        <option value="">Seleccione un sitio</option>
                        @foreach($sites as $site)
                            <option value="{{ $site->id }}" {{ old('site_id', $backlink->site_id) == $site->id ? 'selected' : '' }}>
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
                           value="{{ old('source_url', $backlink->source_url) }}"
                           required>
                    @error('source_url')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="target_url">URL Destino (tu sitio) *</label>
                    <input type="url"
                           name="target_url"
                           id="target_url"
                           class="form-control @error('target_url') is-invalid @enderror"
                           value="{{ old('target_url', $backlink->target_url) }}"
                           required>
                    @error('target_url')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="anchor_text">Anchor Text</label>
                    <input type="text"
                           name="anchor_text"
                           id="anchor_text"
                           class="form-control @error('anchor_text') is-invalid @enderror"
                           value="{{ old('anchor_text', $backlink->anchor_text) }}"
                           maxlength="500">
                    @error('anchor_text')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="link_type">Tipo de Enlace *</label>
                            <select name="link_type" id="link_type" class="form-control @error('link_type') is-invalid @enderror" required>
                                <option value="dofollow" {{ old('link_type', $backlink->link_type) == 'dofollow' ? 'selected' : '' }}>Dofollow</option>
                                <option value="nofollow" {{ old('link_type', $backlink->link_type) == 'nofollow' ? 'selected' : '' }}>Nofollow</option>
                                <option value="sponsored" {{ old('link_type', $backlink->link_type) == 'sponsored' ? 'selected' : '' }}>Sponsored</option>
                                <option value="ugc" {{ old('link_type', $backlink->link_type) == 'ugc' ? 'selected' : '' }}>UGC</option>
                            </select>
                            @error('link_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="is_toxic" value="1" {{ old('is_toxic', $backlink->is_toxic) ? 'checked' : '' }}>
                                Marcar como Tóxico
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="first_seen">Fecha Primera Detección</label>
                            <input type="date"
                                   name="first_seen"
                                   id="first_seen"
                                   class="form-control @error('first_seen') is-invalid @enderror"
                                   value="{{ old('first_seen', $backlink->first_seen ? $backlink->first_seen->format('Y-m-d') : '') }}">
                            @error('first_seen')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="last_seen">Fecha Última Detección</label>
                            <input type="date"
                                   name="last_seen"
                                   id="last_seen"
                                   class="form-control @error('last_seen') is-invalid @enderror"
                                   value="{{ old('last_seen', $backlink->last_seen ? $backlink->last_seen->format('Y-m-d') : '') }}">
                            @error('last_seen')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="domain_authority">Domain Authority</label>
                            <input type="number"
                                   name="domain_authority"
                                   id="domain_authority"
                                   class="form-control @error('domain_authority') is-invalid @enderror"
                                   value="{{ old('domain_authority', $backlink->domain_authority) }}"
                                   min="0"
                                   max="100">
                            @error('domain_authority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_authority">Page Authority</label>
                    <input type="number"
                           name="page_authority"
                           id="page_authority"
                           class="form-control @error('page_authority') is-invalid @enderror"
                           value="{{ old('page_authority', $backlink->page_authority) }}"
                           min="0"
                           max="100">
                    @error('page_authority')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                @if($backlink->is_toxic)
                    <div class="form-group">
                        <label for="toxic_reason">Razón (Tóxico)</label>
                        <textarea name="toxic_reason"
                                  id="toxic_reason"
                                  class="form-control @error('toxic_reason') is-invalid @enderror"
                                  rows="2"
                                  maxlength="500">{{ old('toxic_reason', $backlink->toxic_reason) }}</textarea>
                        @error('toxic_reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                @endif

                <div class="form-group">
                    <label for="notes">Notas</label>
                    <textarea name="notes"
                              id="notes"
                              class="form-control @error('notes') is-invalid @enderror"
                              rows="3"
                              maxlength="1000">{{ old('notes', $backlink->notes) }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Actualizar Backlink
                    </button>
                    <a href="{{ route('backlinks.index', ['site_id' => $backlink->site_id]) }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@stop

