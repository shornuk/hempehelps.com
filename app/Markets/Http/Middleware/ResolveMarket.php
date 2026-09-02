<?php

namespace App\Markets\Http\Middleware;

use App\Markets\MarketContext;
use App\Markets\MarketResolver;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveMarket
{
    public function __construct(private readonly MarketResolver $resolver) {}

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $context = $this->resolver->resolve($request);
        $request->attributes->set('market', $context);
        app()->instance(MarketContext::class, $context);

        return $next($request);
    }
}
