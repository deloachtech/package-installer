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

namespace DeLoachTech\PackageInstaller\Composer;

use Composer\Installer\LibraryInstaller;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;
use DeLoachTech\PackageInstaller\Process\Bundle;
use DeLoachTech\PackageInstaller\Process\Append;
use DeLoachTech\PackageInstaller\Process\Create;

class Installer extends LibraryInstaller
{
    //$project_path = \realpath($this->composer->getConfig()->get('vendor-dir').'/../').'/';

    private $postInstallInfo;
    private $bundles;
    private $pluginIsRequired;


    public function init()
    {
        $requires = $this->composer->getPackage()->getRequires();
        $this->pluginIsRequired = isset($requires['deloachtech/package-installer']);

        $this->bundles = Bundle::getBundleData();
    }

    public function getBundles()
    {
        return $this->bundles;
    }

    public function getPostInstallInfo(): ?array
    {
        return $this->postInstallInfo;
    }

    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
//        if($package->getType() == 'deloachtech-bundle'){
//
//            $requires = $package->getRequires();
//
//            if(!isset($requires['deloachtech/bundle-installer'])){
////                $this->io->alert($package->getName() . ' cannot be installed without the deloachtech/bundle-installer. (Run composer require deloachtech/bundle-installer first.)');
//                throw new \Exception('Package(s) require deloachtech/bundle-installer. Run composer require deloachtech/bundle-installer first.');
//            }
//        }


        (new Bundle())->installBundles($package, $this->bundles);
        (new Append())->installAppends($package);
        (new Create())->createFiles($package);

        if (!empty($package->getExtra()['post-install-info'])) {
            $this->postInstallInfo[] = [$package->getName() => $package->getExtra()['post-install-info']];
        }

        return parent::install($repo, $package);

    }


    public function uninstall(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        if ($this->pluginIsRequired) {
            (new Bundle())->removeBundles($package, $this->bundles);
            file_put_contents($this->bundles['file'], Bundle::buildContents($this->bundles['array']));
        } else {
            if (!empty($package->getExtra()['bundle'])) {
                $this->io->alert($package->getName() . ' bundle(s) will have to be manually removed from the ' . $this->bundles['file'] . ' file.');
            }
        }

        (new Append())->removeAppends($package);
        (new Create())->removeCreatedFiles($package);

        return parent::uninstall($repo, $package);
    }


    /**
     * @inheritDoc
     */
    public function supports($packageType): bool
    {
        if ($packageType == 'deloachtech-package') {
            return true;
        }
//        if($packageType == 'deloachtech-bundle'){
//            return true;
//        }
        return false;
    }
}