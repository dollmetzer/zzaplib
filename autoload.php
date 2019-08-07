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

        array_shift($tmp);
        array_shift($tmp);

        $filename = realpath(__DIR__.'/src/'.join('/', $tmp).'.php');
        if(!empty($filename)) {
            require_once($filename);
        }

    }

}

// Register Autoloader
spl_autoload_register('autoload_zzaplib');