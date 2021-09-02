<?php

declare(strict_types=1);

namespace Lex\Router;

interface RouteInterface
{
    /**
     * @param string[] $methods
     * @return $this
     */
    public function methods(array $methods): self;

    public function pattern(string $pattern): self;

    public function getPattern(): string;

    /** @return string[] */
    public function getMethods(): array;
}
