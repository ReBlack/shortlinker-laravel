<?php

namespace Tests\Unit\Services;

use App\Services\Bridges\IQrCodeForUrl;
use App\Services\CreateQrForShortHash;
use App\Services\Models\IUrlModel;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CreateQrForShortHashTest extends TestCase
{
    private IUrlModel&MockObject $urlModelMock;
    private IQrCodeForUrl&MockObject $qrForUrlMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->urlModelMock = $this->createMock(IUrlModel::class);
        $this->qrForUrlMock = $this->createMock(IQrCodeForUrl::class);
    }

    public static function successProvider(): array
    {
        return [
            'basic short hash' => [
                'shortHash' => 'aBcD1234',
                'template' => 'https://short.ly/{hash}',
                'shortUrl' => 'https://short.ly/aBcD1234',
                'pngData' => "\x89PNG_FAKE_DATA_1",
            ],
            'different template' => [
                'shortHash' => 'xYzW5678',
                'template' => 'https://s.io/{hash}',
                'shortUrl' => 'https://s.io/xYzW5678',
                'pngData' => "\x89PNG_FAKE_DATA_2",
            ],
            'template with path prefix' => [
                'shortHash' => 'qWeR9012',
                'template' => 'https://example.com/go/{hash}',
                'shortUrl' => 'https://example.com/go/qWeR9012',
                'pngData' => "\x89PNG_FAKE_DATA_3",
            ],
        ];
    }

    #[DataProvider('successProvider')]
    public function test_returns_qr_png_data_for_valid_short_hash(
        string $shortHash,
        string $template,
        string $shortUrl,
        string $pngData,
    ): void {
        $urlEntity = $this->createMock(IUrlModel::class);
        $urlEntity
            ->expects($this->once())
            ->method('getShortUrlString')
            ->with($template)
            ->willReturn($shortUrl);

        $this->urlModelMock
            ->expects($this->once())
            ->method('getByShortHash')
            ->with($shortHash)
            ->willReturn($urlEntity);

        $this->qrForUrlMock
            ->expects($this->once())
            ->method('generate')
            ->with($shortUrl)
            ->willReturn($pngData);

        $service = new CreateQrForShortHash(
            shortHash: $shortHash,
            shortUrlTemplate: $template,
            urlModel: $this->urlModelMock,
            qrForUrl: $this->qrForUrlMock,
        );

        $result = $service->handle();

        $this->assertSame($pngData, $result);
    }

    public static function notFoundProvider(): array
    {
        return [
            'alphanumeric hash' => [
                'shortHash' => 'aBcD1234',
            ],
            'all digits' => [
                'shortHash' => '12345678',
            ],
            'all letters' => [
                'shortHash' => 'abcdEFGH',
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

        $this->qrForUrlMock
            ->expects($this->never())
            ->method('generate');

        $service = new CreateQrForShortHash(
            shortHash: $shortHash,
            shortUrlTemplate: 'https://short.ly/{hash}',
            urlModel: $this->urlModelMock,
            qrForUrl: $this->qrForUrlMock,
        );

        $result = $service->handle();

        $this->assertNull($result);
    }
}
