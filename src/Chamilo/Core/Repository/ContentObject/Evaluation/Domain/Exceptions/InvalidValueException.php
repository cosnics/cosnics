<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Domain\Exceptions;

/**
 * Class InvalidValueException
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Domain\Exceptions
 *
 * @author - Stefan Gabriëls - Hogeschool Gent
 */
class InvalidValueException extends \Exception implements CuriosImportException
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
     * @var mixed
     */
    protected $value;

    /**
     * InvalidValueException constructor.
     * @param int $line
     * @param string $field
     * @param string $type
     * @param $value
     */
    public function __construct(int $line, string $field, string $type, $value)
    {
        parent::__construct(sprintf('Line %s: %s value \'%s\' given for field \'%s\'', $line, $this->getFromType($type), $value, $field));

        $this->csvLine = $line;
        $this->field = $field;
        $this->type = $type;
        $this->value = $value;
    }

    protected function getFromType(string $type)
    {
        switch ($type)
        {
            case 'number':
                return 'Non-numeric';
            case 'string':
                return 'Non-string';
            case 'date':
                return 'Non-date';
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
        return ['line' => $this->csvLine, 'type' => $this->type, 'field' => $this->field, 'value' => $this->value];
    }
}
