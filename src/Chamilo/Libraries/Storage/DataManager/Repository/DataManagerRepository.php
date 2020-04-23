<?php
namespace Chamilo\Libraries\Storage\DataManager\Repository;

use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\Interfaces\DataManagerRepositoryInterface;

/**
 * Abstract repository that can be used as a base for repositories that still use the old DataManagers.
 *
 * @package Chamilo\Libraries\Storage\DataManager\Repository
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class DataManagerRepository implements DataManagerRepositoryInterface
{

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $dataClass
     *
     * @return boolean
     * @throws \Exception
     */
    public function create(DataClass $dataClass)
    {
        return $dataClass->create();
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $dataClass
     *
     * @return boolean
     * @throws \Exception
     */
    public function delete(DataClass $dataClass)
    {
        return $dataClass->delete();
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $dataClass
     *
     * @return boolean
     * @throws \Exception
     */
    public function update(DataClass $dataClass)
    {
        return $dataClass->update();
    }
}