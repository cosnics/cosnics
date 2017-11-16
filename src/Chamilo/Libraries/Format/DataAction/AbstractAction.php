<?php

namespace Chamilo\Libraries\Format\DataAction;

/**
 * @package Chamilo\Libraries\Format\DataAction
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop - hans.de.bisschop@ehb.be
 */
abstract class AbstractAction implements ActionInterface
{
    const TYPE_NAME = 'abstract';

    /**
     * A name for the action, used as identifier
     *
     * @var string
     */
    protected $name;

    /**
     * AbstractAction constructor.
     *
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * Converts this object's properties to an array
     *
     * @param array $baseArray
     *
     * @return array
     */
    public function toArray($baseArray = [])
    {
        $baseArray['name'] = $this->getName();
        $baseArray['type'] = static::TYPE_NAME;

        return $baseArray;
    }
}