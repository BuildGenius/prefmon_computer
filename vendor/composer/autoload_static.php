<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit6f50f0a9c9ff66c2ff22cc673c74e28f
{
    public static $prefixLengthsPsr4 = array (
        'W' => 
        array (
            'Webmozart\\Assert\\' => 17,
        ),
        'P' => 
        array (
            'Phoomin\\PerformanceComputer\\' => 28,
        ),
        'G' => 
        array (
            'GO\\' => 3,
        ),
        'C' => 
        array (
            'Cron\\' => 5,
        ),
        'A' => 
        array (
            'App\\Models\\' => 11,
            'App\\Http\\Controller\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Webmozart\\Assert\\' => 
        array (
            0 => __DIR__ . '/..' . '/webmozart/assert/src',
        ),
        'Phoomin\\PerformanceComputer\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        'GO\\' => 
        array (
            0 => __DIR__ . '/..' . '/peppeocchi/php-cron-scheduler/src/GO',
        ),
        'Cron\\' => 
        array (
            0 => __DIR__ . '/..' . '/dragonmantank/cron-expression/src/Cron',
        ),
        'App\\Models\\' => 
        array (
            0 => __DIR__ . '/../..' . '/models',
        ),
        'App\\Http\\Controller\\' => 
        array (
            0 => __DIR__ . '/../..' . '/controller',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit6f50f0a9c9ff66c2ff22cc673c74e28f::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit6f50f0a9c9ff66c2ff22cc673c74e28f::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit6f50f0a9c9ff66c2ff22cc673c74e28f::$classMap;

        }, null, ClassLoader::class);
    }
}
