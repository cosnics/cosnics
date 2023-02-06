<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Service;

use Chamilo\Core\Repository\ContentObject\GradeBook\Domain\Exceptions\GradeBookImportException;
use Chamilo\Core\Repository\ContentObject\GradeBook\Domain\Exceptions\DuplicateResultException;
use Chamilo\Core\Repository\ContentObject\GradeBook\Domain\Exceptions\InvalidValueException;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Service
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class ImportFromGradeBookExceptionDisplayService
{
    /**
     * @param GradeBookImportException $ex
     * @param Translator $translator
     * @param string $translateContext
     * @param string $fieldSeparator
     * @return array
     */
    public function translateExceptionProperties(GradeBookImportException $ex, Translator $translator, string $translateContext, string $fieldSeparator = ", "): array
    {
        $properties = $ex->getProperties();

        switch (get_class($ex))
        {
            case InvalidValueException::class:
                $properties['type'] = $translator->trans('Type' . $properties['type'], [], $translateContext);
                break;
            case DuplicateResultException::class:
                $properties['fields'] = implode($fieldSeparator, $properties['fields']);
                break;
        }

        $getPropKey = function ($prop) {
            return '{' . strtoupper($prop) . '}';
        };

        return array_combine(array_map($getPropKey, array_keys($properties)), $properties);
    }

    /**
     * @param GradeBookImportException $ex
     * @return string
     */
    public function getExceptionName(GradeBookImportException $ex): string
    {
        $array = explode('\\', get_class($ex));
        return end($array);
    }
}