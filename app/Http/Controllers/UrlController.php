<?php

namespace App\Http\Controllers;

use App\Http\Requests\UrlrRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Url;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Response;



class UrlController extends Controller
{
    const MAX_REQUESTS = 3;
    const BLOCK_DURATION = 5;

    public function shortUrl(UrlrRequest $request, Url $urlModel)
    {
       

        $ip = $request->ip();

        $blockedUrl = $this->isIPBlocked($ip);

        if ($blockedUrl) {
            return $this->handleBlockedIP(Carbon::parse($blockedUrl->blocked_until));
        }

        $recentRequests = $this->getRecentRequestsCount($ip);

        if ($recentRequests >= self::MAX_REQUESTS) {
            return $this->handleExceededRequests($ip);
        }

        $key = $this->generateKey();

        $user=Auth::user()->id;


        $url = $urlModel->create([
            'original_url' => $request->original_url,
            'key' => $key,
            'user_id' => $user,
            'ip' => $ip
           
        ]);

        $shortenedUrl = url("/$key");

        return response()->json([
            'status' => 'success',
            'Response' => Response::HTTP_OK,
            'success' => 'Short URL generated successfully!', 'shortenedUrl' => $shortenedUrl
        ]);
    }

   

    private function isIPBlocked($ip)
    {
        return Url::where('ip', $ip)->where('blocked_until', '>', now())->first();
    }

    private function handleBlockedIP($blockedUntil)
    {
        return response()->json([
            'status' => 'error',
            'Response' => Response::HTTP_FORBIDDEN,
            'error' => "Your IP address is currently blocked. Please try again after {$blockedUntil->diffForHumans()}",
        ]);
    }

    private function getRecentRequestsCount($ip)
    {
        return Url::where('created_at', '>=', now()->subMinutes(1))->where('ip', $ip)->count();
    }

    private function handleExceededRequests($ip)
    {
        $blockedUntil = now()->addMinutes(self::BLOCK_DURATION);
        Url::where('ip', $ip)->update(['block_count' => \DB::raw('block_count + 1'), 'blocked_until' => $blockedUntil]);

        return response()->json([
            'status' => 'error',
            'Response' => Response::HTTP_TOO_MANY_REQUESTS,
            'error' => 'You have exceeded the maximum number of requests allowed for your IP address. Please try again in 5 minutes.'
        ]);
    }

    private function generateKey()
    {
        return substr(md5(uniqid()), 0, 6);
    }
}

