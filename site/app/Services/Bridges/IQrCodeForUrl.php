<?php

namespace App\Services\Bridges;

interface IQrCodeForUrl
{
    public function generate(string $url): string;
}
