<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Http\Request;

class RedirectController extends Controller
{
    public function __invoke(Request $request, string $code)
    {
        $link = Link::where('code', $code)->firstOrFail();

        if ($link->expires_at && $link->expires_at->isPast()) {
            abort(410, 'Este enlace expiró.');
        }

        if ($link->max_clicks && $link->clicks()->count() >= $link->max_clicks) {
            abort(410, 'Este enlace alcanzó su límite de clics.');
        }

        $link->clicks()->create([
            'ip'         => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referer'    => $request->headers->get('referer'),
        ]);

        return redirect()->away($link->original_url);
    }
}