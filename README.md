# Laravel Desensitization Middleware

[![Latest Version on Packagist](https://img.shields.io/packagist/v/maxlcoder/laravel-desensitization.svg?style=flat-square)](https://packagist.org/packages/maxlcoder/laravel-desensitization)
[![Total Downloads](https://img.shields.io/packagist/dt/maxlcoder/laravel-desensitization.svg?style=flat-square)](https://packagist.org/packages/maxlcoder/laravel-desensitization)
![GitHub Actions](https://github.com/maxlcoder/laravel-desensitization/actions/workflows/main.yml/badge.svg)

[English](#english) | [简体中文](#简体中文)

---

## English

A middleware for desensitizing API response data. It is configuration-driven and supports highly customizable processing functions (methods).

### Installation

```bash
composer require maxlcoder/laravel-desensitization
```

### Configuration

Publish configuration:

```bash
php artisan vendor:publish --provider="Maxlcoder\LaravelDesensitization\LaravelDesensitizationServiceProvider"
```

#### Config Fields

- `functions`: Global helper function mapping. Example: `'mobile' => 'desensitiseMobile'` means fields with `type=mobile` in `uris` will use this helper.
- `class`: Global custom handler class config. `name` is the full class path (string), and `functions` maps field type to class method.
- Priority: `functions` has higher priority. If not found, it falls back to `class`. If both are missing, no processing is applied and an error log is recorded.
- `uris`: Defines APIs and field paths to process with corresponding types. The middleware parses response structure and iterates fields. Use `*` for array items.

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

### Usage

Register middleware in `Kernel.php`:

```php
protected $routeMiddleware = [
    // ...
    'desensitization' => \Maxlcoder\LaravelDesensitization\Http\Middleware\Desensitization::class,
];
```

### Example

#### 1) Original Response

```json
{
    "code": 200,
    "msg": "success",
    "data": {
        "mobile": "18900000001",
        "contacts": [
            {
                "name": "王组闲",
                "mobile": "18900000002"
            }
        ]
    }
}
```

#### 2) Config

Edit `config/laravel-desensitization.php`:

```php
<?php

/*
 * You can place your custom package configuration in here.
 *
 */
return [
    'functions' => [

    ],
    'class' => [
        'name' => 'App\Lib\Desensitization',
        'functions' => [
            'name' => 'desensitiseRealName',
            'mobile' => 'desensitiseMobile'
        ],
    ],
    'uris' => [
        'admin/admins' => [
            ['key' => 'data.mobile', 'type' => 'mobile'],
            ['key' => 'data.contacts.*.name', 'type' => 'name'],
            ['key' => 'data.contacts.*.mobile', 'type' => 'mobile'],
        ],
    ],
];
```

Custom handler class `Desensitization.php` example:

```php
<?php

namespace App\Lib;

use Illuminate\Support\Str;

class Desensitization
{
    public static function desensitiseMobile($mobile)
    {
        return mb_substr($mobile, 0, 3) . '****' . mb_substr($mobile, 7, 4);
    }

    public static function desensitiseName($name)
    {
        return Str::mask($name, '*', 0, 1);
    }
}
```

#### 3) Processed Response

```json
{
    "code": 200,
    "msg": "success",
    "data": {
        "mobile": "189****0001",
        "contacts": [
            {
                "name": "*组闲",
                "mobile": "189****0002"
            }
        ]
    }
}
```

### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

### Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security-related issues, please email liurenlin77@gmail.com instead of using the issue tracker.

### Credits

- [Woody](https://github.com/maxlcoder)
- [All Contributors](../../contributors)

### License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

### Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).

---

## 简体中文

一个用于 API 返回数据脱敏的中间件，采用配置驱动方式，支持高度自定义处理函数（方法）。

### 安装

```bash
composer require maxlcoder/laravel-desensitization
```

### 配置

发布配置文件：

```bash
php artisan vendor:publish --provider="Maxlcoder\LaravelDesensitization\LaravelDesensitizationServiceProvider"
```

#### 配置字段说明

- `functions`：全局辅助函数映射，例如 `'mobile' => 'desensitiseMobile'`，表示对 `uris` 中 `type=mobile` 的字段调用该函数。
- `class`：全局自定义处理类配置。`name` 为类全路径字符串，`functions` 为类型与类方法的映射。
- 优先级：优先使用 `functions`；若不存在再使用 `class`；若都未配置则不处理并记录 error 日志。
- `uris`：指定需要处理的接口及字段路径和处理类型。系统会解析返回结构并迭代处理，数组使用 `*` 表示。

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

### 使用方法

在 `Kernel.php` 中注册中间件：

```php
protected $routeMiddleware = [
    // ...
    'desensitization' => \Maxlcoder\LaravelDesensitization\Http\Middleware\Desensitization::class,
];
```

### 示例

#### 1) 处理前返回

```json
{
    "code": 200,
    "msg": "success",
    "data": {
        "mobile": "18900000001",
        "contacts": [
            {
                "name": "王组闲",
                "mobile": "18900000002"
            }
        ]
    }
}
```

#### 2) 配置

修改 `config/laravel-desensitization.php`：

```php
<?php

/*
 * You can place your custom package configuration in here.
 *
 */
return [
    'functions' => [

    ],
    'class' => [
        'name' => 'App\Lib\Desensitization',
        'functions' => [
            'name' => 'desensitiseRealName',
            'mobile' => 'desensitiseMobile'
        ],
    ],
    'uris' => [
        'admin/admins' => [
            ['key' => 'data.mobile', 'type' => 'mobile'],
            ['key' => 'data.contacts.*.name', 'type' => 'name'],
            ['key' => 'data.contacts.*.mobile', 'type' => 'mobile'],
        ],
    ],
];
```

自定义处理类 `Desensitization.php` 示例：

```php
<?php

namespace App\Lib;

use Illuminate\Support\Str;

class Desensitization
{
    public static function desensitiseMobile($mobile)
    {
        return mb_substr($mobile, 0, 3) . '****' . mb_substr($mobile, 7, 4);
    }

    public static function desensitiseName($name)
    {
        return Str::mask($name, '*', 0, 1);
    }
}
```

#### 3) 处理后返回

```json
{
    "code": 200,
    "msg": "success",
    "data": {
        "mobile": "189****0001",
        "contacts": [
            {
                "name": "*组闲",
                "mobile": "189****0002"
            }
        ]
    }
}
```

### 测试

```bash
composer test
```

### 更新日志

更多更新内容请查看 [CHANGELOG](CHANGELOG.md)。

### 参与贡献

贡献细节请查看 [CONTRIBUTING](CONTRIBUTING.md)。

### 安全

如发现安全问题，请发送邮件至 liurenlin77@gmail.com，而非直接提交 issue。

### 致谢

- [Woody](https://github.com/maxlcoder)
- [All Contributors](../../contributors)

### 许可证

本项目使用 MIT 许可证，详情请见 [License File](LICENSE.md)。

### Laravel Package Boilerplate

本包基于 [Laravel Package Boilerplate](https://laravelpackageboilerplate.com) 生成。
