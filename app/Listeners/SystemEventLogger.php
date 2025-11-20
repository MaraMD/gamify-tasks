<?php

namespace App\Listeners;

use App\Models\SystemEvent;
use Illuminate\Support\Facades\Request;

class SystemEventLogger
{
    /**
     * Registra un evento en la tabla system_events
     *
     * @param array $data Datos del evento: type, entity_type, entity_id, message
     */
    public static function log(array $data): void
    {
        $event = [
            'user_id' => auth()->id(),
            'type' => $data['type'] ?? 'unknown',
            'entity_type' => $data['entity_type'] ?? null,
            'entity_id' => $data['entity_id'] ?? null,
            'message' => $data['message'] ?? null,
            'ip' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ];

        SystemEvent::create($event);
    }
}
