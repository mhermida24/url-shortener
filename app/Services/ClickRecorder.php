<?php

namespace App\Services;

use App\Models\Click;
use App\Models\Link;
use DeviceDetector\DeviceDetector;
use Illuminate\Http\Request;

class ClickRecorder
{
    public function record(Link $link, Request $request): Click
    {
        $userAgent = $request->userAgent() ?? '';

        $detector = new DeviceDetector($userAgent);
        $detector->parse();

        return $link->clicks()->create([
            'ip'         => $request->ip(),
            'user_agent' => $userAgent,
            'browser'    => $detector->getClient('name') ?: 'Desconocido',
            'os'         => $detector->getOs('name') ?: 'Desconocido',
            'device'     => $detector->getDeviceName() ?: 'desktop',
            'referer'    => $request->headers->get('referer'),
        ]);
    }
}