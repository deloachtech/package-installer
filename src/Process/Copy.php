<?php

namespace DeLoachTech\PackageInstaller\Process;

use Composer\Package\PackageInterface;

class Copy
{

    public function __construct()
    {
    }

    public function copyFiles(PackageInterface $package)
    {
        $extra = $package->getExtra();
        if (!empty($extra['copy'])) {
            foreach ($extra['copy'] as $source => $dest) {
                if (file_exists($source)) {

                    // Files do not exist in vendor.
                    // Can't find a way to copy package files through this plugin.
                    // Even tried plugin events!
                    // Resorted to creating the file with provided content (see Create.php)
                    file_put_contents('copy.txt',"$source : $dest\n", FILE_APPEND);

                    copy($source, $dest);
                }
            }
        }
    }


    public function removeFiles(PackageInterface $package)
    {
        $extra = $package->getExtra();

        if (!empty($extra['copy'])) {
            foreach ($extra['copy'] as $source => $dest) {
                if (file_exists($dest)) {
                    unlink($dest);
                }
            }
        }
    }
}