<?php

declare(strict_types=1);

namespace Butler\Service\Http\Controllers;

use Butler\Auth\AccessToken;
use Butler\Service\Models\Consumer;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Middleware\SetCacheHeaders;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;

class TokensController implements HasMiddleware
{
    use ValidatesRequests;

    public static function middleware(): array
    {
        return [
            Authenticate::using('web'),
            SetCacheHeaders::using('no_store'),
        ];
    }

    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            return AccessToken::with('tokenable')->get()->map(fn ($token) => [
                'id' => $token->id,
                'name' => $token->name,
                'owner' => $token->tokenable?->name,
                'abilities' => $token->abilities,
                'last_used_at' => $token->last_used_at?->toDateTimeString(),
                'created_at' => $token->created_at->toDateTimeString(),
                'is_stale' => $token->last_used_at
                    ? $token->last_used_at->diffInMonths() > 2
                    : $token->created_at->diffInMonths() > 2,
            ]);
        }

        return view('butler::tokens');
    }

    public function store(Request $request)
    {
        $input = $this->validate($request, [
            'consumer' => 'required|string',
            'abilities' => 'required|array|min:1',
            'abilities.*' => 'required|string',
            'name' => 'nullable|string',
        ]);

        $consumer = Consumer::firstOrCreate(['name' => $input['consumer']]);

        $token = $consumer->createToken($input['abilities'], $input['name']);

        return $token->toArray();
    }

    public function destroy(Request $request)
    {
        $input = $this->validate($request, ['ids' => 'required|array']);

        AccessToken::destroy($input['ids']);

        return response()->noContent();
    }
}
