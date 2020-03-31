<?php
namespace Chamilo\Libraries\Format;

use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Format\Theme\ThemePathBuilder;

/**
 *
 * @package Chamilo\Libraries\Format
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 *
 * @deprecated Use the \Chamilo\Libraries\Format\Theme\ThemePathBuilder service now
 */
class Theme
{
    /**
     *
     * @var \Chamilo\Libraries\Format\Theme\ThemePathBuilder
     */
    private static $instance;

    /**
     * @return \Chamilo\Libraries\Format\Theme\ThemePathBuilder
     *
     * @deprecated Use this as a service now
     */
    static public function getInstance()
    {
        if (is_null(static::$instance))
        {
            $container = DependencyInjectionContainerBuilder::getInstance()->createContainer();
            self::$instance = $container->get(ThemePathBuilder::class);
        }

        return static::$instance;
    }
}
