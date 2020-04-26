<?php

declare(strict_types=1);

namespace Oneup\Contao\LanguageDependentModulesBundle\Provider;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Symfony\Contracts\Translation\TranslatorInterface;

class AvailableLanguageProvider
{
    private Connection $connection;
    private TranslatorInterface $translator;

    public function __construct(Connection $connection, TranslatorInterface $translator)
    {
        $this->connection = $connection;
        $this->translator = $translator;
    }

    public function getLanguages(bool $published = true): array
    {
        $languages = [];

        $statement = $this->connection->prepare('
            SELECT p.* 
            FROM tl_page p 
            WHERE 
                p.pid = 0 AND 
                p.published = :published 
            ORDER BY p.sorting ASC
        ');

        $statement->bindValue(':published', $published);
        $statement->execute();

        $rootPages = $statement->fetchAll(FetchMode::STANDARD_OBJECT);

        foreach ($rootPages as $rootPage) {
            $languages[$rootPage->language] = $this->translator->trans(
                sprintf('LNG.%s', $rootPage->language),
                [],
                'contao_languages'
            );
        }

        return $languages;
    }
}
