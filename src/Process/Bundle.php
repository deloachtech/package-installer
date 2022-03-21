<?php
// Could not get the bundle logic to 'uninstall', because the plugin gets removed before the bundle package.

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