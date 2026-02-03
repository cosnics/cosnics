<?php
namespace Chamilo\Libraries\UserInterface\Tab\Architecture\Domain;

use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\InlineGlyph;

/**
 * @package Chamilo\Libraries\UserInterface\Tab\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ActionsTab extends GenericTab
{
    /**
     * @var \Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\Action[]
     */
    private array $actions;

    /**
     * @param \Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\Action[] $actions
     */
    public function __construct(
        string $identifier, string $label, ?InlineGlyph $inlineGlyph = null, ?array $actions = []
    )
    {
        parent::__construct($identifier, $label, $inlineGlyph);
        $this->actions = $actions;
    }

    public function addAction(Action $action): static
    {
        $this->actions[] = $action;

        return $this;
    }

    /**
     * @return \Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\Action[]
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    /**
     * @param \Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\Action[] $actions
     */
    public function setActions(array $actions): static
    {
        $this->actions = $actions;

        return $this;
    }
}
