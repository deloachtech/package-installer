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

class Bundle
{

    public function installBundles(PackageInterface $package, &$bundles)
    {
        $extra = $package->getExtra();
        if (!empty($extra['bundle'])) {
            foreach ($extra['bundle'] as $bundle => $str) {
                $keys = explode("|", $str);
                foreach ($keys as $key) {
                    $bundles['array'][$bundle][$key] = true;
                }
            }
        }
    }


    public function removeBundles(PackageInterface $package, &$bundles)
    {
        // The installer can be uninstalled before the packages! (If not 'required' first.)


        $extra = $package->getExtra();
        if (!empty($extra['bundle'])) {
            foreach ($extra['bundle'] as $bundle => $str) {
                if (isset($bundles['array'][$bundle])) {
                    unset($bundles['array'][$bundle]);
                }
            }
        }
    }


    public static function getBundleData(): array
    {
        $file = [
            'config/bundles.php',
            '../config/bundles.php',
            '../../config/bundles.php',
        ];

        $_file = null;

        foreach ($file as $k => $v) {
            if (file_exists($v)) {
                $_file = $v;
                break;
            }
        }
        return [
            'file' => $_file,
            'array' => self::load($_file)
        ];
    }


    public static function buildContents(array $bundles): string
    {
        $contents = "<?php\n\nreturn [\n";
        foreach ($bundles as $class => $envs) {
            $contents .= "    $class::class => [";
            foreach ($envs as $env => $value) {
                $booleanValue = var_export($value, true);
                $contents .= "'$env' => $booleanValue, ";
            }
            $contents = substr($contents, 0, -2) . "],\n";
        }
        $contents .= "];\n";

        return $contents;
    }


    private static function load(string $file): array
    {
        $bundles = file_exists($file) ? (require $file) : [];
        if (!\is_array($bundles)) {
            $bundles = [];
        }

        return $bundles;
    }

}