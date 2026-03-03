<?php

namespace App\Services\Models;

interface IUrlModel
{
    public function insertUrl(string $url, string $shortHash, int $visits_cnt = 0): ?IUrlModel;
    public function getByUrl(string $url): ?IUrlModel;
    public function getByShortHash(string $shortHash): ?IUrlModel;
    public function generateUniqueShortHash();
    public function getShortUrlString(string $template);
    public function getUrlString(): string;
    public function incVisits(): int;
}
