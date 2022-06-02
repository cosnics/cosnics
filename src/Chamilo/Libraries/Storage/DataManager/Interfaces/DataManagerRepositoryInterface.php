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

    public function create(DataClass $dataClass): bool;

    public function delete(DataClass $dataClass): bool;

    public function update(DataClass $dataClass): bool;
}