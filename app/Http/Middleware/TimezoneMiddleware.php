<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\Request;

class TimezoneMiddleware
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws \Exception
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $default_timezone = 'UTC';
        $timezone_cookie = Cookie::get('timezone');
        $visitor_timezone = geoip()->getLocation(request()->ip())->timezone;

        if (($timezone_cookie === null) || (!in_array($timezone_cookie, \DateTimeZone::listIdentifiers()))) {
            $this->setTimezone($visitor_timezone);
            return $response->withCookie(cookie()->forever('timezone', $visitor_timezone));
        } elseif (in_array($timezone_cookie, \DateTimeZone::listIdentifiers())) {
            $this->setTimezone($timezone_cookie);
            return $response->withCookie(cookie()->forever('timezone', $timezone_cookie));
        } else {
            $this->setTimezone($default_timezone);
            return $response->withCookie(cookie()->forever('timezone', $default_timezone));
        }

        return $response;
    }

    /**
     * @param $timezone
     */
    public function setTimezone($timezone)
    {
        config(['app.timezone' => $timezone]);
        date_default_timezone_set($timezone);
    }
}
