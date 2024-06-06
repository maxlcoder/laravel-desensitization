<?php

namespace Maxlcoder\LaravelDesensitization;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Maxlcoder\LaravelDesensitization\Skeleton\SkeletonClass
 */
class LaravelDesensitizationFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-desensitization';
    }
}
