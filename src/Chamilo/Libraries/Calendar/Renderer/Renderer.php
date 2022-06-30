<?php
namespace Chamilo\Libraries\Calendar\Renderer;

use Chamilo\Libraries\Architecture\Traits\ClassContext;
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
    use ClassContext;

    private CalendarRendererProviderInterface $dataProvider;

    public function __construct(CalendarRendererProviderInterface $dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }

    abstract public function render(): string;

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface
     */
    public function getDataProvider(): CalendarRendererProviderInterface
    {
        return $this->dataProvider;
    }

    /**
     * @throws \ReflectionException
     */
    public static function package(): string
    {
        return static::context();
    }
}
