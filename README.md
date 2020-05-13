Contao Language Dependent Modules Bundle
========================================

This Contao bundle provides a special module where you can configure your modules based on available languages.  
This bundle is made for Contao 4.9 and newer.

![CI](https://github.com/1up-lab/contao-language-dependent-modules-bundle/workflows/CI/badge.svg)
[![Total Downloads](https://img.shields.io/packagist/dt/oneup/contao-language-dependent-modules-bundle.svg?style=flat-square)](https://packagist.org/packages/oneup/contao-language-dependent-modules-bundle)


Why this bundle?
----------------
The idea is to get rid of multiple page layouts (or inserttags with module IDs in combination with `{{iflng::*}}` tags) for multiple languages.

Usage
-----

You can now easy configure you modules for every language available:
<img width="1186" alt="Bildschirmfoto 2020-05-13 um 10 25 13" src="https://user-images.githubusercontent.com/754921/81790808-3503b800-9506-11ea-8c10-c294867d2533.png">

and include this module in your layout: 
<img width="820" alt="Bildschirmfoto 2020-05-13 um 10 44 04" src="https://user-images.githubusercontent.com/754921/81791211-b65b4a80-9506-11ea-81ca-dff019b53a97.png">

This module will then render the configured module for each configured language. 

Developers
----------

The bundle provides an additional input type to use in your own code:

**Use the default configuration** (loads all available modules):
```php
<?php
// config/dca/tl_modules.php

$GLOBALS['TL_DCA']['tl_module']['fields'] += [
    'myLanguageDependentModules' => [
        'inputType' => 'languageDependentModulesSurrogate',
        'eval' => [
            'tl_class' => 'w50',
        ],
        'sql' => 'blob NULL',
    ],
];
```

There is even more and you can customize this widget to your needs:

**use `options`** (for custom list of modules):
```php
<?php
// config/dca/tl_modules.php

$GLOBALS['TL_DCA']['tl_module']['fields'] += [
    'myLanguageDependentModules' => [
        'inputType' => 'languageDependentModulesSurrogate',
        'options' => [
            0 => 'My module 0',
            1 => 'My module 1',
        ],
        'eval' => [
            'tl_class' => 'w50',
        ],
        'sql' => 'blob NULL',
    ],
];
```

**use `options_callback`** (for custom list of modules):
```php
<?php
// config/dca/tl_modules.php

$GLOBALS['TL_DCA']['tl_module']['fields'] += [
    'myLanguageDependentModules' => [
        'inputType' => 'languageDependentModulesSurrogate',
        'options_callback' => ['my.service_id', 'methodName'],
        'eval' => [
            'tl_class' => 'w50',
        ],
        'sql' => 'blob NULL',
    ],
];

// you can also use this with service tagging, see https://docs.contao.org/dev/framework/dca/#registering-callbacks
```

**use `eval['modules']`** (to filter for custom types):
```php
<?php
// config/dca/tl_modules.php

$GLOBALS['TL_DCA']['tl_module']['fields'] += [
    'myLanguageDependentModules' => [
        'inputType' => 'languageDependentModulesSurrogate',
        'eval' => [
            'tl_class' => 'w50',
            'eval' = > [
                'modules' => [
                    'navigation',
                    'customnav',
                    'search',
                    'html',
                    'myCustomModule',
                ],
            ],
        ],
        'sql' => 'blob NULL',
    ],
];
```

**Change the label for the blank option**:
```php
<?php
// config/dca/tl_modules.php

$GLOBALS['TL_DCA']['tl_module']['fields'] += [
    'myLanguageDependentModules' => [
        'inputType' => 'languageDependentModulesSurrogate',
        'eval' => [
            'tl_class' => 'w50',
            'eval' = > [
                'blankOptionLabel' => 'My Label'
            ],
        ],
        'sql' => 'blob NULL',
    ],
];
```

_Note:_ If you just want to change the label for the blank option of the default field, the key is: `tl.module.languageDependentModulesBlankOptionLabel`.
