<?php

if (! function_exists('sqlite_database_path')) {
    function sqlite_database_path(?string $path, ?string $driver): ?string
    {
        if ($driver === 'sqlite' && $path !== ':memory:') {
            return app()->databasePath($path);
        }

        return $path;
    }
}

if (! function_exists('is_graphql')) {
    function is_graphql(string $query): bool
    {
        try {
            GraphQL\Language\Parser::parse($query);
        } catch (Exception $_) {
            return false;
        }

        return true;
    }
}
