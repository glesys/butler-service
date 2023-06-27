<?php

namespace Butler\Service\Http\Controllers;

use Butler\Auth\AccessToken;
use Butler\Service\Http\Middleware\Authenticate;
use Butler\Service\Models\Consumer;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Middleware\SetCacheHeaders;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class TokensController extends BaseController
{
    use ValidatesRequests;

    public function __construct()
    {
        $this->middleware(Authenticate::using('web'));
        $this->middleware(SetCacheHeaders::using('no_store'));
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

    public function store()
    {
        $input = $this->validate(request(), [
            'consumer' => 'required|string',
            'abilities' => 'required|array|min:1',
            'abilities.*' => 'required|string',
            'name' => 'nullable|string',
        ]);

        $consumer = Consumer::firstOrCreate(['name' => $input['consumer']]);

        $token = $consumer->createToken($input['abilities'], $input['name']);

        return $token->toArray();
    }

    public function destroy()
    {
        $input = $this->validate(request(), ['ids' => 'required|array']);

        AccessToken::destroy($input['ids']);

        return response()->noContent();
    }
}
