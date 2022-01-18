<?php

namespace LaravelAnnotation\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Middleware
{
    /**
     * @param string $name
     * @param string|object|string[]|object[] $arguments
     * @param string|string[] $only
     * @param string|string[] $except
     */
    function __construct(public string $name, public string | object | array $arguments = [], public string | array $only = [], public string | array $except = [])
    {
    }

    public array $options = [];
}
