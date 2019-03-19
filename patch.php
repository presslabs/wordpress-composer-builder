#!/usr/bin/env php
<?php
define('PATCHES_DIR', __DIR__ . '/patches' );
require_once __DIR__ . '/wordpress/wp-includes/version.php';
chdir(__DIR__ . "/wordpress");

patch('real_path_5.1.diff', '5.1-rc1');
patch('real_path_5.0.diff', '5.0', '5.1-rc1');
patch('oem_preload_5.1.diff', '5.1-rc1');
patch('oem_preload_5.0.diff', '5.0', '5.1-rc1');
patch('imagick_5.1.diff', '5.1-rc1');
patch('imagick_5.0.diff', '5.0', '5.1-rc1');
patch('stream_wrappers_5.1.diff', '5.1-rc1');
patch('stream_wrappers_5.0.diff', '5.0', '5.1-rc1');

function shouldApply($minVersion = null, $maxVersion = null) {
    global $wp_version;
    if ($minVersion && version_compare($wp_version, $minVersion, '<')) {
        return false;
    }
    if ($maxVersion && version_compare($wp_version, $maxVersion, '>=')) {
        return false;
    }
    return true;
}

function isApplied($file) {
    exec("patch -R -p1 -s -f --dry-run -i$file", $_, $code);
    return ($code == 0);
}

function patch($file, $minVersion = null, $maxVersion = null) {
    $fullpath = PATCHES_DIR . "/$file";
    if (!shouldApply($minVersion, $maxVersion)) {
        return;
    }
    if (isApplied($fullpath)) {
        echo "Skipping applied patch $file\n";
        return;
    }

    echo "Applying patch $file\n";
    exec("patch --no-backup-if-mismatch -p1 -i$fullpath", $out, $code);
    if ($code > 0) {
        echo "ERROR applying patch:\n" . join("\n", $out);
        exit(1);
    }
}
