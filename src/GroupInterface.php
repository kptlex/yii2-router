<?php

declare(strict_types=1);

namespace Lex\Router;


interface GroupInterface extends RouterInterface
{
    public function getPrefix(): string;
}
