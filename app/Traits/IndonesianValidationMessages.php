<?php

namespace App\Traits;

trait IndonesianValidationMessages
{
    protected function validationMessages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'string' => ':attribute harus berupa teks.',
            'max' => ':attribute maksimal :max karakter.',
            'min' => ':attribute minimal :min karakter.',
            'exists' => ':attribute tidak valid.',
            'in' => ':attribute tidak valid.',
            'boolean' => ':attribute harus berupa pilihan ya/tidak.',
            'email' => ':attribute harus berupa alamat email yang valid.',
            'unique' => ':attribute sudah digunakan.',
        ];
    }
}
