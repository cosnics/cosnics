<?php

namespace Chamilo\Libraries\Architecture;

/**
 * @package Chamilo\Libraries\Architecture
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ContextIdentifier
{
    /**
     * @var string
     */
    protected $contextClass;

    /**
     * @var int
     */
    protected $contextId;

    /**
     * ContextIdentifier constructor.
     *
     * @param string $contextClass
     * @param int $contextId
     */
    public function __construct(string $contextClass, int $contextId)
    {
        $this->contextClass = $contextClass;
        $this->contextId = $contextId;
    }

    /**
     * @return string
     */
    public function getContextClass(): ?string
    {
        return $this->contextClass;
    }

    /**
     * @return int
     */
    public function getContextId(): ?int
    {
        return $this->contextId;
    }
}
