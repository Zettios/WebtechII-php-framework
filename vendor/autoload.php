<?php

function webtekAutoload(String $fqcn) {
    // Replace namespace separators with OS directory separators (\ for windows, / for linux/macos)
    $osCorrectPath = str_replace('\\', DIRECTORY_SEPARATOR, $fqcn);

    // Require class
    require_once $osCorrectPath . '.php';
}

spl_autoload_register('webtekautoload');