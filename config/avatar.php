<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Avatar Base Path
    |--------------------------------------------------------------------------
    |
    | Base path for Kenney Modular Characters assets, relative to public/
    |
    */
    'base_path' => 'assets/avatars/kenney',

    /*
    |--------------------------------------------------------------------------
    | Layer Rendering Order
    |--------------------------------------------------------------------------
    |
    | The order in which avatar layers are stacked (bottom to top).
    | These are logical layer names used in the database fields.
    |
    */
    'order' => [
        'body',      // Skin - base layer
        'eyes',      // Face - over skin
    ],

    /*
    |--------------------------------------------------------------------------
    | Folder Mapping
    |--------------------------------------------------------------------------
    |
    | Maps logical layer names to actual Kenney pack folder names.
    | This allows the code to use consistent names while supporting
    | the actual folder structure from the Kenney Modular Characters pack.
    |
    */
    'folders' => [
        'body'   => 'skin',
        'eyes'   => 'face',
    ],
];
