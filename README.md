# laravel desensitization middleware

[![Latest Version on Packagist](https://img.shields.io/packagist/v/maxlcoder/laravel-desensitization.svg?style=flat-square)](https://packagist.org/packages/maxlcoder/laravel-desensitization)
[![Total Downloads](https://img.shields.io/packagist/dt/maxlcoder/laravel-desensitization.svg?style=flat-square)](https://packagist.org/packages/maxlcoder/laravel-desensitization)
![GitHub Actions](https://github.com/maxlcoder/laravel-desensitization/actions/workflows/main.yml/badge.svg)

针对 API 的敏感数据处理的中间，配置形式，脱敏函数和脱敏方法均自定义

## Installation

You can install the package via composer:

```bash
composer require maxlcoder/laravel-desensitization
```

## Config

```bash
php artisan vendor:publish --provider="Maxlcoder\LaravelDesensitization\LaravelDesensitizationServiceProvider"
```

* functions: 脱敏使用的全局辅助函数，例如 'mobile' => 'desensitiseMobile'，使用全局辅助函数 desensitiseMobile 对 uris 中配置的 type 为 mobile 类型的 key 做执行脱敏 
* class: 全局自定义脱敏类, name 表示类的全路径，这里只能填字符串，functions 表示类中脱敏类型对应的脱敏方法
* functions 和 class 优先使用 functions 全局辅助函数，当全局辅助函数没有指定，才使用 class. 如果二者均没有配置，则不处理脱敏，但是会有 error 日志提示
* uris: 全局需要进行脱敏的接口，以及接口返回中需要脱敏的字段名和脱敏类型，系统会对返回的数据结构做解析，并进行迭代脱敏，其中数组 * 表示返回的数据是数组

```php
[
    'functions' => [
        'mobile' => 'desensitise_mobile',
        'name' => 'desensitise_name'
    ],
    'class' => [
        'name' => 'App\Lib\Desensitization',
        'functions' => [
            'mobile' => 'desensitiseMobile',
        ],
    ],
    'uris' => [
        'admin/admins' => [
            ['key' => 'data.data.*.mobile', 'type' => 'mobile'],
            ['key' => 'data.data.*.name', 'type' => 'name'],
        ],
    ],
];
```

## Usage

在 `Kerner.php` 中引入中间件

```php
protected $routeMiddleware = [
        ...
        
        'desensitization' => \Maxlcoder\LaravelDesensitization\Http\Middleware\Desensitization::class,
    ];
```

### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email liurenlin77@gmail.com instead of using the issue tracker.

## Credits

-   [Woody](https://github.com/maxlcoder)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
