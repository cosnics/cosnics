<?php
namespace Chamilo\Core\Home\Architecture;

/**
 *
 * @package Chamilo\Core\Home\Architecture
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
interface ConfigurableInterface
{

    /**
     *
     * @return string[]
     */
    public function getConfigurationVariables();
}