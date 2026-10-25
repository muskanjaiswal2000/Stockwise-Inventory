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
        abort_unless(QrCatalog::where('token', $token)->exists(), 404);

        $png = (new QRCode(new QROptions([
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'scale' => 8,
            'quietzoneSize' => 4,
        ])))->render(route('inventory.catalog', $token));

        if (str_starts_with($png, 'data:image/')) {
            $png = base64_decode(substr($png, strpos($png, ',') + 1), true);
        }

        return response($png, 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
