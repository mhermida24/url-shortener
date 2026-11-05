<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Services\ClickRecorder;
use Illuminate\Http\Request;

class RedirectController extends Controller
{
    public function __invoke(Request $request, string $code, ClickRecorder $recorder)
    {
        $link = Link::where('code', $code)->firstOrFail();

        if ($link->expires_at && $link->expires_at->isPast()) {
            abort(410, 'Este enlace expiró.');
        }

        if ($link->max_clicks && $link->clicks()->count() >= $link->max_clicks) {
            abort(410, 'Este enlace alcanzó su límite de clics.');
        }

        $recorder->record($link, $request);

        return redirect()->away($link->original_url);
    }
}