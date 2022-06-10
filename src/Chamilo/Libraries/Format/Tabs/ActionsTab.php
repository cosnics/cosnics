<?php
namespace Chamilo\Libraries\Format\Tabs;

use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;

/**
 *
 * @package Chamilo\Libraries\Format\Tabs
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ActionsTab extends Tab
{

    /**
     * @var \Chamilo\Libraries\Format\Tabs\Action[]
     */
    private array $actions;

    /**
     * @param \Chamilo\Libraries\Format\Tabs\Action[] $actions
     */
    public function __construct(
        string $identifier, string $label, ?InlineGlyph $inlineGlyph = null, ?array $actions = []
    )
    {
        parent::__construct($identifier, $label, $inlineGlyph);
        $this->actions = $actions;
    }

    public function addAction(Action $action)
    {
        $this->actions[] = $action;
    }

    public function render(bool $isOnlyTab = false): string
    {
        $html = [];

        $html[] = $this->bodyHeader();

        foreach ($this->actions as $action)
        {
            $html[] = $action->render();
        }

        $html[] = $this->bodyFooter();

        return implode(PHP_EOL, $html);
    }

    public function bodyHeader(): string
    {
        $html = [];

        $html[] = '<div role="tabpanel" class="tab-pane" id="' . $this->getIdentifier() . '">';
        $html[] = '<div class="list-group">';

        return implode(PHP_EOL, $html);
    }

    /**
     * @return \Chamilo\Libraries\Format\Tabs\Action[]
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    /**
     * @param \Chamilo\Libraries\Format\Tabs\Action[] $actions
     */
    public function setActions(array $actions)
    {
        $this->actions = $actions;
    }

    public function getLink(): string
    {
        return '#' . $this->getIdentifier();
    }
}
