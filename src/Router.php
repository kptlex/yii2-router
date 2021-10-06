<?php

declare(strict_types=1);

namespace Lex\Yii2\Router;

use Yii;
use Yiisoft\Arrays\ArrayHelper;
use yii\web\UrlRule;

final class Router implements RouterInterface
{
    private Group $group;

    public function __construct()
    {
        $this->group = new Group('', [], []);
        $this->group->instance = Yii::$app;
    }

    public function addRoute(RouteInterface $route): RouterInterface
    {
        $this->group->addRoute($route);
        return $this;
    }

    public function getItems(): array
    {
        $groups = $this->group->getItems();
        $routes = [];
        $urlManager = Yii::$app->urlManager;
        /** @var UrlRule $rule */
        foreach ($urlManager->rules as $rule) {
            $routes[] = new Route($rule);
        }
        return ArrayHelper::merge($routes, $groups);
    }

    public function getMiddlewares(): array
    {
        return $this->group->getMiddlewares();
    }

    public function addGroup(GroupInterface $group): RouterInterface
    {
        $this->group->addGroup($group);
        return $this;
    }

    public function getGroup(string $prefix): ?GroupInterface
    {
        return $this->group->getGroup($prefix);
    }
}
