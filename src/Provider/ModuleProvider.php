<?php

declare(strict_types=1);

namespace Oneup\Contao\LanguageDependentModulesBundle\Provider;

use Doctrine\DBAL\Connection;

class ModuleProvider
{
    protected Connection $database;

    public function __construct(Connection $database)
    {
        $this->database = $database;
    }

    public function getModules(array $types = null): array
    {
        $statement = $this->database->prepare("
            SELECT m.id, m.name
            FROM tl_module m
            WHERE m.type <> 'language_dependent_modules_surrogate'
            ORDER BY m.name
        ");

        if (\is_array($types) && \count($types)) {
            $statement = $this->database->prepare(
                sprintf("
                    SELECT m.id, m.name
                    FROM tl_module m
                    WHERE m.type IN (%s) AND m.type <> 'language_dependent_modules_surrogate'
                    ORDER BY m.name
                ", $this->getTypeString($types)
                )
            );
        }

        $modules = $statement->executeQuery()->fetchAllAssociative();

        $list = [];

        foreach ($modules as $module) {
            $list[$module['id']] = $module['name'];
        }

        return $list;
    }

    private function getTypeString(array $list): string
    {
        $list = array_map(fn ($input) => sprintf('"%s"', $input), $list);

        return implode(', ', $list);
    }
}
