<?php

declare(strict_types=1);

namespace Butler\Service\Http\Controllers;

use Butler\Graphql\Concerns\HandlesGraphqlRequests;
use GraphQL\Language\AST\DocumentNode;
use GraphQL\Language\AST\FieldNode;
use GraphQL\Language\AST\OperationDefinitionNode;
use GraphQL\Type\Schema;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Gate;

class GraphqlController implements HasMiddleware
{
    use AuthorizesRequests;
    use HandlesGraphqlRequests;

    public static function middleware(): array
    {
        return [
            Authenticate::using('butler'),
        ];
    }

    public function schemaCacheKey(): string
    {
        return gethostname() . ':' . config('butler.graphql.schema_cache_key');
    }

    protected function beforeExecutionHook(Schema $schema, DocumentNode $source): void
    {
        $this->authorizeQuery($source);
    }

    protected function authorizeQuery(DocumentNode $source): void
    {
        collect($source->definitions)
            ->filter(fn ($definition) => $definition instanceof OperationDefinitionNode)
            ->each(function (OperationDefinitionNode $definition) {
                $operation = $definition->operation;

                if (Gate::allows('graphql', $operation)) {
                    return;
                }

                collect($definition->selectionSet->selections)
                    ->map(fn (FieldNode $node) => $node->name->value)
                    ->reject(fn (string $type) => $this->isIntrospectionType($operation, $type))
                    ->each(fn (string $type) => Gate::authorize('graphql', "{$operation}:{$type}"));
            });
    }

    protected function isIntrospectionType(string $operation, string $type): bool
    {
        return $operation === 'query' && in_array(strtolower($type), [
            '__schema',
            '__type',
            '__typename',
        ]);
    }
}
