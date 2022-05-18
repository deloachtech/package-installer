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

class Create
{


    public function createFiles(PackageInterface $package){
        $extra = $package->getExtra();

        if (!empty($extra['create'])) {
            foreach ($extra['create'] as $file => $content) {

                $dir = pathinfo($file, PATHINFO_DIRNAME);
                if(!is_dir($dir)){
                    mkdir($dir,0755,true);
                }
                file_put_contents($file,$content);
            }
        }
    }
    public function removeCreatedFiles(PackageInterface $package){
        $extra = $package->getExtra();

        if (!empty($extra['create'])) {
            foreach ($extra['create'] as $file => $content) {
                if(file_exists($file)){
                    unlink($file);
                }
            }
        }
    }

}