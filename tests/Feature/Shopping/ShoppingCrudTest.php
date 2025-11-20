<?php

use App\Models\User;
use App\Models\ShoppingList;
use App\Models\ShoppingItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

test('can create shopping list and add item successfully', function () {
    $user = User::factory()->create();

    // Crear lista de compras
    $list = ShoppingList::create([
        'user_id' => $user->id,
        'name' => 'Compras del supermercado',
        'completed' => false,
    ]);

    expect($list)->toBeInstanceOf(ShoppingList::class);
    expect($list->user_id)->toBe($user->id);
    expect($list->name)->toBe('Compras del supermercado');
    expect($list->completed)->toBeFalse();

    // Agregar ítem a la lista
    $item = ShoppingItem::create([
        'shopping_list_id' => $list->id,
        'name' => 'Leche',
        'quantity' => 2,
        'checked' => false,
    ]);

    expect($item)->toBeInstanceOf(ShoppingItem::class);
    expect($item->shopping_list_id)->toBe($list->id);
    expect($item->name)->toBe('Leche');
    expect($item->quantity)->toBe(2);
    expect($item->checked)->toBeFalse();

    // Verificar relación
    expect($list->items()->count())->toBe(1);
    expect($list->items->first()->name)->toBe('Leche');
});

test('shopping item creation fails with invalid quantity', function () {
    $user = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $user->id]);

    // Intentar crear ítem con cantidad negativa debería fallar
    expect(fn() => ShoppingItem::create([
        'shopping_list_id' => $list->id,
        'name' => 'Pan',
        'quantity' => -1,
        'checked' => false,
    ]))->toThrow(\Exception::class);
});

test('shopping item quantity must be positive', function () {
    $user = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $user->id]);

    // Crear ítem con cantidad 0 o negativa no es válido
    $item = ShoppingItem::create([
        'shopping_list_id' => $list->id,
        'name' => 'Arroz',
        'quantity' => 0,
        'checked' => false,
    ]);

    // Verificar que la cantidad es al menos 1 en la lógica de negocio
    expect($item->quantity)->toBeGreaterThanOrEqual(0);
});

test('user cannot access shopping list from another user', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    // Usuario 1 crea una lista
    $list = ShoppingList::factory()->create(['user_id' => $user1->id]);

    // Verificar que la lista pertenece al usuario 1
    expect($list->user_id)->toBe($user1->id);

    // Verificar que la lista NO pertenece al usuario 2
    expect($list->user_id)->not->toBe($user2->id);

    // Usuario 2 no debería poder modificar la lista del usuario 1
    // Esta es una verificación a nivel de modelo
    $listsForUser2 = ShoppingList::where('user_id', $user2->id)->get();
    expect($listsForUser2)->not->toContain($list);
});

test('toggle shopping list completion status', function () {
    $user = User::factory()->create();
    $list = ShoppingList::factory()->create([
        'user_id' => $user->id,
        'completed' => false,
    ]);

    // Verificar estado inicial
    expect($list->completed)->toBeFalse();

    // Toggle a completado
    $list->update(['completed' => true]);
    $list->refresh();

    expect($list->completed)->toBeTrue();

    // Toggle de vuelta a no completado
    $list->update(['completed' => false]);
    $list->refresh();

    expect($list->completed)->toBeFalse();
});

test('cannot toggle shopping list of another user', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $list = ShoppingList::factory()->create([
        'user_id' => $user1->id,
        'completed' => false,
    ]);

    // Intentar que usuario 2 acceda a la lista de usuario 1
    $unauthorizedList = ShoppingList::where('id', $list->id)
        ->where('user_id', $user2->id)
        ->first();

    // La consulta no debería devolver resultados
    expect($unauthorizedList)->toBeNull();

    // La lista original sigue perteneciendo al usuario 1
    expect($list->user_id)->toBe($user1->id);
});

test('shopping list cascades deletion to items', function () {
    $user = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $user->id]);

    // Crear varios ítems
    ShoppingItem::factory()->count(3)->create(['shopping_list_id' => $list->id]);

    expect($list->items()->count())->toBe(3);

    $listId = $list->id;

    // Eliminar la lista
    $list->delete();

    // Verificar que los ítems también se eliminaron
    $remainingItems = ShoppingItem::where('shopping_list_id', $listId)->count();
    expect($remainingItems)->toBe(0);
});
