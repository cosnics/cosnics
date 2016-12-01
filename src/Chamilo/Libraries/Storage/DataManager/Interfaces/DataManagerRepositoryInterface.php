<?php
namespace Chamilo\Libraries\Storage\DataManager\Interfaces;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * Abstract repository that can be used as a base for repositories that still use the old DataManagers. Groups common
 * functionality
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface DataManagerRepositoryInterface
{
    /**
     * Wrapper for the creation of an object
     *
     * @param DataClass $object
     *
     * @return bool
     */
    public function create(DataClass $object);

    /**
     * Wrapper for the update of an object
     *
     * @param DataClass $object
     *
     * @return bool
     */
    public function update(DataClass $object);

    /**
     * Wrapper for the deletion of an object
     *
     * @param DataClass $object
     *
     * @return bool
     */
    public function delete(DataClass $object);
}