<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Domain\Exceptions;

/**
 * Class DuplicateFieldsException
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Domain\Exceptions
 *
 * @author - Stefan Gabriëls - Hogeschool Gent
 */
class DuplicateFieldsException extends \Exception implements CuriosImportException
{
    /**
     * @var int
     */
    protected $csvLine;

    /**
     * @var array
     */
    protected $fields;

    /**
     * DuplicateFieldsException constructor.
     * @param int $line
     * @param array $fields
     */
    public function __construct(int $line, array $fields)
    {
        parent::__construct(sprintf('Line %s: Non-unique fields present: %s', $line, implode(', ', $fields)));

        $this->csvLine = $line;
        $this->fields = $fields;
    }

    /**
     * @return int
     */
    public function getCSVLine(): int
    {
        return $this->csvLine;
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @return array
     */
    public function getProperties(): array
    {
        return ['line' => $this->csvLine, 'fields' => $this->fields];
    }
}
