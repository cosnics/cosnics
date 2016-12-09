<?php

namespace Chamilo\Libraries\Storage\DataManager\Repository;

use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\Interfaces\DataManagerRepositoryInterface;

/**
 * Abstract repository that can be used as a base for repositories that still use the old DataManagers. Groups common
 * functionality
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class DataManagerRepository implements DataManagerRepositoryInterface
{
    /**
     * Wrapper for the creation of an object
     *
     * @param DataClass $object
     *
     * @return bool
     */
    public function create(DataClass $object)
    {
        return $object->create();
    }

    /**
     * Wrapper for the update of an object
     *
     * @param DataClass $object
     *
     * @return bool
     */
    public function update(DataClass $object)
    {
        return $object->update();
    }

    /**
     * Wrapper for the deletion of an object
     *
     * @param DataClass $object
     *
     * @return bool
     */
    public function delete(DataClass $object)
    {
        return $object->delete();
    }
}