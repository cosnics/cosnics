<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Domain\Exceptions;

/**
 * Class DuplicateResultException
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Domain\Exceptions
 *
 * @author - Stefan Gabriëls - Hogeschool Gent
 */
class DuplicateResultException extends \Exception implements GradeBookImportException
{
    /**
     * @var int
     */
    protected $csvLine;

    /**
     * @var string
     */
    protected $lastname;

    /**
     * @var string
     */
    protected $firstname;

    /**
     * DuplicateResultException constructor.
     * @param int $line
     * @param string $lastname
     * @param string $firstname
     */
    public function __construct(int $line, string $lastname, string $firstname)
    {
        parent::__construct(sprintf('Line %s: Duplicate result found for student: %s %s', $line, $lastname, $firstname));

        $this->csvLine = $line;
        $this->lastname = $lastname;
        $this->firstname = $firstname;
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
    public function getLastName(): string
    {
        return $this->lastname;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstname;
    }

    /**
     * @return array
     */
    public function getProperties(): array
    {
        return ['line' => $this->csvLine, 'firstname' => $this->firstname, 'lastname' => $this->lastname];
    }
}
