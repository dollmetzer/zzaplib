<?php


/**
 * Autoloader for zzaplib classes
 *
 * Usage for unit tests or for direct invocation of this lib in the
 * application without composer
 *
 * @param string $classname
 */
function autoload_zzaplib($classname) {

    $tmp = explode("\\", $classname);
    if( ($tmp[0] == 'dollmetzer') && ($tmp[1] == 'zzaplib')) {

        $filename = realpath(__DIR__.'/src/'.$tmp[2].'.php');
        if(!empty($filename)) {
            require_once($filename);
        }

    }

}

// Register Autoloader
spl_autoload_register('autoload_zzaplib');