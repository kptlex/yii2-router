<?php

declare(strict_types=1);

namespace Lex\Yii2\Router;

interface RouterInterface
{
    public function getMiddlewares(): array;

    public function addRoute(RouteInterface $route): self;

    public function addGroup(GroupInterface $group): self;
    public function getGroup(string $prefix): ?GroupInterface;
    /** @return GroupInterface[]|RouteInterface[] */
    public function getItems(): array;
}
