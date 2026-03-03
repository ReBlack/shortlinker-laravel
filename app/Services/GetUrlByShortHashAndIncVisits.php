<?php

namespace App\Services;

use App\Services\Models\IUrlModel;

class GetUrlByShortHashAndIncVisits implements IService
{
    public function __construct(
        private readonly string $shortHash,
        private readonly IUrlModel $urlModel,
    ) {

    }

    public function handle()
    {
        $urlEntity = $this->urlModel->getByShortHash($this->shortHash);

        if (!$urlEntity) {
            return null;
        }

        $urlEntity->incVisits();

        return  $urlEntity->getUrlString();
    }
}
