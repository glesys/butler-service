<?php

namespace Butler\Service\Http\Controllers;

use Butler\Graphql\Concerns\HandlesGraphqlRequests;

class GraphqlController extends Controller
{
    use HandlesGraphqlRequests;
}
