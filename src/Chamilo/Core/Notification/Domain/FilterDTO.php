<?php
namespace Chamilo\Core\Notification\Domain;

/**
 * @package Chamilo\Core\Notification\Domain
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class FilterDTO
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $description;

    /**
     * FilterDTO constructor.
     *
     * @param int $id
     * @param string $description
     */
    public function __construct(int $id, string $description)
    {
        $this->id = $id;
        $this->description = $description;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

}