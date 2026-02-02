<?php
namespace Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait;

use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\SubButton;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\SubButtonDivider;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\SubButtonHeader;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait DropDownButtonCollectionTrait
{
    /**
     * @var ArrayCollection<SubButton|SubButtonDivider|SubButtonHeader> $dropDownButton >
     */
    private ArrayCollection $buttons;

    /**
     * @var string[]
     */
    private array $dropDownClasses = [];

    public function addButton(SubButton|SubButtonDivider|SubButtonHeader $dropDownButton): static
    {
        $this->getButtons()->add($dropDownButton);

        return $this;
    }

    /**
     * @param ArrayCollection<SubButton|SubButtonDivider|SubButtonHeader> $buttons
     */
    public function addButtons(ArrayCollection $buttons): static
    {
        foreach ($buttons as $button) {
            $this->addButton($button);
        }

        return $this;
    }

    /**
     * @return ArrayCollection<SubButton|SubButtonDivider|SubButtonHeader>
     */
    public function getButtons(): ArrayCollection
    {
        return $this->buttons;
    }

    /**
     * @param ArrayCollection<SubButton|SubButtonDivider|SubButtonHeader> $buttons
     */
    public function setButtons(ArrayCollection $buttons): static
    {
        $this->buttons = $buttons;

        return $this;
    }

    public function getDropDownClasses(): array
    {
        return $this->dropDownClasses;
    }

    /**
     * @param string[] $classes
     */
    public function setDropDownClasses(array $classes): static
    {
        $this->dropDownClasses = $classes;

        return $this;
    }

    public function hasButtons(): bool
    {
        return !$this->getButtons()->isEmpty();
    }
}
