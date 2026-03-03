<?php

namespace App\Services;

use App\Services\Models\IUrlModel;

class CreateNewShortLinkForUrl implements IService
{
    public function __construct(
        private readonly string $url,
        private readonly IUrlModel $urlModel,
    ) {

    }

    public function handle(): ?IUrlModel
    {
        $existsUrl = $this->urlModel->getByUrl($this->url);
        if ($existsUrl) {
            return $existsUrl;
        }

        $newShortCode = $this->urlModel->generateUniqueShortHash();
        if ($newShortCode) {
            return $this->urlModel->insertUrl($this->url, $newShortCode);
        }

        return null;
    }
}
