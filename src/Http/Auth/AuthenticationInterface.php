<?php

namespace Viktoriya\PHP2\Http\Auth;

use Viktoriya\PHP2\Blog\User;
use Viktoriya\PHP2\Http\Request;

interface AuthenticationInterface
{
  public function user(Request $request): User;
}
