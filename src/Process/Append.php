<?php
/**
 * This file is part of the deloachtech/package-installer package.
 *
 * Copyright (c) DeLoach Tech, LLC - All Rights Reserved
 * https://deloachtech.com
 *
 * Unauthorized copying of this file, via any medium is strictly prohibited.
 * Proprietary and confidential.
 */

namespace DeLoachTech\PackageInstaller\Process;

use Composer\Package\PackageInterface;

class Append
{

    public function __construct()
    {
    }

    public function installAppends(PackageInterface $package)
    {
        $extra = $package->getExtra();
        $packageName = $package->getName();

        if (!empty($extra['append'])) {
            foreach ($extra['append'] as $file => $data) {
                $this->createAppend($packageName, $data, $file);
            }
        }
    }

    public function removeAppends(PackageInterface $package)
    {
        $extra = $package->getExtra();
        $packageName = $package->getName();

        if (!empty($extra['append'])) {
            foreach ($extra['append'] as $file => $data) {
                $this->removeAppend($packageName, $file);
            }
        }
    }


    private function createAppend(string $pkgName, string $data, string $file): bool
    {
        if (!file_exists($file)) {
            return false;
        }

        file_put_contents($file, $this->tagData($pkgName, $data), FILE_APPEND);
        return true;
    }


    private function isFileAppended(string $pkgName, string $file): bool
    {
        return is_file($file) && false !== strpos(file_get_contents($file), sprintf('###> %s ###', $pkgName));
    }

    private function updateAppend(string $pkgName, string $data, string $file): bool
    {
        if (!file_exists($file)) {
            return false;
        }

        $data = $this->tagData($pkgName, $data);

        $pieces = explode("\n", trim($data));
        $startMark = trim(reset($pieces));
        $endMark = trim(end($pieces));
        $contents = file_get_contents($file);

        if (false === strpos($contents, $startMark) || false === strpos($contents, $endMark)) {
            return false;
        }

        $pattern = '/' . preg_quote($startMark, '/') . '.*?' . preg_quote($endMark, '/') . '/s';
        $newContents = preg_replace($pattern, trim($data), $contents);
        file_put_contents($file, $newContents);

        return true;
    }

    private function removeAppend(string $pkgName, string $file): bool
    {
        if (!file_exists($file)) {
            return false;
        }

        $contents = preg_replace(sprintf('{%s*###> %s ###.*###< %s ###%s+}s', "\n", $pkgName, $pkgName, "\n"), "\n", file_get_contents($file), -1, $count);
        if ($count) {
            file_put_contents($file, $contents);
        }

        return true;

    }

    private function tagData(string $pkgName, string $data): string
    {
        // /vendor/symfony/flex/src/Configurator/EnvConfigurator.php
        return "\n" . sprintf('###> %s ###%s%s%s###< %s ###%s', $pkgName, "\n", rtrim($data, "\r\n"), "\n", $pkgName, "\n");
    }

}