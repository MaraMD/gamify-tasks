@props(['character', 'size' => 128, 'class' => ''])

@php
    $order = config('avatar.order', ['body', 'eyes', 'hair', 'top', 'bottom', 'acc']);
@endphp

<div class="position-relative {{ $class }}" style="width:{{ $size }}px;height:{{ $size }}px;">
    @foreach($order as $index => $layer)
        @php
            $src = $character?->avatarLayerPath($layer);
        @endphp
        @if($src)
            <img
                alt="{{ $layer }}"
                src="{{ $src }}"
                class="position-absolute top-0 start-0"
                style="width:100%;height:100%;object-fit:contain;image-rendering:auto;z-index:{{ $index + 1 }};"
            >
        @endif
    @endforeach
</div>

{{--
Kenney Modular Characters Avatar Component

Usage Examples:

  Grande en Mi Personaje:
  <x-avatar :character="$character" size="220" class="rounded-3 shadow-sm" />

  Mini en Tareas de Hoy:
  <x-avatar :character="$character" size="72" class="me-2" />

  En tablas (lista de tareas):
  <x-avatar :character="$character" size="40" class="align-middle me-2" />

Notas:
- Para asignar un asset específico, guardar la ruta relativa en el campo correspondiente
  (p.ej. body_file = 'skin/skin_01.png').
- Si el campo está null, el helper elige el primer archivo del folder.
- Los layers lógicos (body, eyes, hair, top, bottom, acc) se mapean a los folders reales
  del pack Kenney (skin, face, hair, shirts, pants, shoes) mediante config/avatar.php
--}}
