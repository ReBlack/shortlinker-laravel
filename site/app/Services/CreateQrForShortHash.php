<?php

namespace App\Services;

use App\Services\Bridges\IQrCodeForUrl;
use App\Services\Models\IUrlModel;

class CreateQrForShortHash implements IService
{
    public function __construct(
        private readonly string $shortHash,
        private readonly string $shortUrlTemplate,
        private readonly IUrlModel $urlModel,
        private readonly IQrCodeForUrl $qrForUrl,
    ) {

    }

    public function handle(): ?string
    {
        $urlEntity = $this->urlModel->getByShortHash($this->shortHash);

        if (!$urlEntity) {
            return null;
        }

        return $this->qrForUrl
            ->generate($urlEntity->getShortUrlString($this->shortUrlTemplate));
    }
}
