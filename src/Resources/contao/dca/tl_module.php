<?php

declare(strict_types=1);

$GLOBALS['TL_DCA']['tl_module']['palettes'] += [
    'language_dependent_modules_surrogate' => '
        {title_legend},name,type;
        {module_legend},modules;
    ',
];

$GLOBALS['TL_DCA']['tl_module']['fields'] += [
    'modules' => [
        'inputType' => 'languageDependentModulesSurrogate',
        'eval' => [
            'tl_class' => 'w50',
        ],
        'sql' => 'blob NULL',
    ],
];
