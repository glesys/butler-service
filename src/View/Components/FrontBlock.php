<?php

namespace Butler\Service\View\Components;

use Illuminate\View\Component;

class FrontBlock extends Component
{
    public function __construct(
        public string $title,
        public ?string $httpMethod = null,
        public ?string $responseType = null,
        public ?string $url = null,
        public bool $requiresToken = false,
    ) {
    }

    public function render()
    {
        return view('service::components.front-block');
    }
}
