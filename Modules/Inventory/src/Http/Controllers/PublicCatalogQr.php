<?php

namespace Modules\Inventory\Http\Controllers;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Http\Response;
use Modules\Inventory\Models\QrCatalog;

class PublicCatalogQr
{
    public function __invoke(string $token): Response
    {
        abort_unless(
            QrCatalog::where('token', $token)->exists(),
            404
        );

        $png = (new QRCode(new QROptions([
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'scale' => 8,
            'quietzoneSize' => 4,
        ])))->render(route('inventory.catalog', $token));

        if (str_starts_with($png, 'data:image/png;base64,')) {
            $png = base64_decode(
                substr($png, strlen('data:image/png;base64,')),
                true
            );

            abort_if($png === false, 500, 'Invalid QR image data.');
        }

        $png = ltrim($png, "\x00\x09\x0A\x0B\x0D\x20");

        abort_unless(
            str_starts_with($png, "\x89PNG\r\n\x1A\n"),
            500,
            'Generated QR code is not a valid PNG image.'
        );

        return response($png, 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}
