<?php
// Could not get the bundle logic to 'uninstall', because the plugin gets removed before the bundle package.

namespace DeLoachTech\PackageInstaller\Process;

use Composer\Package\PackageInterface;

class Bundle
{

    public function installBundles(PackageInterface $package)
    {
        $extra = $package->getExtra();
        if (!empty($extra['bundle'])) {

            if($bundleData = $this->getBundleData()) {

                foreach ($extra['bundle'] as $bundle => $str) {
                    $keys = explode("|", $str);
                    foreach ($keys as $key) {
                        $bundleData['array'][$bundle][$key] = true;
                    }
                }
                file_put_contents($bundleData['file'], $this->buildContents($bundleData['array']));
            }
        }
    }


    public function removeBundles(PackageInterface $package)
    {
        $extra = $package->getExtra();
        if (!empty($extra['bundle'])) {

            if($bundleData = $this->getBundleData()) {

                foreach ($extra['bundle'] as $bundle => $str) {
                    if (isset($bundleData['array'][$bundle])) {
                        unset($bundleData['array'][$bundle]);
                    }
                }
                file_put_contents($bundleData['file'], $this->buildContents($bundleData['array']));
            }
        }
    }


    private function getBundleData(): array
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
            'array' => $this->load($_file)
        ];
    }



    private function buildContents(array $bundles): string
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


    private function load(string $file): array
    {
        $bundles = file_exists($file) ? (require $file) : [];
        if (!\is_array($bundles)) {
            $bundles = [];
        }

        return $bundles;
    }

}