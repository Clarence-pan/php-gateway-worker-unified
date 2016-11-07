<?php


call_user_func(function(){
    $possibleVendorDirs = [
        __DIR__ .'/../../../vendor', // If required by other app
        __DIR__ .'/../vendor',       // If installed by self
    ];


    foreach ($possibleVendorDirs as $possibleVendorDir) {
        if (is_dir($possibleVendorDir)){
            $vendorDir = $possibleVendorDir;
            break;
        }
    }

    if (!isset($vendorDir)){
        throw new \RuntimeException("Cannot find the vendor directory! Did you run 'composer install'?");
    }

    if (PATH_SEPARATOR === '/'){
        // linux:
        $workerSrcDir = $vendorDir .'/workerman/gateway-worker/src';
    } else {
        // windows:
        $workerSrcDir = $vendorDir .'/workerman/gateway-worker-for-win/src';
    }

    if (!is_dir($workerSrcDir)){
        throw new \RuntimeException("Cannot find the workerman directory! Did you require workerman?");
    }

    $workerNamespace = 'GatewayWorker\\';
    $workerNamespaceLen = strlen($workerNamespace);

    // a simple psr-4 autoloader
    spl_autoload_register(function($class) use ($workerSrcDir, $workerNamespace, $workerNamespaceLen){
        if (substr_compare($class, $workerNamespace, $workerNamespaceLen) === 0){
            $file = $workerSrcDir . PATH_SEPARATOR . str_replace('\\', PATH_SEPARATOR, substr($class, $workerNamespaceLen)) . '.php';
            if (is_file($file)){
                require_once($file);
                return true;
            }
        }

        return false;
    });
});
