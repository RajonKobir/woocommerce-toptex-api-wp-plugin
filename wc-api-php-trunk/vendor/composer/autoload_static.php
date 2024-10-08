<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitd975b581428bd350a1bedbe7a978823c
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'Automattic\\WooCommerce\\' => 23,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Automattic\\WooCommerce\\' => 
        array (
            0 => __DIR__ . '/..' . '/automattic/woocommerce/src/WooCommerce',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitd975b581428bd350a1bedbe7a978823c::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitd975b581428bd350a1bedbe7a978823c::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitd975b581428bd350a1bedbe7a978823c::$classMap;

        }, null, ClassLoader::class);
    }
}
