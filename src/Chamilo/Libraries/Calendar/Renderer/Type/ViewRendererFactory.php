<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type;

use Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface;
use Chamilo\Libraries\Calendar\Renderer\Legend;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ViewRendererFactory
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
     * @var \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem[]
     */
    private $actions;

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
        $displayTime, $actions = array(), $linkTarget = '')
    {
        $this->rendererType = $rendererType;
        $this->dataProvider = $dataProvider;
        $this->legend = $legend;
        $this->displayTime = $displayTime;
        $this->actions = $actions;
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
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem[]
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem[] $actions
     */
    public function setActions($actions)
    {
        $this->actions = $actions;
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
        $className = __NAMESPACE__ . '\View\\' . $this->getRendererType() . 'Renderer';

        return new $className(
            $this->getDataProvider(),
            $this->getLegend(),
            $this->getDisplayTime(),
            $this->getActions(),
            $this->getLinkTarget());
    }
}
