<?php

namespace DeLoachTech\PackageInstaller\Process;

use Composer\Package\PackageInterface;

class Alert
{

    public function getAlerts(PackageInterface $package): array
    {
        $extra = $package->getExtra();
        return $extra['alerts']??[];
    }
}