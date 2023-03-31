<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Domain\Exceptions;

/**
 * Class NoValueException
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Domain\Exceptions
 *
 * @author - Stefan Gabriëls - Hogeschool Gent
 */
class NoValueException extends \Exception implements GradeBookImportException
{
    /**
     * @var int
     */
    protected $csvLine;

    /**
     * @var string
     */
    protected $field;

    /**
     * @var string
     */
    protected $type;

    /**
     * NoValueException constructor.
     * @param int $line
     * @param string $field
     * @param string $type
     */
    public function __construct(int $line, string $field, string $type)
    {
        parent::__construct(sprintf('Line %s: No value given for field \'%s\'', $line, $field));

        $this->csvLine = $line;
        $this->field = $field;
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getCSVLine(): int
    {
        return $this->csvLine;
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getProperties(): array
    {
        return ['line' => $this->csvLine, 'type' => $this->type, 'field' => $this->field];
    }
}
