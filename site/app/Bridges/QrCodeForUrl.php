<?php

namespace App\Bridges;

use App\Services\Bridges\IQrCodeForUrl;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class QrCodeForUrl implements IQrCodeForUrl
{
    public function generate(string $url): string
    {
        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'scale' => 4,
            'quietzoneSize' => 2,
            'outputBase64' => false,
        ]);

        return (new QRCode($options))->render($url);
    }
}
