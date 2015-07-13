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
class RendererFactory
{

    /**
     *
     * @var string
     */
    private $rendererType;

    /**
     *
     * @var \Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface
     */
    private $dataProvider;

    /**
     *
     * @var \Chamilo\Libraries\Calendar\Renderer\Legend
     */
    private $legend;

    /**
     *
     * @var integer
     */
    private $displayTime;

    /**
     *
     * @var string
     */
    private $linkTarget;

    /**
     *
     * @param string $rendererType
     * @param \Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface $dataProvider
     * @param \Chamilo\Libraries\Calendar\Renderer\Legend $legend
     * @param integer $displayTime
     * @param string $linkTarget
     */
    public function __construct($rendererType, CalendarRendererProviderInterface $dataProvider, Legend $legend,
        $displayTime, $linkTarget)
    {
        $this->rendererType = $rendererType;
        $this->dataProvider = $dataProvider;
        $this->legend = $legend;
        $this->displayTime = $displayTime;
        $this->linkTarget = $linkTarget;
    }

    /**
     *
     * @return string
     */
    public function getRendererType()
    {
        return $this->rendererType;
    }

    /**
     *
     * @param string $rendererType
     */
    public function setRendererType($rendererType)
    {
        $this->rendererType = $rendererType;
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface
     */
    public function getDataProvider()
    {
        return $this->dataProvider;
    }

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface $dataProvider
     */
    public function setDataProvider(CalendarRendererProviderInterface $dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Renderer\Legend
     */
    public function getLegend()
    {
        return $this->legend;
    }

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Renderer\Legend $legend
     */
    public function setLegend(Legend $legend)
    {
        $this->legend = $legend;
    }

    /**
     *
     * @return integer
     */
    public function getDisplayTime()
    {
        return $this->displayTime;
    }

    /**
     *
     * @param integer $displayTime
     */
    public function setDisplayTime($displayTime)
    {
        $this->displayTime = $displayTime;
    }

    /**
     *
     * @return string
     */
    public function getLinkTarget()
    {
        return $this->linkTarget;
    }

    /**
     *
     * @param string $linkTarget
     */
    public function setLinkTarget($linkTarget)
    {
        $this->linkTarget = $linkTarget;
    }

    /**
     * Constructs the renderer and runs it
     */
    public function render()
    {
        return $this->getRenderer()->render();
    }

    /**
     *
     * @throws \Exception
     * @return \Chamilo\Libraries\Calendar\Renderer\Renderer
     */
    public function getRenderer()
    {
        $className = __NAMESPACE__ . '\Type\\' . $this->getRendererType() . 'Renderer';

        return new $className(
            $this->getDataProvider(),
            $this->getLegend(),
            $this->getDisplayTime(),
            $this->getLinkTarget());
    }
}
