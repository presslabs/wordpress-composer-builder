#!/usr/bin/env php
<?php
require_once __DIR__ . '/common.php';

define("MIN_WP_TAG", "5.0");
chdir(__DIR__ . "/wordpress");
exec("git tag -l", $tags, $code);
if ($code > 0) {
    exit($code);
}

// map of local tags to upstream tags
$upstream_tags = array();

// a list of existing local tags
$existing_tags = array();

// a list of next available patch version for local tags
$patch_tags = array();

foreach ($tags as $tag) {
    if (preg_match('/upstream-(([0-9]+).([0-9]+)(.[0-9]+)*)/', $tag, $matches)) {
        if (count($matches) == 4) {
            $upstream_tags["$matches[1].0"] = $tag;
        } else {
            $upstream_tags[$matches[1]] = $tag;
        }
    } else {
        $existing_tags[$tag] = true;
    }
}

foreach($existing_tags as $tag => $_) {
    if (preg_match('/([0-9a-z.]+).p([0-9]+)/', $tag, $matches)) {
        $idx = ((int)$matches[2] + 1);
        while ( $idx < 10000 ) {
            $patch = "$matches[1].p$idx";
            if (isset($existing_tags[$patch])) break;
            $idx++;
        }
        $patch_tags[$tag] = "$patch";
    } else {
        $patch_tags[$tag] = "$tag.p0";
    }
}

if (count($argv) == 1) {
    foreach($upstream_tags as $localtag => $tag) {
        if (version_compare($localtag, MIN_WP_TAG, ">=") && !isset($existing_tags[$localtag])) {
            echo "$localtag\n";
        }
    }
} elseif (count($argv) == 2) {
    if (isset($upstream_tags[$argv[1]])) {
        echo $upstream_tags[$argv[1]] . "\n";
        return;
    }
} else {
    error("USAGE: tags.php [local-tag]");
}


/*
foreach($upstream_tags as $localtag => $tag) {
    if (version_compare($localtag, MIN_WP_TAG, ">=") && !isset($existing_tags[$localtag])) {
        exec(__DIR__ . "/sync.php tag $localtag $tag", $out, $code);
        echo join("\n", $out) . "\n";
        if ( $code > 0 ) {
            error("ERROR: error building tag $localtag");
        }

        exec("git tag $localtag", $out, $code);
        echo join("\n", $out) . "\n";
        if ( $code > 0 ) {
            error("ERROR: creating tag");
        }
    }
}*/
