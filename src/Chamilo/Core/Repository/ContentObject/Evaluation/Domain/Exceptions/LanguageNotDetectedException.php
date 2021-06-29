<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Domain\Exceptions;

/**
 * Class LanguageNotDetectedException
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Domain\Exceptions
 *
 * @author - Stefan Gabriëls - Hogeschool Gent
 */
class LanguageNotDetectedException extends \Exception implements CuriosImportException
{
    /**
     * @var int
     */
    protected $csvLine;

    /**
     * LanguageNotDetectedException constructor.
     * @param int $line
     */
    public function __construct(int $line)
    {
        parent::__construct(sprintf('Line %s: Language not detected', $line));

        $this->csvLine = $line;
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
    public function getProperties(): array
    {
        return ['line' => $this->csvLine];
    }
}
