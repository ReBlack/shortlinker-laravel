<?php

namespace Tests\Unit\Services;

use App\Services\GetUrlByShortHashAndIncVisits;
use App\Services\Models\IUrlModel;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GetUrlByShortHashAndIncVisitsTest extends TestCase
{
    private IUrlModel&MockObject $urlModelMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->urlModelMock = $this->createMock(IUrlModel::class);
    }

    public static function successProvider(): array
    {
        return [
            'simple url' => [
                'shortHash' => 'aBcD1234',
                'expectedUrl' => 'https://example.com',
            ],
            'url with path' => [
                'shortHash' => 'xYzW5678',
                'expectedUrl' => 'https://example.com/some/long/path',
            ],
            'url with query string' => [
                'shortHash' => 'qWeR9012',
                'expectedUrl' => 'https://example.com?foo=bar&baz=qux',
            ],
            'http url' => [
                'shortHash' => 'mNbV3456',
                'expectedUrl' => 'http://legacy-site.org/page',
            ],
        ];
    }

    #[DataProvider('successProvider')]
    public function test_returns_url_and_increments_visits(string $shortHash, string $expectedUrl): void
    {
        $urlEntity = $this->createMock(IUrlModel::class);

        $urlEntity
            ->expects($this->once())
            ->method('incVisits');

        $urlEntity
            ->expects($this->once())
            ->method('getUrlString')
            ->willReturn($expectedUrl);

        $this->urlModelMock
            ->expects($this->once())
            ->method('getByShortHash')
            ->with($shortHash)
            ->willReturn($urlEntity);

        $service = new GetUrlByShortHashAndIncVisits(
            shortHash: $shortHash,
            urlModel: $this->urlModelMock,
        );

        $result = $service->handle();

        $this->assertSame($expectedUrl, $result);
    }

    public static function notFoundProvider(): array
    {
        return [
            'random hash' => [
                'shortHash' => 'aBcD1234',
            ],
            'another hash' => [
                'shortHash' => 'NoTfOuNd',
            ],
        ];
    }

    #[DataProvider('notFoundProvider')]
    public function test_returns_null_when_short_hash_not_found(string $shortHash): void
    {
        $this->urlModelMock
            ->expects($this->once())
            ->method('getByShortHash')
            ->with($shortHash)
            ->willReturn(null);

        $service = new GetUrlByShortHashAndIncVisits(
            shortHash: $shortHash,
            urlModel: $this->urlModelMock,
        );

        $result = $service->handle();

        $this->assertNull($result);
    }

    public function test_does_not_increment_visits_when_not_found(): void
    {
        $urlEntity = $this->createMock(IUrlModel::class);

        $urlEntity
            ->expects($this->never())
            ->method('incVisits');

        $urlEntity
            ->expects($this->never())
            ->method('getUrlString');

        $this->urlModelMock
            ->expects($this->once())
            ->method('getByShortHash')
            ->with('notFound1')
            ->willReturn(null);

        $service = new GetUrlByShortHashAndIncVisits(
            shortHash: 'notFound1',
            urlModel: $this->urlModelMock,
        );

        $result = $service->handle();

        $this->assertNull($result);
    }
}
