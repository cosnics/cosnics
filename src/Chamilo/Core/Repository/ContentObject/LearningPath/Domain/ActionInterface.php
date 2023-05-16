<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Domain;

/**
 * Interface to describe an action
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
interface ActionInterface
{
    /**
     * Converts this object's properties to an array
     *
     * @return array
     */
    public function toArray();

    /**
     * Returns a unique name for the action
     *
     * @return string
     */
    public function getName();
}