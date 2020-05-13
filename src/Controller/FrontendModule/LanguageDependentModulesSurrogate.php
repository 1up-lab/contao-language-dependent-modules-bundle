<?php

declare(strict_types=1);

namespace Oneup\Contao\LanguageDependentModulesBundle\Controller\FrontendModule;

use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\ModuleModel;
use Contao\StringUtil;
use Contao\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LanguageDependentModulesSurrogate extends AbstractFrontendModuleController
{
    protected function getResponse(Template $template, ModuleModel $model, Request $request): ?Response
    {
        $locale = $request->getLocale();
        $modules = StringUtil::deserialize($model->languageDependentModules);

        $template->surrogate = null;

        if (\is_array($modules) && \array_key_exists($locale, $modules)) {
            $template->surrogate = (int) $modules[$locale];
        }

        return $template->getResponse();
    }
}
