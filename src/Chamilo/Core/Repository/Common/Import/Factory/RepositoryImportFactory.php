<?php

namespace Chamilo\Core\Repository\Common\Import\Factory;

/**
 * Class RepositoryImportFactory
 * @package Chamilo\Core\Repository\Common\Import\Factory
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
abstract class RepositoryImportFactory implements ImportFactoryInterface
{
    public function getImportContext()
    {
        return \Chamilo\Core\Repository\Manager::package();
    }
}
