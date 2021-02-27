<?php

namespace Chamilo\Application\Lti\Component;

use Chamilo\Application\Lti\Manager;
use Chamilo\Application\Lti\Service\Outcome\OutcomeWebservice;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;

/**
 * Class BasicOutcomesComponent
 *
 * @package Chamilo\Application\Lti\Component
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class BasicOutcomesComponent extends Manager implements NoAuthenticationSupport
{

    /**
     * @return string
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    function run()
    {
//        $handle = fopen(__DIR__ . '/log.txt', 'a+');
//
//        foreach (apache_request_headers() as $name => $value)
//        {
//            $message = 'HEADER ' . $name . ': ' . $value . PHP_EOL;
//            fwrite($handle, $message);
//        }
//
//        fwrite($handle, file_get_contents('php://input'));
//        fwrite($handle, PHP_EOL);
//        fwrite($handle, PHP_EOL);

        $outcomeWebservice = $this->getService(OutcomeWebservice::class);
        $result = $outcomeWebservice->handleRequest($this->getRequest());

//        fwrite($handle, $result);
//        fwrite($handle, PHP_EOL);
//        fwrite($handle, PHP_EOL);
//        fclose($handle);

        return $result;
    }
}