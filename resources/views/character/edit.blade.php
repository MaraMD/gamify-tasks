@extends('layouts.app')

@section('content')
<style>
/* Avatar Customizer Styles */
.avatar-tile {
    width: 72px;
    height: 72px;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    outline: 2px solid transparent;
    transition: all 0.2s ease;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    background: var(--bs-light);
}

.avatar-tile:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    transform: translateY(-2px);
}

.avatar-tile.selected {
    outline-color: var(--bs-primary);
    box-shadow: 0 0 0 4px rgba(var(--bs-primary-rgb), 0.2);
}

.avatar-tile.selected::after {
    content: '✓';
    position: absolute;
    top: 4px;
    right: 4px;
    background: var(--bs-primary);
    color: white;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: bold;
}

.avatar-tile img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.avatar-stage {
    border: 2px solid var(--bs-border-color);
    border-radius: 1rem;
    overflow: hidden;
    position: relative;
    max-width: 512px;
    margin: 0 auto;
    aspect-ratio: 1;
}

.avatar-stage.bg-light {
    background: var(--bs-light);
}

.avatar-stage.bg-dark {
    background: var(--bs-dark);
}

.avatar-stage.bg-checker {
    background-image:
        linear-gradient(45deg, #ccc 25%, transparent 25%),
        linear-gradient(-45deg, #ccc 25%, transparent 25%),
        linear-gradient(45deg, transparent 75%, #ccc 75%),
        linear-gradient(-45deg, transparent 75%, #ccc 75%);
    background-size: 20px 20px;
    background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
}

.avatar-preview-container {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    transform-origin: center;
    transition: transform 0.3s ease;
}

.tile-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(72px, 1fr));
    gap: 0.75rem;
    padding: 1rem;
}

.sticky-footer {
    position: sticky;
    bottom: 0;
    background: var(--bs-body-bg);
    border-top: 1px solid var(--bs-border-color);
    padding: 1rem;
    z-index: 10;
}

@media (max-width: 768px) {
    .avatar-stage {
        max-width: 100%;
    }

    .tile-grid {
        grid-template-columns: repeat(auto-fill, minmax(60px, 1fr));
    }

    .avatar-tile {
        width: 60px;
        height: 60px;
    }
}
</style>

<div class="row justify-content-center">
    <div class="col-12 col-xl-10">
        {{-- Header --}}
        <div class="mb-4">
            <h2 class="mb-1">Personaliza tu Avatar</h2>
            <p class="text-muted mb-3">{{ $character->name }} - Nivel {{ $character->level }}</p>

            <div class="mb-2">
                <strong>Experiencia:</strong> {{ $character->xp }} XP
            </div>
            <div class="progress" style="height: 20px;">
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

        <form method="POST" action="{{ route('character.update') }}" id="avatar-form">
            @csrf
            @method('PATCH')

            {{-- Hidden inputs for layer files --}}
            <input type="hidden" name="body_file" id="input-skin" value="{{ old('body_file', $character->body_file) }}">
            <input type="hidden" name="eyes_file" id="input-face" value="{{ old('eyes_file', $character->eyes_file) }}">

            <div class="row g-4">
                {{-- Left Column: Stage --}}
                <div class="col-md-5">
                    <div class="card sticky-top" style="top: 1rem;">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Vista Previa</h5>

                            {{-- Stage Controls --}}
                            <div class="btn-group mb-3 w-100" role="group">
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setZoom(0.75)">0.75×</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary active" onclick="setZoom(1)">1×</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setZoom(2)">2×</button>
                            </div>

                            <div class="btn-group mb-3 w-100" role="group">
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="cycleBackground()">
                                    🎨 Fondo
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="randomize()">
                                    🔀 Aleatorio
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-warning" onclick="resetToSaved()">
                                    ↻ Restaurar
                                </button>
                            </div>

                            {{-- Avatar Stage --}}
                            <div class="avatar-stage bg-light" id="avatar-stage">
                                <div class="avatar-preview-container" id="avatar-preview">
                                    {{-- Avatar layers will be dynamically updated here --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Accordion --}}
                <div class="col-md-7">
                    <div class="accordion" id="layerAccordion">
                        @php
                            $layers = [
                                'skin' => ['title' => 'Skin (Cuerpo)', 'input' => 'input-skin', 'icon' => '👤'],
                                'face' => ['title' => 'Face (Rostro)', 'input' => 'input-face', 'icon' => '😊'],
                            ];
                        @endphp

                        @foreach($layers as $layerKey => $layerData)
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}"
                                            type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#collapse-{{ $layerKey }}"
                                            aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                                            aria-controls="collapse-{{ $layerKey }}">
                                        <span class="me-2">{{ $layerData['icon'] }}</span>
                                        {{ $layerData['title'] }}
                                        <span class="badge bg-secondary ms-2">{{ count($inventory[$layerKey] ?? []) }}</span>
                                    </button>
                                </h2>
                                <div id="collapse-{{ $layerKey }}"
                                     class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                                     data-bs-parent="#layerAccordion">
                                    <div class="accordion-body p-0">
                                        {{-- Tiles Grid --}}
                                        <div class="tile-grid" data-layer="{{ $layerKey }}">
                                            {{-- None option --}}
                                            <div class="avatar-tile"
                                                 role="button"
                                                 tabindex="0"
                                                 data-layer="{{ $layerKey }}"
                                                 data-key=""
                                                 data-url=""
                                                 data-label="Ninguno"
                                                 onclick="selectTile(this)"
                                                 onkeypress="if(event.key==='Enter')selectTile(this)">
                                                <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                                                    <span style="font-size: 2rem;">✕</span>
                                                </div>
                                            </div>

                                            {{-- Asset tiles --}}
                                            @foreach($inventory[$layerKey] ?? [] as $asset)
                                                <div class="avatar-tile"
                                                     role="button"
                                                     tabindex="0"
                                                     data-layer="{{ $layerKey }}"
                                                     data-key="{{ $asset['key'] }}"
                                                     data-url="{{ $asset['url'] }}"
                                                     data-label="{{ $asset['label'] }}"
                                                     onclick="selectTile(this)"
                                                     onkeypress="if(event.key==='Enter')selectTile(this)"
                                                     aria-pressed="false">
                                                    <img src="{{ $asset['url'] }}"
                                                         alt="{{ $asset['label'] }}"
                                                         loading="lazy">
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Sticky Footer --}}
            <div class="sticky-footer mt-4">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('tasks.today') }}" class="btn btn-secondary">
                        ✕ Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        ✓ Guardar Cambios
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
(function() {
    'use strict';

    // State
    let currentZoom = 1;
    let currentBackground = 'light';
    const backgrounds = ['light', 'dark', 'checker'];

    // Layer order for rendering
    const layerOrder = {!! json_encode(config('avatar.order', ['body', 'eyes', 'hair', 'top', 'bottom', 'acc'])) !!};

    // Mapping between layer keys and input IDs
    const layerInputMap = {
        'skin': 'input-skin',
        'face': 'input-face'
    };

    // Mapping between layer keys and DB field names
    const layerFieldMap = {
        'skin': 'body',
        'face': 'eyes'
    };

    // Store original values
    const originalValues = {
        'skin': '{{ $character->body_file ?? '' }}',
        'face': '{{ $character->eyes_file ?? '' }}'
    };

    // Inventory data
    const inventory = @json($inventory);

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Set initial selections based on character data
        Object.keys(originalValues).forEach(layer => {
            const value = originalValues[layer];
            if (value) {
                const tile = document.querySelector(`[data-layer="${layer}"][data-key="${value}"]`);
                if (tile) {
                    tile.classList.add('selected');
                    tile.setAttribute('aria-pressed', 'true');
                }
            } else {
                // Select "None" option
                const noneTile = document.querySelector(`[data-layer="${layer}"][data-key=""]`);
                if (noneTile) {
                    noneTile.classList.add('selected');
                    noneTile.setAttribute('aria-pressed', 'true');
                }
            }
        });

        updatePreview();
    });

    // Select a tile
    window.selectTile = function(tile) {
        const layer = tile.dataset.layer;
        const key = tile.dataset.key;
        const url = tile.dataset.url;

        // Deselect all tiles in this layer
        const allTiles = document.querySelectorAll(`[data-layer="${layer}"]`);
        allTiles.forEach(t => {
            t.classList.remove('selected');
            t.setAttribute('aria-pressed', 'false');
        });

        // Select this tile
        tile.classList.add('selected');
        tile.setAttribute('aria-pressed', 'true');

        // Update hidden input
        const inputId = layerInputMap[layer];
        const input = document.getElementById(inputId);
        if (input) {
            input.value = key;
        }

        // Update preview
        updatePreview();
    };

    // Update the preview
    function updatePreview() {
        const preview = document.getElementById('avatar-preview');
        const size = 384; // Base size for preview

        let html = '';

        layerOrder.forEach((layerField, index) => {
            // Find the layer key that maps to this field
            const layerKey = Object.keys(layerFieldMap).find(k => layerFieldMap[k] === layerField);
            if (!layerKey) return;

            const inputId = layerInputMap[layerKey];
            const input = document.getElementById(inputId);
            const file = input ? input.value : '';

            if (file) {
                const basePath = '{{ asset(trim(config("avatar.base_path"), "/")) }}';
                const src = `${basePath}/${file}`;
                const zIndex = index + 1;
                html += `<img src="${src}"
                              alt="${layerField}"
                              style="position:absolute;top:0;left:0;width:${size}px;height:${size}px;object-fit:contain;image-rendering:auto;z-index:${zIndex};">`;
            }
        });

        if (html === '') {
            html = '<div class="text-muted text-center">Selecciona partes para tu avatar</div>';
        }

        preview.innerHTML = html;
    }

    // Set zoom level
    window.setZoom = function(scale) {
        currentZoom = scale;
        const preview = document.getElementById('avatar-preview');
        preview.style.transform = `scale(${scale})`;

        // Update button states
        document.querySelectorAll('[onclick^="setZoom"]').forEach(btn => {
            btn.classList.remove('active');
        });
        event.target.classList.add('active');
    };

    // Cycle background
    window.cycleBackground = function() {
        const stage = document.getElementById('avatar-stage');
        const currentIndex = backgrounds.indexOf(currentBackground);
        const nextIndex = (currentIndex + 1) % backgrounds.length;
        currentBackground = backgrounds[nextIndex];

        backgrounds.forEach(bg => stage.classList.remove(`bg-${bg}`));
        stage.classList.add(`bg-${currentBackground}`);
    };

    // Randomize selection
    window.randomize = function() {
        Object.keys(inventory).forEach(layer => {
            const assets = inventory[layer];
            if (assets.length > 0) {
                const randomIndex = Math.floor(Math.random() * assets.length);
                const randomAsset = assets[randomIndex];

                const tile = document.querySelector(`[data-layer="${layer}"][data-key="${randomAsset.key}"]`);
                if (tile) {
                    selectTile(tile);
                }
            }
        });
    };

    // Reset to saved values
    window.resetToSaved = function() {
        Object.keys(originalValues).forEach(layer => {
            const value = originalValues[layer];
            let tile;

            if (value) {
                tile = document.querySelector(`[data-layer="${layer}"][data-key="${value}"]`);
            } else {
                tile = document.querySelector(`[data-layer="${layer}"][data-key=""]`);
            }

            if (tile) {
                selectTile(tile);
            }
        });
    };
})();
</script>
@endsection
