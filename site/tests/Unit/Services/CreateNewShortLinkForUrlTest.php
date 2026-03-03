<?php

namespace Tests\Unit\Services;

use App\Services\CreateNewShortLinkForUrl;
use App\Services\Models\IUrlModel;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CreateNewShortLinkForUrlTest extends TestCase
{
    private IUrlModel&MockObject $urlModelMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->urlModelMock = $this->createMock(IUrlModel::class);
    }

    public static function existingUrlProvider(): array
    {
        return [
            'simple url' => [
                'url' => 'https://example.com',
            ],
            'url with path' => [
                'url' => 'https://example.com/some/path',
            ],
            'url with query' => [
                'url' => 'https://example.com?foo=bar&baz=1',
            ],
        ];
    }

    #[DataProvider('existingUrlProvider')]
    public function test_returns_existing_url_model_when_url_already_exists(string $url): void
    {
        $existingModel = $this->createMock(IUrlModel::class);

        $this->urlModelMock
            ->expects($this->once())
            ->method('getByUrl')
            ->with($url)
            ->willReturn($existingModel);

        $this->urlModelMock
            ->expects($this->never())
            ->method('generateUniqueShortHash');

        $this->urlModelMock
            ->expects($this->never())
            ->method('insertUrl');

        $service = new CreateNewShortLinkForUrl(
            url: $url,
            urlModel: $this->urlModelMock,
        );

        $result = $service->handle();

        $this->assertSame($existingModel, $result);
    }

    public static function newUrlProvider(): array
    {
        return [
            'simple url' => [
                'url' => 'https://new-site.com',
                'shortHash' => 'aBcD1234',
            ],
            'url with path' => [
                'url' => 'https://new-site.com/page/1',
                'shortHash' => 'xYzW5678',
            ],
            'url with fragment' => [
                'url' => 'https://new-site.com/page#section',
                'shortHash' => 'qWeR9012',
            ],
        ];
    }

    #[DataProvider('newUrlProvider')]
    public function test_creates_new_short_link_when_url_not_exists(string $url, string $shortHash): void
    {
        $insertedModel = $this->createMock(IUrlModel::class);

        $this->urlModelMock
            ->expects($this->once())
            ->method('getByUrl')
            ->with($url)
            ->willReturn(null);

        $this->urlModelMock
            ->expects($this->once())
            ->method('generateUniqueShortHash')
            ->willReturn($shortHash);

        $this->urlModelMock
            ->expects($this->once())
            ->method('insertUrl')
            ->with($url, $shortHash)
            ->willReturn($insertedModel);

        $service = new CreateNewShortLinkForUrl(
            url: $url,
            urlModel: $this->urlModelMock,
        );

        $result = $service->handle();

        $this->assertSame($insertedModel, $result);
    }

    public function test_returns_null_when_short_hash_generation_fails(): void
    {
        $this->urlModelMock
            ->expects($this->once())
            ->method('getByUrl')
            ->willReturn(null);

        $this->urlModelMock
            ->expects($this->once())
            ->method('generateUniqueShortHash')
            ->willReturn(null);

        $this->urlModelMock
            ->expects($this->never())
            ->method('insertUrl');

        $service = new CreateNewShortLinkForUrl(
            url: 'https://example.com',
            urlModel: $this->urlModelMock,
        );

        $result = $service->handle();

        $this->assertNull($result);
    }

    public function test_returns_null_when_insert_fails(): void
    {
        $this->urlModelMock
            ->method('getByUrl')
            ->willReturn(null);

        $this->urlModelMock
            ->method('generateUniqueShortHash')
            ->willReturn('abCD1234');

        $this->urlModelMock
            ->expects($this->once())
            ->method('insertUrl')
            ->willReturn(null);

        $service = new CreateNewShortLinkForUrl(
            url: 'https://example.com',
            urlModel: $this->urlModelMock,
        );

        $result = $service->handle();

        $this->assertNull($result);
    }
}
