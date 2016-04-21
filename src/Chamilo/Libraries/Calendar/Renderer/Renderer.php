<?php
namespace Chamilo\Libraries\Calendar\Renderer;

use Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Renderer
{
    use \Chamilo\Libraries\Architecture\Traits\ClassContext;

    /**
     *
     * @var CalendarRendererProviderInterface
     */
    private $dataProvider;

    /**
     *
     * @param CalendarRendererProviderInterface $dataProvider
     */
    public function __construct(CalendarRendererProviderInterface $dataProvider)
    {
        if (! $dataProvider instanceof CalendarRendererProviderInterface)
        {
            throw new \Exception('Please implement the CalendarRendererProviderInterface in ' . get_class($dataProvider));
        }

        $this->dataProvider = $dataProvider;
    }

    /**
     *
     * @return CalendarRendererProviderInterface
     */
    public function getDataProvider()
    {
        return $this->dataProvider;
    }

    /**
     * Render the calendar
     *
     * @return string
     */
    abstract public function render();

    /**
     *
     * @return string
     */
    public static function package()
    {
        return static :: context();
    }
}
