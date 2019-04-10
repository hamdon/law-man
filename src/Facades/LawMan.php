<?php
namespace Hamdon\LawMan\Facades;
use Illuminate\Support\Facades\Facade;
class LawMan extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'lawMan';
    }
}