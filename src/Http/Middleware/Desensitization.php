<?php

namespace Maxlcoder\LaravelDesensitization\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use ReflectionClass;

class Desensitization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $uri = $request->route()->uri;
        $fields = $this->getFields($uri);
        if (!empty($fields)) {
            $result = $response->content();
            $resultArr = json_decode($result, JSON_OBJECT_AS_ARRAY);
            foreach ($fields as $field) {
                // 检查是否能够进行脱敏
                $functions = config('laravel-desensitization.functions');
                $classFunctions = config('laravel-desensitization.class.functions');
                if (empty($functions[$field['type']]) && empty($classFunctions[$field['type']])) {
                    Log::error('laravel-desensitization function and class function not found: ' . $field['type']);
                    return;
                }
                $isFunctionRight = false;
                if (!empty($functions[$field['type']])) {
                    if (!function_exists($functions[$field['type']])) {
                        Log::error('laravel-desensitization function not found: ' . $field['type']);
                    } else {
                        $isFunctionRight = true;
                    }
                }
                if (!$isFunctionRight) {
                    // 是否存在脱敏类
                    $className = config('laravel-desensitization.class.name');
                    if (!class_exists($className)) {
                        Log::error('laravel-desensitization function not found and class ' . $className . ' not found: ' . $field['type']);
                        return;
                    }
                    // 判断类方法是否存在
                    $class = new $className;
                    $exit = method_exists($class, $classFunctions[$field['type']]);
                    unset($class);
                    if (!$exit) {
                        Log::error('laravel-desensitization function not found and class ' . $className . ' function not found: ' . $field['type']);
                        return;
                    }
                }
                $this->traverseArray($resultArr, '', $field, $isFunctionRight);
            }
            $response->setContent(json_encode($resultArr));
        }
        return $response;
    }


    public function getFields($uri)
    {
        $uris = config('laravel-desensitization.uris');
        return $uris[$uri] ?? null;
    }

    public function traverseArray(&$array, $prefix = '', $field, $isFunctionRight)
    {
        foreach ($array as $key => &$value) {
            if (is_int($key)) {
                $key = '*';
            }
            $currentKey = $prefix === '' ? $key : $prefix . '.' . $key;
            if ($currentKey == $field['key'] && !is_array($value) && !empty($value)) {
                $value = $this->doDesensitization($field['type'], $value, $isFunctionRight);
            }
            if (is_array($value)) {
                $this->traverseArray($value, $currentKey, $field, $isFunctionRight);
            }
        }
    }

    public function doDesensitization($type, $value, $isFunctionRight)
    {
        if ($isFunctionRight) {
            $functions = config('laravel-desensitization.functions');
            $value = $functions[$type]($value);
        } else {
            $className = config('laravel-desensitization.class.name');
            $classFunctions = config('laravel-desensitization.class.functions');
            $funcType = new \ReflectionMethod($className, $classFunctions[$type]);
            $class = new $className();
            if ($funcType->isStatic()) {
                $value = $class::{$classFunctions[$type]}($value);
            } else {
                $value = $class->$classFunctions[$type]($value);
            }
            unset($funcType);
            unset($class);
        }
        return $value;
    }
}
