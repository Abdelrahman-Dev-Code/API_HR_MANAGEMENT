<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class SanitizingFormRequest extends FormRequest
{
    /** @var array<int, string> */
    private const UNSANITIZED_FIELDS = [
        'password',
        'password_confirmation',
        'current_password',
        'token',
        'api_token',
        'access_token',
        'secret',
    ];

    protected function prepareForValidation(): void
    {
        $this->merge($this->sanitizeValues($this->all()));
    }

    /**
     * @param array<array-key, mixed> $values
     * @return array<array-key, mixed>
     */
    private function sanitizeValues(array $values): array
    {
        foreach ($values as $key => $value) {
            if (is_string($value) && ! in_array((string) $key, self::UNSANITIZED_FIELDS, true)) {
                $values[$key] = $this->sanitizeString($value);

                continue;
            }

            if (is_array($value)) {
                $values[$key] = $this->sanitizeValues($value);
            }
        }

        return $values;
    }

    private function sanitizeString(string $value): string
    {
        $value = str_replace("\0", '', $value);
        $value = preg_replace('/[\x01-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value) ?? $value;

        return trim(strip_tags($value));
    }
}
