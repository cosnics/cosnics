<?php

namespace Chamilo\Application\Plagiarism\Component;

use Chamilo\Application\Plagiarism\Manager;
use Chamilo\Application\Plagiarism\Service\Turnitin\EulaService;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;

/**
 * @package Chamilo\Application\Plagiarism\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TurnitinEulaComponent extends Manager implements NoAuthenticationSupport
{

    /**
     * @return string|\Symfony\Component\HttpFoundation\Response
     */
    function run()
    {

    }

    /**
     * @return \Chamilo\Application\Plagiarism\Service\Turnitin\EulaService
     */
    protected function getEulaService()
    {
        return $this->getService(EulaService::class);
    }
}