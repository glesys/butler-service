<?php

namespace Butler\Service\Database;

use Illuminate\Database\DatabaseManager as BaseDatabaseManager;

class DatabaseManager extends BaseDatabaseManager
{
    /**
     * Remove connection from local cache.
     */
    public function forget(string $name = null): void
    {
        $name = $name ?: $this->getDefaultConnection();

        unset($this->connections[$name]);
    }
}
