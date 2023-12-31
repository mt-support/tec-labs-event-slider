<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit559ad3dbe282f24b5181e85da6f87c7f
{
    public static $prefixLengthsPsr4 = array (
        'T' => 
        array (
            'TEC\\Events\\' => 11,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'TEC\\Events\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/TEC',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit559ad3dbe282f24b5181e85da6f87c7f::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit559ad3dbe282f24b5181e85da6f87c7f::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit559ad3dbe282f24b5181e85da6f87c7f::$classMap;

        }, null, ClassLoader::class);
    }
}
