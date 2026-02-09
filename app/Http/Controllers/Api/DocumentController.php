<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\AuditLog;
use App\Services\ExtractionService;
use App\Services\ValidationService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Exception;

class DocumentController extends Controller
{
    protected $extractor, $validator, $notifier;

    public function __construct(
        ExtractionService $extractor,
        ValidationService $validator,
        NotificationService $notifier
    ) {
        $this->extractor = $extractor;
        $this->validator = $validator;
        $this->notifier = $notifier;
    }

    public function filing(Request $request)
    {
        try {
            // 1. Validación de entrada
            $request->validate([
                'file' => 'required|mimes:pdf,xml|max:10240',
                'document_type' => 'required|in:contractor_invoice,supplier_invoice,general_invoice',
                'email' => 'required|email'
            ]);

            // 2. Guardar archivo
            $file = $request->file('file');
            $path = $file->store('documents', 'public');

            // 3. Crear registro
            $document = Document::create([
                'filing_number' => 'RAD-' . strtoupper(Str::random(8)),
                'document_type' => $request->document_type,
                'status' => 'processing',
                'original_filename' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getClientMimeType(),
                'email_recipient' => $request->email,
                'filed_at' => now()
            ]);

            // 4. Extracción
            $extractedData = $this->extractor->extract($path, $file->getClientMimeType());
            $document->update(['extracted_data' => $extractedData, 'processed_at' => now()]);

            // 5. Validación de negocio
            $validation = $this->validator->validate($extractedData);

            if ($validation['is_valid']) {
                $document->update(['status' => 'validated', 'validated_at' => now()]);
                
                AuditLog::create([
                    'document_id' => $document->id,
                    'action' => 'VALIDATION_SUCCESS',
                    'ip_address' => $request->ip(),
                    'changes' => json_encode(['status' => 'validated'])
                ]);

                $this->notifier->send($document, 'success', 'Radicación exitosa.');

                return response()->json([
                    'success' => true,
                    'filing_number' => $document->filing_number
                ], 201);
            } else {
                $document->update(['status' => 'rejected', 'validation_errors' => $validation['errors']]);
                
                $this->notifier->send($document, 'error', 'Errores: ' . implode(', ', $validation['errors']));

                return response()->json([
                    'success' => false,
                    'errors' => $validation['errors']
                ], 422);
            }

        } catch (Exception $e) {
            return response()->json([
                'error' => 'Error interno',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }
}