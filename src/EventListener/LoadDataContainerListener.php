<?php

declare(strict_types=1);

namespace Oneup\Contao\LanguageDependentModulesBundle\EventListener;

use Contao\CoreBundle\Routing\ScopeMatcher;
use Oneup\Contao\LanguageDependentModulesBundle\Provider\AvailableLanguageProvider;
use Oneup\Contao\LanguageDependentModulesBundle\Provider\ModuleProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

class LoadDataContainerListener
{
    private AvailableLanguageProvider $languageProvider;
    private ModuleProvider $moduleProvider;
    private TranslatorInterface $translator;
    private ScopeMatcher $scopeMatcher;
    private RequestStack $requestStack;

    public function __construct(AvailableLanguageProvider $languageProvider, ModuleProvider $moduleProvider, TranslatorInterface $translator, ScopeMatcher $scopeMatcher, RequestStack $requestStack)
    {
        $this->languageProvider = $languageProvider;
        $this->moduleProvider = $moduleProvider;
        $this->translator = $translator;
        $this->scopeMatcher = $scopeMatcher;
        $this->requestStack = $requestStack;
    }

    public function onLoadDataContainer(string $table): void
    {
        /** @var Request $request */
        $request = $this->requestStack->getCurrentRequest();

        if (!$request instanceof Request || !$this->scopeMatcher->isBackendRequest($request)) {
            return;
        }

        $fields = $GLOBALS['TL_DCA'][$table]['fields'];

        if (!\is_array($fields) ||
            !\in_array('languageDependentModulesSurrogate', array_column($fields, 'inputType'), true)
        ) {
            return;
        }

        foreach ($fields as $key => $field) {
            if (!\array_key_exists('inputType', $field)) {
                unset($fields[$key]);
            }
        }

        $affectedFields = array_keys(
            array_column($fields, 'inputType'),
            'languageDependentModulesSurrogate',
            true
        );

        foreach ($affectedFields as $affectedField) {
            $field = \array_slice($fields, $affectedField, 1);
            $keys = array_keys($field);

            if (!\count($keys)) {
                continue;
            }

            $key = $keys[0];
            $fieldConfig = $GLOBALS['TL_DCA'][$table]['fields'][$key];

            if (!\array_key_exists('eval', $fieldConfig)) {
                $fieldConfig['eval'] = [];
            }

            if (!\array_key_exists('save_callback', $fieldConfig)) {
                $fieldConfig['save_callback'] = [];
            }

            if (!\array_key_exists('options', $fieldConfig) &&
                !\array_key_exists('options_callback', $fieldConfig)) {
                $types = [];

                if (\array_key_exists('modules', $fieldConfig['eval'])) {
                    $types = $fieldConfig['eval']['modules'];
                }

                $fieldConfig['options'] = $this->moduleProvider->getModules($types);
            }

            if (!\array_key_exists('languages', $fieldConfig['eval'])) {
                $fieldConfig['eval']['languages'] = $this->languageProvider->getLanguages();
            }

            if (!\array_key_exists('blankOptionLabel', $fieldConfig['eval'])) {
                $fieldConfig['eval']['blankOptionLabel'] = $this->translator->trans(
                    sprintf('tl_module.%sBlankOptionLabel', $key),
                    [],
                    'contao_module'
                );
            }

            $fieldConfig['eval']['includeBlankOption'] = true;
            $fieldConfig['save_callback'][] = [
                'oneup.contao.language_dependent_modules_bundle.listener.language_dependent_modules_surrogate_listener',
                'onSaveCallback',
            ];
        }
    }
}
