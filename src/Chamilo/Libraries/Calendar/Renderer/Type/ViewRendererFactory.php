<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type;

use Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface;
use Chamilo\Libraries\Calendar\Renderer\LegendRenderer;
use Chamilo\Libraries\Calendar\Renderer\Renderer;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ViewRendererFactory
{

    /**
     * @var \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem[]
     */
    private array $actions;

    private CalendarRendererProviderInterface $dataProvider;

    private int $displayTime;

    private LegendRenderer $legend;

    private string $linkTarget;

    private string $rendererType;

    /**
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem[] $actions
     */
    public function __construct(
        string $rendererType, CalendarRendererProviderInterface $dataProvider, LegendRenderer $legend, int $displayTime,
        array $actions = [], string $linkTarget = ''
    )
    {
        $this->rendererType = $rendererType;
        $this->dataProvider = $dataProvider;
        $this->legend = $legend;
        $this->displayTime = $displayTime;
        $this->actions = $actions;
        $this->linkTarget = $linkTarget;
    }

    /**
     * @throws \Exception
     */
    public function render(): string
    {
        return $this->getRenderer()->render();
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem[]
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    /**
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem[] $actions
     */
    public function setActions(array $actions)
    {
        $this->actions = $actions;
    }

    public function getDataProvider(): CalendarRendererProviderInterface
    {
        return $this->dataProvider;
    }

    public function setDataProvider(CalendarRendererProviderInterface $dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }

    public function getDisplayTime(): int
    {
        return $this->displayTime;
    }

    public function setDisplayTime(int $displayTime)
    {
        $this->displayTime = $displayTime;
    }

    public function getLegend(): LegendRenderer
    {
        return $this->legend;
    }

    public function setLegend(LegendRenderer $legend)
    {
        $this->legend = $legend;
    }

    public function getLinkTarget(): string
    {
        return $this->linkTarget;
    }

    public function setLinkTarget(string $linkTarget)
    {
        $this->linkTarget = $linkTarget;
    }

    /**
     * @throws \Exception
     */
    public function getRenderer(): Renderer
    {
        $className = __NAMESPACE__ . '\View\\' . $this->getRendererType() . 'Renderer';

        return new $className(
            $this->getDataProvider(), $this->getLegend(), $this->getDisplayTime(), $this->getActions(),
            $this->getLinkTarget()
        );
    }

    public function getRendererType(): string
    {
        return $this->rendererType;
    }

    public function setRendererType(string $rendererType)
    {
        $this->rendererType = $rendererType;
    }
}
