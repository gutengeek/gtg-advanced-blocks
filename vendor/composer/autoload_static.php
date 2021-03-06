<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit5d979185acfea85dacd47ca20f71af1d
{
    public static $files = array (
        '25037a01d36a2d70ba3dce50eeffa7bf' => __DIR__ . '/..' . '/gutengeek/components/src/init.php',
    );

    public static $prefixLengthsPsr4 = array (
        'G' => 
        array (
            'Gtg_Advanced_Blocks\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Gtg_Advanced_Blocks\\' => 
        array (
            0 => __DIR__ . '/../..' . '/inc',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit5d979185acfea85dacd47ca20f71af1d::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit5d979185acfea85dacd47ca20f71af1d::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
