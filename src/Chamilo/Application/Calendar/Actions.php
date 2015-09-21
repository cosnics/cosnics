<?php
namespace Chamilo\Application\Calendar;

use Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer;

/**
 *
 * @package Chamilo\Application\Calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Actions
{

    /**
     *
     * @var \Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer
     */
    private $dynamicVisualTabsRenderer;

    /**
     *
     * @param \Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer $dynamicVisualTabsRenderer
     */
    public function __construct(DynamicVisualTabsRenderer $dynamicVisualTabsRenderer)
    {
        $this->dynamicVisualTabsRenderer = $dynamicVisualTabsRenderer;
    }

    /**
     *
     * @return \libraries\architecture\application\Application
     */
    public function getDynamicVisualTabsRenderer()
    {
        return $this->dynamicVisualTabsRenderer;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Tabs\DynamicVisualTab[]
     */
    public function get()
    {
        return array();
    }
}