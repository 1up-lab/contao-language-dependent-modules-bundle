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

        $total = \count($GLOBALS['TL_DCA'][$table]['fields']);
        $inputTypes = \count(array_keys(array_column($fields, 'inputType')));
        $diff = $total - $inputTypes;

        $affectedFields = array_keys(
            array_column($fields, 'inputType'),
            'languageDependentModulesSurrogate',
            true
        );

        foreach ($affectedFields as $affectedField) {
            $field = \array_slice($GLOBALS['TL_DCA'][$table]['fields'], $affectedField + $diff, 1);
            $keys = array_keys($field);

            if (!\count($keys)) {
                continue;
            }

            $key = $keys[0];

            if (!\array_key_exists('eval', $GLOBALS['TL_DCA'][$table]['fields'][$key])) {
                $GLOBALS['TL_DCA'][$table]['fields'][$key]['eval'] = [];
            }

            if (!\array_key_exists('save_callback', $GLOBALS['TL_DCA'][$table]['fields'][$key])) {
                $GLOBALS['TL_DCA'][$table]['fields'][$key]['save_callback'] = [];
            }

            if (!\array_key_exists('options', $GLOBALS['TL_DCA'][$table]['fields'][$key]) &&
                !\array_key_exists('options_callback', $GLOBALS['TL_DCA'][$table]['fields'][$key])) {
                $types = [];

                if (\array_key_exists('modules', $GLOBALS['TL_DCA'][$table]['fields'][$key]['eval'])) {
                    $types = $GLOBALS['TL_DCA'][$table]['fields'][$key]['eval']['modules'];
                }

                $GLOBALS['TL_DCA'][$table]['fields'][$key]['options'] = $this->moduleProvider->getModules($types);
            }

            if (!\array_key_exists('languages', $GLOBALS['TL_DCA'][$table]['fields'][$key]['eval'])) {
                $GLOBALS['TL_DCA'][$table]['fields'][$key]['eval']['languages'] = $this->languageProvider->getLanguages();
            }

            if (!\array_key_exists('blankOptionLabel', $GLOBALS['TL_DCA'][$table]['fields'][$key]['eval'])) {
                $GLOBALS['TL_DCA'][$table]['fields'][$key]['eval']['blankOptionLabel'] = $this->translator->trans(
                    sprintf('tl_module.%sBlankOptionLabel', $key),
                    [],
                    'contao_module'
                );
            }

            $GLOBALS['TL_DCA'][$table]['fields'][$key]['eval']['includeBlankOption'] = true;
            $GLOBALS['TL_DCA'][$table]['fields'][$key]['save_callback'][] = [
                LanguageDependentModulesSurrogateListener::class,
                'onSaveCallback',
            ];
        }
    }
}
