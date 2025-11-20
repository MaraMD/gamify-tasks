<?php

use App\Services\TaskScoringService;

beforeEach(function () {
    config(['app.timezone' => 'America/Mexico_City']);
    $this->service = new TaskScoringService();
});

test('evaluate returns required keys', function () {
    $result = $this->service->evaluate('Test task', 'Simple description', now()->addDays(5)->toDateString());

    expect($result)->toBeArray();
    expect($result)->toHaveKeys(['difficulty', 'points', 'factors', 'score']);
});

test('evaluate returns difficulty within valid range', function () {
    $result = $this->service->evaluate('Test task', 'Description', now()->addDays(3)->toDateString());

    expect($result['difficulty'])->toBeGreaterThanOrEqual(1);
    expect($result['difficulty'])->toBeLessThanOrEqual(3);
});

test('evaluate returns points within valid range', function () {
    $result = $this->service->evaluate('Test task', 'Description', now()->addDays(3)->toDateString());

    expect($result['points'])->toBeGreaterThanOrEqual(5);
    expect($result['points'])->toBeLessThanOrEqual(100);
});

test('long description with urgent due date scores higher than baseline', function () {
    // Escenario base: descripción corta, fecha lejana
    $baseResult = $this->service->evaluate(
        'Simple task',
        'Short description',
        now()->addDays(10)->toDateString()
    );

    // Escenario complejo: descripción larga (>100 palabras), fecha urgente
    $longDescription = implode(' ', array_fill(0, 120, 'palabra'));

    $complexResult = $this->service->evaluate(
        'Complex task',
        $longDescription,
        now()->toDateString() // Hoy = urgente
    );

    // El escenario complejo debe tener mayor score
    expect($complexResult['score'])->toBeGreaterThan($baseResult['score']);

    // El escenario complejo debe tener mayor o igual dificultad
    expect($complexResult['difficulty'])->toBeGreaterThanOrEqual($baseResult['difficulty']);

    // El escenario complejo debe tener más puntos
    expect($complexResult['points'])->toBeGreaterThan($baseResult['points']);
});

test('urgent task with today due date scores high', function () {
    $result = $this->service->evaluate(
        'Urgent task',
        'This is an urgent task that needs to be done today',
        now()->toDateString()
    );

    // Tareas urgentes (hoy) deberían tener score alto
    expect($result['score'])->toBeGreaterThan(40);
    expect($result['factors'])->toHaveKey('urgency');
});

test('task with past due date scores highest urgency', function () {
    $result = $this->service->evaluate(
        'Overdue task',
        'This task is overdue',
        now()->subDays(2)->toDateString()
    );

    // Tareas atrasadas deberían tener score muy alto
    expect($result['score'])->toBeGreaterThan(40);
    expect($result['difficulty'])->toBeGreaterThanOrEqual(2);
});

test('long description alone increases score', function () {
    // Descripción corta
    $shortResult = $this->service->evaluate(
        'Task',
        'Short text',
        now()->addDays(10)->toDateString()
    );

    // Descripción larga (>100 palabras)
    $longDescription = implode(' ', array_fill(0, 110, 'word'));
    $longResult = $this->service->evaluate(
        'Task',
        $longDescription,
        now()->addDays(10)->toDateString()
    );

    // La descripción larga debe tener mayor score
    expect($longResult['score'])->toBeGreaterThan($shortResult['score']);
});

test('evaluate with null values returns valid result', function () {
    $result = $this->service->evaluate(null, null, null);

    expect($result)->toBeArray();
    expect($result)->toHaveKeys(['difficulty', 'points', 'factors', 'score']);
    expect($result['difficulty'])->toBeGreaterThanOrEqual(1);
    expect($result['difficulty'])->toBeLessThanOrEqual(3);
    expect($result['points'])->toBeGreaterThanOrEqual(5);
    expect($result['points'])->toBeLessThanOrEqual(100);
});

test('evaluate with keywords increases score', function () {
    // Sin keywords
    $baseResult = $this->service->evaluate(
        'Normal task',
        'This is a normal task description',
        now()->addDays(5)->toDateString()
    );

    // Con keywords de alta prioridad
    $priorityResult = $this->service->evaluate(
        'Urgent task',
        'This is an urgent and critical task that is very important',
        now()->addDays(5)->toDateString()
    );

    // Las keywords deberían aumentar el score
    expect($priorityResult['score'])->toBeGreaterThan($baseResult['score']);
});

test('score maps to appropriate difficulty level', function () {
    // Score bajo debería dar dificultad baja
    $easyResult = $this->service->evaluate(
        'Easy task',
        'Simple',
        now()->addDays(20)->toDateString()
    );

    // Score alto debería dar dificultad alta
    $longDescription = implode(' ', array_fill(0, 120, 'palabra'));
    $hardResult = $this->service->evaluate(
        'Hard urgent critical important task',
        $longDescription,
        now()->toDateString()
    );

    expect($easyResult['difficulty'])->toBeLessThanOrEqual($hardResult['difficulty']);
});
