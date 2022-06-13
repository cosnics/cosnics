<?php
namespace Chamilo\Libraries\Format\Tabs;

use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;

/**
 *
 * @package Chamilo\Libraries\Format\Tabs
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ActionsTab extends GenericTab
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
}
