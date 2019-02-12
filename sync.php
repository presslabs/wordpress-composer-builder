#!/usr/bin/env php
<?php
require_once __DIR__ . '/common.php';

chdir(__DIR__ . "/wordpress");
exec("git tag -l", $tags, $code);
if ($code > 0) {
    exit($code);
}

if (count($argv) != 4) {
    error("USAGE: sync.php tag/branch git-reference-to-commit upstream-git-reference");
}
$kind = $argv[1];
$ref = $argv[2];
$upstreamRef = $argv[3];

if (!isGitClean()) {
    error("ERROR: Git dir wordpress not clean!");
}

$wpComposerVersion = $ref == "master" ? "dev-master" : $ref;
if ($kind == "branch") {
    $workBranch = "work-$ref";
} elseif ($kind == "tag") {
    $workBranch = "work-tag-$ref";
} else {
    error("ERROR: invalid kind $kind");
}

prepareWorkBranch($workBranch, $upstreamRef, $wpComposerVersion);
