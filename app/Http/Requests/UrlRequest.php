<?php

namespace App\Http\Requests;

use Closure;
use Illuminate\Foundation\Http\FormRequest;

class UrlRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $url = $this->input('url');

        if ($url && !preg_match('#^https?://#i', $url)) {
            $this->merge(['url' => 'https://' . $url]);
        }
    }

    public function rules(): array
    {
        return [
            'url' => ['required', 'url:http,https', function (string $attribute, mixed $value, Closure $fail) {
                if (!$this->isUrlReachable($value)) {
                    $fail('Введенный URL недоступен');
                }
            }],
        ];
    }

    public function messages(): array
    {
        return [
            'url.required' => 'Пожалуйста, введите URL адрес.',
            'url.url' => 'Введите корректный URL (например, https://example.com).',
        ];
    }

    protected function isUrlReachable(string $url): bool
    {
        $host = parse_url($url, PHP_URL_HOST);

        if (!$host) {
            return false;
        }

        $ip = gethostbyname($host);
        if ($ip === $host || !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return false;
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_NOBODY => true,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_RETURNTRANSFER => true,
        ]);
        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode >= 200 && $httpCode < 400;
    }
}
