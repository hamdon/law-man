# law-man monitor system client  执行官监控系统客户端

基于laravel的异常信息收集。

报警系统分为客户端和服务端，服务端可以创建不同的项目，并集成了微信企业号报警功能，客户端为不同的项目的异常收集器

# 1、在config/app.php目录注册我们的服务提供者和门脸类

```
'providers' => [
    Hamdon\LawMan\LawManServiceProvider::class,
]

'aliases' => [
    'lawMan' => Hamdon\LawMan\Facades\LawMan::class,
]
```

# 2、发布服务
php artisan vendor:publish --provider="Hamdon\LawMan\LawManServiceProvider"

# 3、修改config/lawman.php里面的内容

# 4、修改app/Exceptions/handler.php里面的内容，使用report方法或者render增加收集操作

```
\lawMan::setBackEndSubmit()->submitException($exception);
```
