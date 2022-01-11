<?php

namespace LaravelAnnotation;

use Illuminate\Routing\Controller;
use Illuminate\Routing\ControllerMiddlewareOptions;
use ReflectionAttribute;
use ReflectionClass;

class BaseController extends Controller
{
    /**
     * Get the middleware assigned to the controller.
     *
     * @return array
     */
    public function getMiddleware(): array
    {
        return [...$this->middleware, ...$this->getMiddlewaresByAttributes()];
    }

    /**
     * Get the controller middlewares by attributes
     *
     * @see Middleware
     *
     * @return array
     */
    public function getMiddlewaresByAttributes(): array
    {
        $middlewares = [];

        /** @var ReflectionAttribute[] $attributes */
        $push = function (array $attributes, ?string $method = null) use (&$middlewares) {
            foreach ($attributes as $attribute) {
                /** @var Middleware $middleware */
                $middleware = $attribute->newInstance();

                $name = $middleware->name;
                $arguments = $middleware->arguments ? ':'.implode(',', (array) $middleware->arguments) : '';

                $middlewares[] = [
                    'middleware' => $name.$arguments,
                    'options' => &$middleware->options
                ];

                $middlewareOptions = new ControllerMiddlewareOptions($middleware->options);

                if ($method) $middlewareOptions->only((array) $method);
                elseif ($middleware->only) $middlewareOptions->only((array) $middleware->only);
                elseif ($middleware->except) $middlewareOptions->except((array) $middleware->except);
            }
        };

        $class = new ReflectionClass($this);

        // Class
        $push($class->getAttributes(Middleware::class));

        // Methods
        foreach ($class->getMethods() as $method) {
            $push($method->getAttributes(Middleware::class), $method->name);
        }

        return $middlewares;
    }
}
