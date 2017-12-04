<?php
namespace Chamilo\Libraries\Calendar\Format\Renderer;

use Chamilo\Libraries\Calendar\Interfaces\CalendarRendererProviderInterface;

/**
 *
 * @package Chamilo\Libraries\Calendar\Format\Renderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class FormatRenderer
{
    use \Chamilo\Libraries\Architecture\Traits\ClassContext;

    /**
     *
     * @var \Chamilo\Libraries\Calendar\Interfaces\CalendarRendererProviderInterface
     */
    private $dataProvider;

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Interfaces\CalendarRendererProviderInterface $dataProvider
     * @throws \Exception
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
     * @return \Chamilo\Libraries\Calendar\Interfaces\CalendarRendererProviderInterface
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
        return static::context();
    }
}
