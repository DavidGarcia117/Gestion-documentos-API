<?php

namespace App\Services;

class ValidationService
{
    public function validate(array $data): array
    {
        $errors = [];

        if ($data['nit'] === 'No encontrado') $errors[] = "El NIT es obligatorio.";
        if ($data['amount'] === 'No encontrado') $errors[] = "El valor total no es legible.";
        if ($data['contract_number'] === 'No encontrado') $errors[] = "El número de contrato es requerido.";

        return [
            'is_valid' => empty($errors),
            'errors' => $errors
        ];
    }
}