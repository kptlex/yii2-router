<?php

declare(strict_types=1);

namespace Lex\Yii2\Router;

use Yii;
use yii\base\InvalidArgumentException;
use yii\base\Module;
use yii\web\Application;
use yii\web\Controller;

final class Group implements GroupInterface
{
    /** @var Application|Module|null */
    public ?Module $instance;

    public ?string $controller;

    public array $config;

    public array $routes;

    private string $prefix;

    private array $middlewares;

    /**
     * @param string $prefix
     * @param array $middlewares
     * @param array $routes
     */
    public function __construct(string $prefix, array $middlewares, array $routes = [])
    {
        $this->prefix = $prefix;
        $this->middlewares = $middlewares;
        $this->routes = $routes;
        foreach ($middlewares as $middleware) {
            if ($middleware instanceof ControllerDirectory) {
                $this->instance = $middleware->instance;
            }
        }
    }

    /**
     * @param string $prefix
     * @param Route[] $routes
     * @param array $middlewares
     * @return self
     */
    public static function create(string $prefix, array $routes = [], array $middlewares = []): GroupInterface
    {
        $status = false;
        foreach ($middlewares as $middleware) {
            if (is_string($middleware) && class_exists($middleware) && $middleware instanceof Controller) {
                $status = true;
            } elseif ($middleware instanceof ControllerDirectory) {
                $status = true;
            }
        }
        if (!$status) {
            $message = 'Third params with a controller class or {class} is required.';
            throw new InvalidArgumentException(Yii::t('router', $message, ['class' => ControllerDirectory::class]));
        }
        $rules = [];
        foreach ($routes as $route) {
            $rules[] = $route->instance;
        }
        Yii::$app->urlManager->addRules($rules);
        return new self($prefix, $middlewares, $routes);
    }

    public function addGroup(GroupInterface $group): GroupInterface
    {
        foreach ($group->getMiddlewares() as $key => $middleware) {
            if (is_string($middleware) && class_exists($middleware) && $middleware instanceof Controller) {
                if (!$this->instance) {
                    throw new InvalidArgumentException(Yii::t('router', 'Current group is end point.'));
                }
                $this->instance->controllerMap[$group->getPrefix()] = $group->controller;
                unset($this->middlewares[$key]);
            } elseif ($middleware instanceof ControllerDirectory) {
                if ($middleware->instance instanceof Application || $middleware->instance->module) {
                    continue;
                }
                $middleware->instance->module = $this->instance;
                $this->instance->setModule($group->getPrefix(), $middleware->instance);
                unset($this->middlewares[$key]);
            }
        }
        return $this;
    }

    public function getItems(): array
    {
        //Routes is always empty for groups. All routes to Router.
        $groups = [];
        if ($this->instance) {
            /** @var Module $module */
            foreach ($this->instance->getModules(true) as $module) {
                $groups[] = self::createFromModule($module);
            }
            foreach ($this->instance->controllerMap as $prefix => $controller) {
                $groups[] = new self($prefix, [$controller], []);
            }
            return $groups;
        }
        return [];
    }

    /**
     * @param Route $route
     * @return GroupInterface
     */
    public function addRoute(RouteInterface $route): GroupInterface
    {
        $urlManager = Yii::$app->urlManager;
        $urlManager->addRules([$route->instance]);
        $this->routes[] = $route;
        return $this;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    private static function createFromModule(Module $module): Group {
        $controllerGroup = new ControllerDirectory($module->id, $module->controllerNamespace);
        $controllerGroup->instance = $module;

        $group = new self($module->id, [$controllerGroup], $module->controllerMap);
        $group->instance = $module;

        return $group;
    }

    public function getGroup(string $prefix): ?GroupInterface
    {
        if ($this->instance) {
            /** @var Module $module */
            foreach ($this->instance->getModules(true) as $module) {
                if ($module->id === $prefix) {
                    return self::createFromModule($module);
                }
            }
            foreach ($this->instance->controllerMap as $id => $controller) {
                if ($id === $prefix) {
                    return new self($id, [$controller], []);
                }
            }
        }
        return null;
    }
}
