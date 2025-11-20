<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShoppingItem extends Model
{
    /** @use HasFactory<\Database\Factories\ShoppingItemFactory> */
    use HasFactory;

    protected $fillable = ['shopping_list_id', 'name', 'quantity', 'checked'];

    protected $casts = [
        'checked' => 'boolean',
        'quantity' => 'integer',
    ];

    public function shoppingList()
    {
        return $this->belongsTo(ShoppingList::class);
    }
}
