<?php

namespace Butler\Service\Http\Controllers;

use Butler\Auth\AccessToken;
use Butler\Service\Models\Consumer;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class TokensController extends BaseController
{
    use ValidatesRequests;

    public function __construct()
    {
        $this->middleware('auth:web');
        $this->middleware('cache.headers:no_store,no_cache,must_revalidate');
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
