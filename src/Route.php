<?php

declare(strict_types=1);

namespace Lex\Yii2\Router;

use yii\web\UrlRule;

final class Route implements RouteInterface
{
    public UrlRule $instance;

    public function __construct(UrlRule $instance)
    {
        $this->instance = $instance;
    }

    public static function create(string $pattern, string $path): RouteInterface
    {
        $rule = new UrlRule([
            'pattern' => $pattern,
            'route' => $path
        ]);
        return new self($rule);
    }

    public function pattern(string $pattern): RouteInterface
    {
        $this->instance->pattern = $pattern;
        return $this;
    }

    public function getPattern(): string
    {
        return $this->instance->pattern;
    }

    public function methods(array $methods): RouteInterface
    {
        $this->instance->verb = $methods;
        return $this;
    }

    public function getMethods(): array
    {
        return $this->instance->verb;
    }
}
