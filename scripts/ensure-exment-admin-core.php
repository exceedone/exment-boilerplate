<?php

/**
 * Ensures the local path repositories required by composer.dev.json exist.
 * Runs automatically via composer `pre-install-cmd` / `pre-update-cmd`.
 *
 * - ./exment must be provided by the caller: CI (the exment repository
 *   workflows) checks out the revision under test there, so its absence is
 *   reported as an error instead of being cloned blindly.
 * - ./exment-admin-core is cloned from exceedone/exment-admin-core (master)
 *   when missing. To test a specific admin-core revision, place it in
 *   ./exment-admin-core before running composer install / update.
 */

$root = dirname(__DIR__);

$exment = $root . DIRECTORY_SEPARATOR . 'exment';
if (!is_dir($exment)) {
    fwrite(STDERR, "[ensure-exment-admin-core] ./exment not found.\n");
    fwrite(STDERR, "Check out the exment revision under test into ./exment first, e.g.:\n");
    fwrite(STDERR, "  git clone --branch <branch> https://github.com/exceedone/exment.git exment\n");
    exit(1);
}

$adminCore = $root . DIRECTORY_SEPARATOR . 'exment-admin-core';
if (is_dir($adminCore)) {
    echo "[ensure-exment-admin-core] ./exment-admin-core already exists, skipping clone.\n";
    exit(0);
}

echo "[ensure-exment-admin-core] Cloning exceedone/exment-admin-core (master) into ./exment-admin-core ...\n";
passthru(
    'git clone --depth 1 https://github.com/exceedone/exment-admin-core.git ' . escapeshellarg($adminCore),
    $exitCode
);
if ($exitCode !== 0) {
    fwrite(STDERR, "[ensure-exment-admin-core] git clone failed with exit code {$exitCode}.\n");
    exit(1);
}
