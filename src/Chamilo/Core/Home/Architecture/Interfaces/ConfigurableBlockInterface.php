<?php
namespace Chamilo\Core\Home\Architecture\Interfaces;

/**
 * @package Chamilo\Core\Home\Architecture\Interfaces
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
interface ConfigurableBlockInterface
{

    /**
     * @return string[]
     */
    public function getConfigurationVariables(): array;
}