<?php

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;

$relationships = getenv('PLATFORM_RELATIONSHIPS');
if (!$relationships) {
    return;
}

$relationships = json_decode(base64_decode($relationships), true);

foreach ($relationships['database'] as $endpoint) {
    if (empty($endpoint['query']['is_master'])) {
        continue;
    }

    $container->setParameter('database_driver', 'pdo_' . $endpoint['scheme']);
    $container->setParameter('database_host', $endpoint['host']);
    $container->setParameter('database_port', $endpoint['port']);
    $container->setParameter('database_name', $endpoint['path']);
    $container->setParameter('database_user', $endpoint['username']);
    $container->setParameter('database_password', $endpoint['password']);
    $container->setParameter('database_path', '');

    // Cluster DB name is hardcoded. It will have no any effect if cluster is disabled
    $container->setParameter('cluster_database_name', 'cluster');
}

// Set env CUSTOM_CACHE_POOL to for instance singleredis/singlememcached for env/generic.php to set pool
if (isset($relationships['cache'])) {
    foreach ($relationships['cache'] as $endpoint) {
        if ($endpoint['scheme'] !== 'memcached') {
            continue;
        }

        $container->setParameter('cache_host', $endpoint['host']);
        $container->setParameter('cache_memcached_port', $endpoint['port']);
    }
} elseif (isset($relationships['redis'])) {
    foreach ($relationships['redis'] as $endpoint) {
        if ($endpoint['scheme'] !== 'redis') {
            continue;
        }

        $container->setParameter('cache_host', $endpoint['host']);
        $container->setParameter('cache_redis_port', $endpoint['port']);
    }
}

// Disable PHPStormPass
$container->setParameter('ezdesign.phpstorm.enabled', false);

// Load platform.sh specifc settings

$loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__));
$loader->load('platformsh.yml');
