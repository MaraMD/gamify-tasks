@props([
    'skin' => '#ffffff',
    'boxer' => '#6c757d',
    'hairStyle' => 'none',
    'hair' => '#000000',
    'eyes' => '#2b2b2b',
    'size' => 240,
    'class' => ''
])

<svg viewBox="0 0 240 320" width="{{ $size }}" height="{{ $size }}" class="{{ $class }}" aria-label="Avatar del personaje" id="character-avatar">
    {{-- Body (torso and head) --}}
    <ellipse id="svg-body" cx="120" cy="80" rx="50" ry="60" fill="{{ $skin }}" />

    {{-- Neck --}}
    <rect id="svg-neck" x="105" y="130" width="30" height="20" fill="{{ $skin }}" />

    {{-- Torso --}}
    <ellipse id="svg-torso" cx="120" cy="180" rx="60" ry="50" fill="{{ $skin }}" />

    {{-- Arms --}}
    <ellipse id="svg-arm-left" cx="60" cy="170" rx="18" ry="45" fill="{{ $skin }}" />
    <ellipse id="svg-arm-right" cx="180" cy="170" rx="18" ry="45" fill="{{ $skin }}" />

    {{-- Legs --}}
    <rect id="svg-leg-left" x="85" y="215" width="25" height="70" rx="10" fill="{{ $skin }}" />
    <rect id="svg-leg-right" x="130" y="215" width="25" height="70" rx="10" fill="{{ $skin }}" />

    {{-- Boxer shorts --}}
    <rect id="svg-boxer" x="70" y="210" width="100" height="40" rx="5" fill="{{ $boxer }}" />

    {{-- Eyes --}}
    <ellipse id="svg-eye-left" cx="105" cy="75" rx="6" ry="8" fill="{{ $eyes }}" />
    <ellipse id="svg-eye-right" cx="135" cy="75" rx="6" ry="8" fill="{{ $eyes }}" />

    {{-- Hair - Buzz (short all around) --}}
    <g id="svg-hair-buzz" style="display: {{ $hairStyle === 'buzz' ? 'block' : 'none' }}">
        <ellipse cx="120" cy="40" rx="52" ry="25" fill="{{ $hair }}" />
    </g>

    {{-- Hair - Short (top with some volume) --}}
    <g id="svg-hair-short" style="display: {{ $hairStyle === 'short' ? 'block' : 'none' }}">
        <ellipse cx="120" cy="35" rx="55" ry="30" fill="{{ $hair }}" />
        <rect x="65" y="35" width="110" height="25" fill="{{ $hair }}" />
    </g>
</svg>
