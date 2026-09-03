<?php

namespace App\Seo\Http\Middleware;

use App\Markets\MarketContext;
use App\Seo\Redirector;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleRedirects
{
    public function __construct(private readonly Redirector $redirector) {}

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $market = $request->attributes->get('market') ?? app(MarketContext::class);
        $redirect = $this->redirector->match($request, $market);

        if (! $redirect) {
            return $next($request);
        }

        $target = $redirect->to_url;

        if (str_starts_with($target, '/') && $market instanceof MarketContext && $market->pathPrefix) {
            $target = rtrim($market->prefix().$target, '/') ?: '/';
        }

        $query = $request->getQueryString();

        if ($query) {
            $target .= (str_contains($target, '?') ? '&' : '?').$query;
        }

        return redirect()->to($target, $redirect->status);
    }
}
