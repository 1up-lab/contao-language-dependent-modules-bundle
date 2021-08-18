<?php

declare(strict_types=1);

namespace Oneup\Contao\LanguageDependentModulesBundle\EventListener;

use Contao\DataContainer;
use Contao\Image;
use Contao\StringUtil;
use Contao\System;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class FieldWizardListener
{
    private TranslatorInterface $translator;
    private CsrfTokenManagerInterface $csrfTokenManager;
    private string $csrfTokenName;

    public function __construct(TranslatorInterface $translator, CsrfTokenManagerInterface $csrfTokenManager, string $csrfTokenName)
    {
        $this->translator = $translator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->csrfTokenName = $csrfTokenName;
    }

    public function onEditModule(DataContainer $dc): string
    {
        $wizards = [];
        $values = StringUtil::deserialize($dc->value, true);

        if (empty($values)) {
            return '';
        }

        System::loadLanguageFile('tl_content');

        foreach ($values as $language => $id) {
            if ('' === $id) {
                continue;
            }

            $title = $this->translator->trans('tl_content.editalias', [$id], 'contao_content');

            $wizards[$language] = ' <a href="contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $id . '&amp;popup=1&amp;nb=1&amp;rt=' . $this->csrfTokenManager->getToken($this->csrfTokenName)->getValue() . '"
                    title="' . StringUtil::specialchars($title) . '"
                    onclick="Backend.openModalIframe({\'title\':\'' . StringUtil::specialchars(str_replace("'", "\\'", $title)) . '\',\'url\':this.href});return false">' . Image::getHtml('alias.svg', $title) . '</a>';
        }

        return serialize($wizards);
    }
}
