<?php

namespace DeLoachTech\PackageInstaller\Composer;

use Composer\Installer\LibraryInstaller;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;
use DeLoachTech\PackageInstaller\Process\Append;
use DeLoachTech\PackageInstaller\Process\Create;

class Installer extends LibraryInstaller
{
    //$project_path = \realpath($this->composer->getConfig()->get('vendor-dir').'/../').'/';

    private $postInstallInfo;

    public function getPostInstallInfo(): ?array
    {
        return $this->postInstallInfo;
    }

    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        if($package->getType() == 'deloachtech-bundle'){

            $requires = $package->getRequires();

            if(!isset($requires['deloachtech/bundle-installer'])){
                $this->io->alert($package->getName() . ' cannot be installed without the deloachtech/bundle-installer. (Run composer require deloachtech/bundle-installer first.)');
                throw new \Exception('Installationn failed');
            }
        }


        (new Append())->installAppends($package);
        (new Create())->createFiles($package);

        if (!empty($package->getExtra()['post-install-info'])) {
            $this->postInstallInfo[] = [$package->getName() => $package->getExtra()['post-install-info']];
        }

        return parent::install($repo, $package);
    }


    public function uninstall(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        (new Append())->removeAppends($package);
        (new Create())->removeCreatedFiles($package);

        return parent::uninstall($repo, $package);
    }


    /**
     * @inheritDoc
     */
    public function supports($packageType): bool
    {
        if($packageType == 'deloachtech-package'){
            return true;
        }
        if($packageType == 'deloachtech-bundle'){
            return true;
        }
        return false;
    }
}