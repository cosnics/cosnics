<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service;

use Chamilo\Core\Repository\ContentObject\Evaluation\Domain\Exceptions\CuriosImportException;
use Chamilo\Core\Repository\ContentObject\Evaluation\Domain\Exceptions\DuplicateFieldsException;
use Chamilo\Core\Repository\ContentObject\Evaluation\Domain\Exceptions\InvalidValueException;
use Chamilo\Core\Repository\ContentObject\Evaluation\Domain\Exceptions\MissingFieldsException;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class ImportFromCuriosExceptionDisplayService
{
    /**
     * @param CuriosImportException $ex
     * @param Translator $translator
     * @param string $translateContext
     * @param string $fieldSeparator
     * @return array
     */
    public function translateExceptionProperties(CuriosImportException $ex, Translator $translator, string $translateContext, string $fieldSeparator = ", "): array
    {
        $properties = $ex->getProperties();

        switch (get_class($ex))
        {
            case InvalidValueException::class:
                $properties['type'] = $translator->trans('Type' . $properties['type'], [], $translateContext);
                break;
            case DuplicateFieldsException::class:
            case MissingFieldsException::class:
                $properties['fields'] = implode($fieldSeparator, $properties['fields']);
                break;
        }

        $getPropKey = function ($prop) {
            return '{' . strtoupper($prop) . '}';
        };

        return array_combine(array_map($getPropKey, array_keys($properties)), $properties);
    }

    /**
     * @param CuriosImportException $ex
     * @return string
     */
    public function getExceptionName(CuriosImportException $ex): string
    {
        $array = explode('\\', get_class($ex));
        return end($array);
    }
}