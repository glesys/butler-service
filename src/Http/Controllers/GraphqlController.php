<?php

namespace Butler\Service\Http\Controllers;

use Butler\Graphql\Concerns\HandlesGraphqlRequests;
use GraphQL\Language\AST\DocumentNode;
use GraphQL\Type\Schema;

class GraphqlController extends Controller
{
    use HandlesGraphqlRequests;

    protected function beforeExecutionHook(Schema $schema, DocumentNode $source): void
    {
        collect($source->toArray(true)['definitions'] ?? null)
            ->pluck('operation')
            ->unique()
            ->filter()
            ->whenEmpty(function () {
                abort(400, 'Invalid operation.');
            })
            ->each(function ($operation) {
                $this->authorize('graphql', $operation);
            });
    }
}
