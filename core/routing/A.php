<?php

namespace Webtek\Core\Routing;

class A
{
    public function __construct(B $b)
    {
    }

    public function wow()
    {
        echo 'Wow';
    }

    static function run()
    {
        echo 'Pog';
    }
}