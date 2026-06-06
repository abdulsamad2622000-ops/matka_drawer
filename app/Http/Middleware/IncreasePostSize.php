<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Middleware\ValidatePostSize;

class IncreasePostSize extends ValidatePostSize
{
    protected function getPostMaxSize(): int
    {
        return 1024 * 1024 * 100; // 100MB
    }
}