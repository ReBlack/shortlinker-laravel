<?php

namespace App\Models;

use App\Services\Models\IUrlModel;
use Illuminate\Database\Eloquent\Model;

class Url extends Model implements IUrlModel
{
    public const GENERATE_SHORT_ATTEMPTS = 5;

    protected $fillable = [
        'url',
        'short_hash',
        'visits_cnt',
    ];

    public function insertUrl(string $url, string $shortHash, int $visits_cnt = 0): ?IUrlModel
    {
        $this->url = $url;
        $this->short_hash = $shortHash;
        $this->visits_cnt = $visits_cnt;
        $result = $this->save();

        return $result ? $this : null;
    }

    public function getByUrl(string $url): ?IUrlModel
    {
        return self::where('url', $url)->first();
    }

    public function getByShortHash(string $shortHash): ?IUrlModel
    {
        return self::where('short_hash', $shortHash)->first();
    }

    public function generateUniqueShortHash($attempts = self::GENERATE_SHORT_ATTEMPTS): ?string
    {
        $attempt = 0;
        while ($attempt++ < $attempts) {
            $md5Hash = md5(uniqid(rand(), true));
            $shortHashes = str_split($md5Hash, 8);
            $existsShortHashes = $this->getExistingByShortHashes($shortHashes);
            $uniqueShortHashes = array_diff($shortHashes, $existsShortHashes);

            if (!empty($uniqueShortHashes)) {
                return current($uniqueShortHashes);
            }
        }

        return null;
    }

    public function incVisits(): int
    {
        $this->visits_cnt++;
        $this->save();

        return $this->visits_cnt;
    }

    protected function getExistingByShortHashes(array $shortHashes): array
    {
        if (!$shortHashes) {
            return [];
        }

        return self::whereIn('short_hash', $shortHashes)
            ->pluck('short_hash')
            ->toArray();
    }

    public function getShortUrlString(string $template): string
    {
        return (string)str_replace('{short_hash}', $this->short_hash, $template);
    }

    public function getUrlString(): string
    {
        return $this->url;
    }
}
