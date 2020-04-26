<?php

declare(strict_types=1);

namespace Oneup\Contao\LanguageDependentModulesBundle\Dca\Callback;

use Contao\DataContainer;
use Contao\StringUtil;
use Oneup\Contao\LanguageDependentModulesBundle\Dca\Provider\AvailableLanguageProvider;

class SaveCallback
{
    private AvailableLanguageProvider $languageProvider;

    public function __construct(AvailableLanguageProvider $languageProvider)
    {
        $this->languageProvider = $languageProvider;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function onSave($value, DataContainer $dataContainer)
    {
        $values = StringUtil::deserialize($value);

        if (!\is_array($values)) {
            return $value;
        }

        $newValues = [];
        $availableLanguages = array_keys($this->languageProvider->getLanguages());

        foreach ($values as $k => $v) {
            $newValues[array_shift($availableLanguages)] = $v;
        }

        return serialize($newValues);
    }
}
