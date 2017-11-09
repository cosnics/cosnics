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
     * @see \Chamilo\Libraries\Storage\DataManager\Interfaces\DataManagerRepositoryInterface::create()
     */
    public function create(DataClass $object)
    {
        return $object->create();
    }

    /**
     *
     * @see \Chamilo\Libraries\Storage\DataManager\Interfaces\DataManagerRepositoryInterface::update()
     */
    public function update(DataClass $object)
    {
        return $object->update();
    }

    /**
     *
     * @see \Chamilo\Libraries\Storage\DataManager\Interfaces\DataManagerRepositoryInterface::delete()
     */
    public function delete(DataClass $object)
    {
        return $object->delete();
    }
}