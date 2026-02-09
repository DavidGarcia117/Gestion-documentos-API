<?php

namespace App\Services;

use App\Models\Notification;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function send($document, $type, $details)
    {
        $subject = $type === 'success' ? 'Radicación Exitosa' : 'Error en Radicación';
        
        // Guardar en DB
        Notification::create([
            'document_id' => $document->id,
            'type' => $type,
            'recipient_email' => $document->email_recipient ?? 'proveedor@ejemplo.com',
            'subject' => $subject,
            'body' => $details,
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        // Escribir en Log (storage/logs/laravel.log)
        Log::info("EMAIL ENVIADO A: {$document->email_recipient} | ASUNTO: {$subject} | MENSAJE: {$details}");
    }
}