<?php

namespace Viktoriya\PHP2\Http\Actions;

use Viktoriya\PHP2\http\Request;
use Viktoriya\PHP2\http\Response;

interface ActionInterface
{
  public function handle(Request $request): Response;
}
