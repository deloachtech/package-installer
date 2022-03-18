<?php

namespace DeLoachTech\PackageInstaller\Process;

use Composer\Package\PackageInterface;

class Bundle
{

    public function installBundles(PackageInterface $package, &$bundleData)
    {
        $extra = $package->getExtra();
        if (!empty($extra['bundle'])) {
            foreach ($extra['bundle'] as $bundle => $str) {
                $keys = explode("|", $str);
                foreach ($keys as $key) {
                    $bundleData['array'][$bundle][$key] = true;
                }
            }
        }
    }


    public function removeBundles(PackageInterface $package, &$bundleData)
    {
        $extra = $package->getExtra();
        if (!empty($extra['bundle'])) {
            foreach ($extra['bundle'] as $bundle => $str) {
                if (isset($bundleData['array'][$bundle])) {
                    unset($bundleData['array'][$bundle]);
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

        $_bundle = null;

        foreach ($file as $k => $v) {
            if (file_exists($v)) {
                $_bundle = $v;
                break;
            }
        }
        return [
            'file' => $_bundle,
            'array' => include($_bundle)
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

}