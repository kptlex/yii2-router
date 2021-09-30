<?php

declare(strict_types=1);

namespace Lex\Yii2\Router;

interface GroupInterface extends RouterInterface
{
    public function getPrefix(): string;
}
