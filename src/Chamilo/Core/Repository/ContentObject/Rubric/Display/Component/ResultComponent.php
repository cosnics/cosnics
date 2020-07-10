<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Display\Component;

use Chamilo\Core\Repository\ContentObject\Rubric\Display\Manager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;

/**
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Display\Component
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class ResultComponent extends Manager implements DelegateComponent
{

    /**
     * @return string
     * @throws \Doctrine\ORM\ORMException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     */
    function run()
    {
        return 'Result';
    }
}
