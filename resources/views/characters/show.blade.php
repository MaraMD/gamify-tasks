@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="mb-4">
            <h2 class="mb-1">Mi Personaje</h2>
            <p class="text-muted mb-0">Gestiona tu avatar y progreso</p>
        </div>

        @if(session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Character Stats Card --}}
        <div class="card mb-4">
            <div class="card-body">
                <h3 class="card-title mb-3">{{ $character->name }}</h3>
                <div class="mb-3">
                    <span class="badge badge-soft-primary fs-6">Nivel {{ $character->level }}</span>
                </div>
                <div class="mb-2">
                    <strong>Experiencia:</strong> {{ $character->xp }} XP
                </div>
                <div class="progress" style="height: 25px;">
                    @php
                        $currentLevelXp = ($character->level - 1) * 100;
                        $nextLevelXp = $character->level * 100;
                        $xpInLevel = $character->xp - $currentLevelXp;
                        $xpNeeded = $nextLevelXp - $currentLevelXp;
                        $percentage = ($xpInLevel / $xpNeeded) * 100;
                    @endphp
                    <div class="progress-bar bg-success" role="progressbar"
                         style="width: {{ $percentage }}%"
                         aria-valuenow="{{ $xpInLevel }}"
                         aria-valuemin="0"
                         aria-valuemax="{{ $xpNeeded }}">
                        {{ round($percentage) }}%
                    </div>
                </div>
                <small class="text-muted">{{ $xpInLevel }} / {{ $xpNeeded }} XP hasta el siguiente nivel</small>
            </div>
        </div>

        {{-- Character Customization --}}
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Personaliza tu Avatar (Kenney Modular Characters)</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    {{-- Left Column: Avatar Preview --}}
                    <div class="col-md-5 text-center">
                        <div class="border rounded p-4 mb-3" style="background: var(--surface);">
                            <div id="avatar-preview">
                                <x-avatar :character="$character" size="220" class="rounded-3 shadow-sm" />
                            </div>
                        </div>
                        <p class="text-muted small">Vista previa en tiempo real</p>
                    </div>

                    {{-- Right Column: Customization Form --}}
                    <div class="col-md-7">
                        <form method="POST" action="{{ route('character.update') }}" id="character-form">
                            @csrf
                            @method('PATCH')

                            <div class="mb-3">
                                <label for="name" class="form-label">Nombre del Personaje</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name', $character->name) }}">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <hr class="my-4">
                            <h6 class="mb-3">Capas del Avatar</h6>
                            <p class="text-muted small mb-3">Selecciona las partes individuales de tu avatar:</p>

                            {{-- Body Layer --}}
                            <div class="mb-3">
                                <label for="body_file" class="form-label">Cuerpo (Skin)</label>
                                <select class="form-select" id="body_file" name="body_file">
                                    <option value="">Ninguno</option>
                                    @foreach($avatarAssets['body'] as $file)
                                        <option value="{{ $file }}"
                                                @selected(old('body_file', $character->body_file) === $file)>
                                            {{ \App\Support\Avatar::displayName(basename($file)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Eyes Layer --}}
                            <div class="mb-3">
                                <label for="eyes_file" class="form-label">Rostro (Face)</label>
                                <select class="form-select" id="eyes_file" name="eyes_file">
                                    <option value="">Ninguno</option>
                                    @foreach($avatarAssets['eyes'] as $file)
                                        <option value="{{ $file }}"
                                                @selected(old('eyes_file', $character->eyes_file) === $file)>
                                            {{ \App\Support\Avatar::displayName(basename($file)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Hair Layer --}}
                            <div class="mb-3">
                                <label for="hair_file" class="form-label">Cabello (Hair)</label>
                                <select class="form-select" id="hair_file" name="hair_file">
                                    <option value="">Ninguno</option>
                                    @foreach($avatarAssets['hair'] as $file)
                                        <option value="{{ $file }}"
                                                @selected(old('hair_file', $character->hair_file) === $file)>
                                            {{ \App\Support\Avatar::displayName(basename($file)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Top Layer (Shirts) --}}
                            <div class="mb-3">
                                <label for="top_file" class="form-label">Camisa (Shirt)</label>
                                <select class="form-select" id="top_file" name="top_file">
                                    <option value="">Ninguno</option>
                                    @foreach($avatarAssets['top'] as $file)
                                        <option value="{{ $file }}"
                                                @selected(old('top_file', $character->top_file) === $file)>
                                            {{ \App\Support\Avatar::displayName(basename($file)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Bottom Layer (Pants) --}}
                            <div class="mb-3">
                                <label for="bottom_file" class="form-label">Pantalones (Pants)</label>
                                <select class="form-select" id="bottom_file" name="bottom_file">
                                    <option value="">Ninguno</option>
                                    @foreach($avatarAssets['bottom'] as $file)
                                        <option value="{{ $file }}"
                                                @selected(old('bottom_file', $character->bottom_file) === $file)>
                                            {{ \App\Support\Avatar::displayName(basename($file)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Accessories Layer (Shoes) --}}
                            <div class="mb-3">
                                <label for="acc_file" class="form-label">Zapatos (Shoes)</label>
                                <select class="form-select" id="acc_file" name="acc_file">
                                    <option value="">Ninguno</option>
                                    @foreach($avatarAssets['acc'] as $file)
                                        <option value="{{ $file }}"
                                                @selected(old('acc_file', $character->acc_file) === $file)>
                                            {{ \App\Support\Avatar::displayName(basename($file)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-secondary" id="reset-btn">
                                    Limpiar Todo
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    Guardar Cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Live Preview Script --}}
<script>
(function() {
    'use strict';

    // Get form elements
    const form = document.getElementById('character-form');
    const resetBtn = document.getElementById('reset-btn');
    const previewDiv = document.getElementById('avatar-preview');

    // Layer selectors
    const selectors = {
        body: document.getElementById('body_file'),
        eyes: document.getElementById('eyes_file'),
        hair: document.getElementById('hair_file'),
        top: document.getElementById('top_file'),
        bottom: document.getElementById('bottom_file'),
        acc: document.getElementById('acc_file')
    };

    // Base path for assets
    const basePath = {!! json_encode(asset(trim(config('avatar.base_path'), '/'))) !!};
    const layerOrder = @json(config('avatar.order'));

    // Update preview when any selector changes
    function updatePreview() {
        const size = 220;
        let html = `<div class="position-relative rounded-3 shadow-sm" style="width:${size}px;height:${size}px;margin:0 auto;">`;

        layerOrder.forEach(layer => {
            const file = selectors[layer]?.value;
            if (file) {
                const src = `${basePath}/${file}`;
                html += `<img alt="${layer}" src="${src}" class="position-absolute top-0 start-0" style="width:100%;height:100%;object-fit:contain;image-rendering:auto;">`;
            }
        });

        html += '</div>';
        previewDiv.innerHTML = html;
    }

    // Attach change listeners to all selectors
    Object.values(selectors).forEach(selector => {
        if (selector) {
            selector.addEventListener('change', updatePreview);
        }
    });

    // Reset button clears all selections
    resetBtn.addEventListener('click', function() {
        Object.values(selectors).forEach(selector => {
            if (selector) {
                selector.value = '';
            }
        });
        updatePreview();
    });

    // Initial preview update
    updatePreview();
})();
</script>
@endsection
