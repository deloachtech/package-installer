<?php

namespace DeLoachTech\PackageInstaller\Composer;

use Composer\Installer\LibraryInstaller;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;
use DeLoachTech\PackageInstaller\Process\Append;
//use DeLoachTech\PackageInstaller\Process\Bundle;
use DeLoachTech\PackageInstaller\Process\Create;

class Installer extends LibraryInstaller
{
    //$project_path = \realpath($this->composer->getConfig()->get('vendor-dir').'/../').'/';

//    private $bundleData;
    private $postInstallInfo;

//    public function setBundleData($bundleData)
//    {
//        $this->bundleData = $bundleData;
//    }
//
//    public function getBundleData()
//    {
//        return $this->bundleData;
//    }

    public function getPostInstallInfo(): array
    {
        return $this->postInstallInfo;
    }

    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {

//        (new Bundle())->installBundles($package, $this->bundleData);
        (new Append())->installAppends($package);
        (new Create())->createFiles($package);

        if (!empty($package->getExtra()['post-install-info'])) {
            $this->postInstallInfo[] = [$package->getName() => $package->getExtra()['post-install-info']];
        }


        return parent::install($repo, $package);
    }


    public function uninstall(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
//        (new Bundle())->removeBundles($package, $this->bundleData);
        (new Append())->removeAppends($package);
        (new Create())->removeCreatedFiles($package);

        return parent::uninstall($repo, $package);
    }


    /**
     * @inheritDoc
     */
    public function supports($packageType): bool
    {
        file_put_contents('info', $this->composer->getPackage()->getName()."\n",FILE_APPEND);
        return 'deloachtech-package' === $packageType;
    }
}