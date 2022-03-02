<?php

namespace Butler\Service\Testing;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use Concerns\CreatesApplication;
    use Concerns\InteractsWithAuthentication;
    use Concerns\MakesGraphqlRequests;
    use Concerns\MigratesDatabases;
}
