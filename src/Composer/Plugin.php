<?php

namespace DeLoachTech\PackageInstaller\Composer;

use Composer\Composer;
use Composer\DependencyResolver\Operation\InstallOperation;
use Composer\DependencyResolver\Operation\UninstallOperation;
use Composer\DependencyResolver\Operation\UpdateOperation;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Installer\PackageEvent;
use Composer\IO\IOInterface;
use Composer\Package\Package;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use DeLoachTech\PackageInstaller\Process\Bundle;

class Plugin implements PluginInterface, EventSubscriberInterface
{
    private $installer;

    public function activate(Composer $composer, IOInterface $io)
    {
        $installer = new Installer($io, $composer);
        $this->installer = $installer;

        $this->installer->init();

        $composer->getInstallationManager()->addInstaller($installer);
    }

    public function deactivate(Composer $composer, IOInterface $io)
    {
        // TODO: Implement deactivate() method.
    }

    public function uninstall(Composer $composer, IOInterface $io)
    {
        // TODO: Implement uninstall() method.
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'pre-package-install' => 'onPrePackageInstall',
            'pre-package-update' => 'onPrePackageUpdate',
            'pre-package-uninstall' => 'onPrePackageUninstall',
            'pre-autoload-dump' => 'onPreAutoloadDump'
        ];
    }


    /**
     * Packages have been installed/updated and Composer is getting ready to dump the autoload.
     *
     * @param Event $event
     */
    public function onPreAutoloadDump(Event $event)
    {


        if($bundles = $this->installer->getBundles()){
            file_put_contents($bundles['file'], Bundle::buildContents($bundles['array']));
        }

        // Process post-install-info the installer has been assembling.

        $postInstallInfo = $this->installer->getPostInstallInfo();

        if (!empty($postInstallInfo)) {

            $event->getIO()->alert('Post-install information from deloachtech/package-installer:');

            // Composer installs package dependencies first, and we want the info in the opposite order.
            asort($postInstallInfo, SORT_DESC);

            foreach ($postInstallInfo as $k => $v) {
                foreach ($v as $p => $a) {
                    $event->getIO()->write("* " . $p);
                    foreach ($a as $al) {
                        $event->getIO()->write("  - " . $al);
                    }
                }
            }
        }
    }


    /**
     * Gets called by Composer for each package being installed.
     *
     * @param PackageEvent $event
     */
    public function onPrePackageInstall(PackageEvent $event)
    {
    }


    /**
     * Gets called by Composer for each package being uninstalled.
     *
     * @param PackageEvent $event
     */
    public function onPrePackageUninstall(PackageEvent $event)
    {
        //$package = $this->getPackage($event);
    }


    /**
     * Gets called by Composer for each package being updated.
     *
     * @param PackageEvent $event
     */
    public function onPrePackageUpdate(PackageEvent $event)
    {
        //$package = $this->getPackage($event);
    }


    /**
     * Gets a package from an install/update event.
     *
     * @param PackageEvent $event
     * @return Package
     */
    private function getPackage(PackageEvent $event): Package
    {
        /** @var InstallOperation|UpdateOperation|UninstallOperation $operation */
        $operation = $event->getOperation();

        return method_exists($operation, 'getPackage')
            ? $operation->getPackage()
            : $operation->getInitialPackage();
    }


}