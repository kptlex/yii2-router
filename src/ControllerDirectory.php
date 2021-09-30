<?php

declare(strict_types=1);

namespace Lex\Yii2\Router;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use yii\base\Application;
use yii\base\Module;

final class ControllerDirectory implements MiddlewareInterface
{
    public string $prefix;

    /** @var Application|Module|null */
    public ?Module $instance;

    public function __construct(string $prefix, ?string $controllerNamespace = null)
    {
        $this->instance = new Module($prefix, null, $controllerNamespace ? ['controllerNamespace' => $controllerNamespace] : []);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $handler->handle($request);
    }
}
