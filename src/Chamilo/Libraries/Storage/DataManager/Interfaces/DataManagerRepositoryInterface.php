<?php
namespace Chamilo\Libraries\Storage\DataManager\Interfaces;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * Abstract repository that can be used as a base for repositories that still use the old DataManagers.
 *
 * @package Chamilo\Libraries\Storage\DataManager\Interfaces
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface DataManagerRepositoryInterface
{

    /**
     * Wrapper for the creation of an object
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $dataClass
     *
     * @return boolean
     */
    public function create(DataClass $dataClass);

    /**
     * Wrapper for the deletion of an object
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $dataClass
     *
     * @return boolean
     */
    public function delete(DataClass $dataClass);

    /**
     * Wrapper for the update of an object
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $dataClass
     *
     * @return boolean
     */
    public function update(DataClass $dataClass);
}