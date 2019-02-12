<?php
function isGitClean() {
    exec("git status --porcelain", $out, $code);
    if ($code > 0 || count($out) > 0) {
        return false;
    }
    return true;
}

function branchExists($branch) {
    exec("git show-ref --quiet refs/heads/$branch", $_, $code);
    return $code == 0;
}

function applyComposer($composer_version) {
    $php_min_version = "7.1";
    ob_start();
    require __DIR__ . "/composer.json.php";
    $composer = ob_get_clean();
    file_put_contents(__DIR__ . "/wordpress/composer.json", $composer);
}

function error($msg) {
    echo "$msg\n";
    exit(1);
}

function prepareWorkBranch($branch, $gitRef, $wpComposerVersion) {
    if (!branchExists("master")) {
        exec("git checkout -b master origin/master", $out, $code);
        if ($code > 0) {
            error(join("\n",$out));
        }
    }
    if (branchExists($branch)) {
        exec("git checkout master", $out, $code);
        if ($code > 0) {
            error(join("\n",$out));
        }

        exec("git branch -D $branch", $out, $code);
        if ($code > 0) {
            error(join("\n",$out));
        }
    }

    exec("git checkout -b $branch $gitRef", $out, $code);
    if ($code > 0) {
        error(join("\n",$out));
    }

    exec(__DIR__ . "/patch.php", $out, $code);
    echo join("\n", $out) . "\n";
    if ( $code > 0 ) {
        error("ERROR: patch failed");
    }

    if (isGitClean()) {
        // noting to do
        return;
    }

    exec("git add .", $out, $code);
    if ($code > 0) {
        error(join("\n", $out));
    }

    exec("git commit -m 'Apply patches'", $out, $code);
    if ($code > 0) {
        error(join("\n", $out));
    }

    applyComposer($wpComposerVersion);
    exec("git add composer.json", $out, $code);
    if ($code > 0) {
        error(join("\n", $out));
    }

    exec("git commit -m 'Add composer.json'", $out, $code);
    if ($code > 0) {
        error(join("\n", $out));
    }
}
