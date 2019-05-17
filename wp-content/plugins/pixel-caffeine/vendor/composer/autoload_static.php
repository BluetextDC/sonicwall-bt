<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInite45a55d5ccebb669a78fc752dbe93ab1
{
    public static $files = array (
        '320cde22f66dd4f5d3fd621d3e88b98f' => __DIR__ . '/..' . '/symfony/polyfill-ctype/bootstrap.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Symfony\\Polyfill\\Ctype\\' => 23,
            'Symfony\\Component\\Filesystem\\' => 29,
        ),
        'P' => 
        array (
            'Psr\\Log\\' => 8,
        ),
        'M' => 
        array (
            'Monolog\\' => 8,
        ),
        'F' => 
        array (
            'Firebase\\JWT\\' => 13,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Symfony\\Polyfill\\Ctype\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-ctype',
        ),
        'Symfony\\Component\\Filesystem\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/filesystem',
        ),
        'Psr\\Log\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/log/Psr/Log',
        ),
        'Monolog\\' => 
        array (
            0 => __DIR__ . '/..' . '/monolog/monolog/src/Monolog',
        ),
        'Firebase\\JWT\\' => 
        array (
            0 => __DIR__ . '/..' . '/firebase/php-jwt/src',
        ),
    );

    public static $classMap = array (
        'AEPC_Addon_Factory' => __DIR__ . '/../..' . '/includes/class-aepc-addon-factory.php',
        'AEPC_Addon_Product_Item' => __DIR__ . '/../..' . '/includes/class-aepc-addon-product-item.php',
        'AEPC_Addons_Support' => __DIR__ . '/../..' . '/includes/class-aepc-addons-support.php',
        'AEPC_Admin' => __DIR__ . '/../..' . '/includes/admin/class-aepc-admin.php',
        'AEPC_Admin_Ajax' => __DIR__ . '/../..' . '/includes/admin/class-aepc-admin-ajax.php',
        'AEPC_Admin_CA' => __DIR__ . '/../..' . '/includes/admin/class-aepc-admin-ca.php',
        'AEPC_Admin_CA_Manager' => __DIR__ . '/../..' . '/includes/admin/class-aepc-admin-ca-manager.php',
        'AEPC_Admin_Handlers' => __DIR__ . '/../..' . '/includes/admin/class-aepc-admin-handlers.php',
        'AEPC_Admin_Install' => __DIR__ . '/../..' . '/includes/admin/class-aepc-admin-install.php',
        'AEPC_Admin_Logger' => __DIR__ . '/../..' . '/includes/admin/class-aepc-admin-logger.php',
        'AEPC_Admin_Menu' => __DIR__ . '/../..' . '/includes/admin/class-aepc-admin-menu.php',
        'AEPC_Admin_Notices' => __DIR__ . '/../..' . '/includes/admin/class-aepc-admin-notices.php',
        'AEPC_Admin_View' => __DIR__ . '/../..' . '/includes/admin/class-aepc-admin-view.php',
        'AEPC_Cron' => __DIR__ . '/../..' . '/includes/class-aepc-cron.php',
        'AEPC_Currency' => __DIR__ . '/../..' . '/includes/class-aepc-currency.php',
        'AEPC_Edd_Addon_Support' => __DIR__ . '/../..' . '/includes/supports/class-aepc-edd-addon-support.php',
        'AEPC_Facebook_Adapter' => __DIR__ . '/../..' . '/includes/admin/class-aepc-facebook-adapter.php',
        'AEPC_Pixel_Scripts' => __DIR__ . '/../..' . '/includes/class-aepc-pixel-scripts.php',
        'AEPC_Track' => __DIR__ . '/../..' . '/includes/class-aepc-track.php',
        'AEPC_Woocommerce_Addon_Support' => __DIR__ . '/../..' . '/includes/supports/class-aepc-woocommerce-addon-support.php',
        'PixelCaffeine\\Admin\\Exception\\AEPCException' => __DIR__ . '/../..' . '/includes/admin/exceptions/exception-aepc-exception.php',
        'PixelCaffeine\\Admin\\Exception\\FBAPIException' => __DIR__ . '/../..' . '/includes/admin/exceptions/exception-fbapi.php',
        'PixelCaffeine\\Admin\\Exception\\FBAPILoginException' => __DIR__ . '/../..' . '/includes/admin/exceptions/exception-fbapi-login.php',
        'PixelCaffeine\\Admin\\Response' => __DIR__ . '/../..' . '/includes/admin/class-aepc-admin-response.php',
        'PixelCaffeine\\Interfaces\\ECommerceAddOnInterface' => __DIR__ . '/../..' . '/includes/interfaces/interface-ecommerce-addon.php',
        'PixelCaffeine\\Job\\RefreshAudiencesSize' => __DIR__ . '/../..' . '/includes/jobs/class-refresh-audiences-size.php',
        'PixelCaffeine\\Logs\\Entity\\Log' => __DIR__ . '/../..' . '/includes/admin/logs/entity/class-log.php',
        'PixelCaffeine\\Logs\\Exception\\LogNotExistingException' => __DIR__ . '/../..' . '/includes/admin/logs/exception/exception-log-not-existing.php',
        'PixelCaffeine\\Logs\\LogDBHandler' => __DIR__ . '/../..' . '/includes/admin/logs/class-log-db-handler.php',
        'PixelCaffeine\\Logs\\LogRepository' => __DIR__ . '/../..' . '/includes/admin/logs/class-log-repository.php',
        'PixelCaffeine\\Logs\\LogRepositoryInterface' => __DIR__ . '/../..' . '/includes/admin/logs/interface-log-repository.php',
        'PixelCaffeine\\Model\\Job' => __DIR__ . '/../..' . '/includes/models/class-job.php',
        'PixelCaffeine\\ProductCatalog\\Admin\\Metaboxes' => __DIR__ . '/../..' . '/includes/product-catalogs/admin/class-metaboxes.php',
        'PixelCaffeine\\ProductCatalog\\BackgroundFeedSaver' => __DIR__ . '/../..' . '/includes/product-catalogs/class-background-feed-saver.php',
        'PixelCaffeine\\ProductCatalog\\BackgroundFeedSaverProcess' => __DIR__ . '/../..' . '/includes/product-catalogs/class-background-feed-saver-process.php',
        'PixelCaffeine\\ProductCatalog\\Configuration' => __DIR__ . '/../..' . '/includes/product-catalogs/class-configuration.php',
        'PixelCaffeine\\ProductCatalog\\ConfigurationDefaults' => __DIR__ . '/../..' . '/includes/product-catalogs/class-configuration-defaults.php',
        'PixelCaffeine\\ProductCatalog\\Cron\\RefreshFeed' => __DIR__ . '/../..' . '/includes/product-catalogs/cron/class-refresh-feed.php',
        'PixelCaffeine\\ProductCatalog\\DbProvider' => __DIR__ . '/../..' . '/includes/product-catalogs/class-db-provider.php',
        'PixelCaffeine\\ProductCatalog\\Dictionary\\FeedSaver' => __DIR__ . '/../..' . '/includes/product-catalogs/dictionary/class-feed-saver.php',
        'PixelCaffeine\\ProductCatalog\\Entity\\ProductCatalog' => __DIR__ . '/../..' . '/includes/product-catalogs/entity/class-product-catalog.php',
        'PixelCaffeine\\ProductCatalog\\Exception\\EntityException' => __DIR__ . '/../..' . '/includes/product-catalogs/exception/exception-entity.php',
        'PixelCaffeine\\ProductCatalog\\Exception\\FeedException' => __DIR__ . '/../..' . '/includes/product-catalogs/exception/exception-feed.php',
        'PixelCaffeine\\ProductCatalog\\Exception\\GoogleTaxonomyException' => __DIR__ . '/../..' . '/includes/product-catalogs/exception/exception-google-taxonomy.php',
        'PixelCaffeine\\ProductCatalog\\FeedMapper' => __DIR__ . '/../..' . '/includes/product-catalogs/class-feed-mapper.php',
        'PixelCaffeine\\ProductCatalog\\FeedSaverInterface' => __DIR__ . '/../..' . '/includes/product-catalogs/interface-feed-saver.php',
        'PixelCaffeine\\ProductCatalog\\Feed\\WriterInterface' => __DIR__ . '/../..' . '/includes/product-catalogs/feed/interface-writer.php',
        'PixelCaffeine\\ProductCatalog\\Feed\\XMLWriter' => __DIR__ . '/../..' . '/includes/product-catalogs/feed/class-xml-writer.php',
        'PixelCaffeine\\ProductCatalog\\ForegroundFeedSaver' => __DIR__ . '/../..' . '/includes/product-catalogs/class-foreground-feed-saver.php',
        'PixelCaffeine\\ProductCatalog\\Helper\\FeedDirectoryHelper' => __DIR__ . '/../..' . '/includes/product-catalogs/helper/class-feed-directory-helper.php',
        'PixelCaffeine\\ProductCatalog\\ProductCatalogManager' => __DIR__ . '/../..' . '/includes/product-catalogs/class-product-catalog-manager.php',
        'PixelCaffeine\\ProductCatalog\\ProductCatalogs' => __DIR__ . '/../..' . '/includes/product-catalogs/class-product-catalogs.php',
        'WP_Async_Request' => __DIR__ . '/..' . '/a5hleyrich/wp-background-processing/classes/wp-async-request.php',
        'WP_Background_Process' => __DIR__ . '/..' . '/a5hleyrich/wp-background-processing/classes/wp-background-process.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInite45a55d5ccebb669a78fc752dbe93ab1::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInite45a55d5ccebb669a78fc752dbe93ab1::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInite45a55d5ccebb669a78fc752dbe93ab1::$classMap;

        }, null, ClassLoader::class);
    }
}
