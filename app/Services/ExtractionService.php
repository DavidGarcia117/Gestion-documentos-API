<?php

namespace App\Services;

use Smalot\PdfParser\Parser;
use Exception;

class ExtractionService
{
    public function extract(string $filePath, string $mimeType): array
    {
        if ($mimeType === 'application/pdf') {
            try {
                $parser = new Parser();
                $pdf = $parser->parseFile(storage_path('app/public/' . $filePath));
                $text = $pdf->getText();

                return [
                    // Busca NIT: 123.456 o NIT 123456
                    'nit' => $this->extractNit($text),
                    
                    // Busca TOTAL, VALOR o $ seguido de números
                    'amount' => $this->extractAmount($text),
                    
                    // Busca CONTRATO o NÚMERO seguido de letras/números
                    'contract_number' => $this->extractContract($text),
                    
                    'raw_text' => mb_substr($text, 0, 500)
                ];
            } catch (Exception $e) {
                return ['error' => 'Error al leer PDF: ' . $e->getMessage()];
            }
        }

        return [];
    }

    private function extractNit($text) {
        // Regex flexible para NIT
        if (preg_match('/NIT[:\s]*([\d\.-]+)/i', $text, $matches)) {
            return preg_replace('/[^0-9]/', '', $matches[1]);
        }
        return null;
    }

    private function extractAmount($text) {
        // Regex para capturar montos con $, puntos o comas
        if (preg_match('/(?:TOTAL|VALOR|PAGO|[\$])[:\s]*([\d\.,\s]+)/i', $text, $matches)) {
            $value = preg_replace('/[^\d]/', '', $matches[1]); // Quita todo menos números
            return $value !== '' ? (float) $value : null;
        }
        return null;
    }

    private function extractContract($text) {
        // Busca la palabra CONTRATO o NÚMERO
        if (preg_match('/(?:CONTRATO|N[ÚU]MERO)[:\s]*([A-Z0-9-]+)/i', $text, $matches)) {
            return $matches[1];
        }
        return null;
    }
}