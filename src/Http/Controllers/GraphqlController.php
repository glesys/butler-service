<?php

declare(strict_types=1);

namespace Butler\Service\Http\Controllers;

use Butler\Graphql\Concerns\HandlesGraphqlRequests;
use GraphQL\Language\AST\DocumentNode;
use GraphQL\Type\Schema;
use Illuminate\Auth\Middleware\Authenticate;

class GraphqlController extends Controller
{
    use HandlesGraphqlRequests;

    public function __construct()
    {
        $this->middleware(Authenticate::using('butler'));
    }

    protected function beforeExecutionHook(Schema $schema, DocumentNode $source): void
    {
        collect($source->toArray(true)['definitions'] ?? null)
            ->pluck('operation')
            ->unique()
            ->filter()
            ->whenEmpty(fn () => abort(400, 'Invalid operation.'))
            ->each(fn ($operation) => $this->authorize('graphql', $operation));
    }
}
