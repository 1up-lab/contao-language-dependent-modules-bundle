<?php

declare(strict_types=1);

namespace Oneup\Contao\LanguageDependentModulesBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Oneup\Contao\LanguageDependentModulesBundle\OneupContaoLanguageDependentModulesBundle;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(OneupContaoLanguageDependentModulesBundle::class)->setLoadAfter([
                ContaoCoreBundle::class,
                OneupContaoLanguageDependentModulesBundle::class,
            ]),
        ];
    }
}
