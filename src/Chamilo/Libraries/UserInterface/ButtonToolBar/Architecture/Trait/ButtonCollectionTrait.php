<?php
namespace Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait;

use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\Button;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\ButtonGroup;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\DropDownButtonCollection;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\SplitDropdownButtonCollection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait ButtonCollectionTrait
{
    /**
     * @var ArrayCollection<ButtonGroup|Button|DropDownButtonCollection|SplitDropdownButtonCollection>
     */
    private ArrayCollection $buttons;

    public function addButton(ButtonGroup|Button|DropDownButtonCollection|SplitDropdownButtonCollection $button): static
    {
        $this->getButtons()->add($button);

        return $this;
    }

    /**
     * @param ArrayCollection<ButtonGroup|Button|DropDownButtonCollection|SplitDropdownButtonCollection> $buttons
     */
    public function addButtons(ArrayCollection $buttons): static
    {
        foreach ($buttons as $button) {
            $this->addButton($button);
        }

        return $this;
    }

    /**
     * @return ArrayCollection<ButtonGroup|Button|DropDownButtonCollection|SplitDropdownButtonCollection>
     */
    public function getButtons(): ArrayCollection
    {
        return $this->buttons;
    }

    /**
     * @param ArrayCollection<ButtonGroup|Button|DropDownButtonCollection|SplitDropdownButtonCollection> $buttons
     */
    public function setButtons(ArrayCollection $buttons): static
    {
        $this->buttons = $buttons;

        return $this;
    }

    public function hasButtons(): bool
    {
        return !$this->getButtons()->isEmpty();
    }
}
