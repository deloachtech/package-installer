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
        $this->installer->setBundleData(Bundle::getBundleData());

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
            'pre-autoload-dump' => 'onPreAutoloadDump',
        ];
    }

    /**
     * Packages have been installed/updated and Composer is getting ready to dump the autoload.
     *
     * @param Event $event
     */
    public function onPreAutoloadDump(Event $event)
    {
//        $event->getIO()->info('This is an info message');
//        $event->getIO()->alert('This is an alert message');
//        $event->getIO()->notice('This is an notice message');

        $event->getIO()->ask('This is a question?');


        $data = $this->installer->getBundleData();
        file_put_contents($data['file'], Bundle::buildContents($data['array']));
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
        $data = $this->installer->getBundleData();
        file_put_contents($data['file'], Bundle::buildContents($data['array']));
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