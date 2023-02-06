<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Domain\Exceptions;

/**
 * Class InvalidValueException
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Domain\Exceptions
 *
 * @author - Stefan Gabriëls - Hogeschool Gent
 */
class InvalidValueException extends \Exception implements GradeBookImportException
{
    /**
     * @var int
     */
    protected $csvLine;

    /**
     * @var int
     */
    protected $csvColumn;

    /**
     * @var string
     */
    protected $field;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * InvalidValueException constructor.
     * @param int $line
     * @param int $column
     * @param string $field
     * @param string $type
     * @param $value
     */
    public function __construct(int $line, int $column, string $field, string $type, $value)
    {
        parent::__construct(sprintf('Line %s, column %s: %s value \'%s\' given for field \'%s\'', $line, $column, $this->getFromType($type), $value, $field));

        $this->csvLine = $line;
        $this->csvColumn = $column;
        $this->field = $field;
        $this->type = $type;
        $this->value = $value;
    }

    protected function getFromType(string $type)
    {
        switch ($type)
        {
            case 'string':
                return 'Non-string';
            case 'score':
                return 'Non-numeric';
            default:
                return 'Invalid';
        }
    }

    /**
     * @return int
     */
    public function getCSVLine(): int
    {
        return $this->csvLine;
    }

    /**
     * @return int
     */
    public function getCSVColumn(): int
    {
        return $this->csvColumn;
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
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return array
     */
    public function getProperties(): array
    {
        return ['line' => $this->csvLine, 'column' => $this->csvColumn, 'type' => $this->type, 'field' => $this->field, 'value' => $this->value];
    }
}
