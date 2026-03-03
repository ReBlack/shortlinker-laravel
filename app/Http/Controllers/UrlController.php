<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShortHashRequest;
use App\Http\Requests\UrlRequest;
use App\Models\Url;
use App\Services\Bridges\IQrCodeForUrl;
use App\Services\CreateNewShortLinkForUrl;
use App\Services\CreateQrForShortHash;
use App\Services\GetUrlByShortHashAndIncVisits;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class UrlController extends Controller
{
    public function index()
    {
        return view('url.index');
    }

    public function shorten(UrlRequest $request): JsonResponse
    {
        $service = new CreateNewShortLinkForUrl(
            url: $request->validated('url'),
            urlModel: new Url(),
        );

        $urlModelResult = $service->handle();

        if (!$urlModelResult) {
            return response()->json([
                'success' => false,
                'errors' => ['Произошла ошибка при генерации'],
            ], 422);
        }

        return response()->json([
            'success' => true,
            'payload' => [
                'shortUrl' => $urlModelResult->getShortUrlString(config('app.short_link_template')),
                'shortHash' => $urlModelResult->short_hash,
                'url' => $urlModelResult->getUrlString(),
            ],
        ]);
    }

    public function qr(ShortHashRequest $request, string $shortHash, IQrCodeForUrl $qrForUrl): Response
    {
        $service = new CreateQrForShortHash(
            shortHash: $shortHash,
            shortUrlTemplate: config('app.short_link_template'),
            urlModel: new Url(),
            qrForUrl: $qrForUrl,
        );

        $pngData = $service->handle();

        if (!$pngData) {
            abort(422, 'Произошла ошибка при генерации QR кода');
        }

        return response($pngData)->header('Content-Type', 'image/png');
    }

    public function redirect(ShortHashRequest $request, string $shortHash): RedirectResponse
    {
        $service = new GetUrlByShortHashAndIncVisits(
            shortHash: $shortHash,
            urlModel: new Url(),
        );

        $urlString = $service->handle();

        if (!$urlString) {
            abort(404);
        }

        return redirect($urlString);
    }
}
