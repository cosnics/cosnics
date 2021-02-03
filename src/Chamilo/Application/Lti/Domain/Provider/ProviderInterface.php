<?php

namespace Chamilo\Application\Lti\Domain\Provider;

/**
 * @package Chamilo\Application\Lti\Domain\Provider
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface ProviderInterface
{
    /**
     * @return string
     */
    public function getUniqueId();

    /**
     * @return string
     */
    public function getLaunchUrl();

    /**
     * @return string
     */
    public function getKey();

    /**
     * @return string
     */
    public function getSecret();

}