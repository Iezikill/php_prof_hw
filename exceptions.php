<?php

function foo()
{
  try {
    throw new Exception('error');
  } catch (Exception $exception) {
    return false;
  }
  return true;
}

var_dump(foo());
