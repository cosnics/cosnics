<?php
namespace Chamilo\Libraries\UserInterface\ActionBar\Architecture\Trait;

use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\Button;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\DropDownButton;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\SplitDropdownButton;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Libraries\UserInterface\ActionBar\Architecture\Trait
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait ButtonGroupButtonsTrait
{
    /**
     * @var ArrayCollection<\Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\Button|\Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\DropDownButton|\Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\SplitDropdownButton>
     */
    private ArrayCollection $groupButtons;

    public function addGroupButton(Button|DropDownButton|SplitDropdownButton $groupButton): static
    {
        $this->getGroupButtons()->add($groupButton);

        return $this;
    }

    /**
     * @param ArrayCollection<\Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\Button|\Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\DropDownButton|\Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\SplitDropdownButton> $dropDownButtons
     */
    public function addGroupButtons(ArrayCollection $dropDownButtons): static
    {
        foreach ($dropDownButtons as $dropDownButton) {
            $this->addGroupButton($dropDownButton);
        }

        return $this;
    }

    /**
     * @return ArrayCollection<\Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\Button|\Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\DropDownButton|\Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\SplitDropdownButton>
     */
    public function getGroupButtons(): ArrayCollection
    {
        return $this->groupButtons;
    }

    /**
     * @param ArrayCollection<\Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\Button|\Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\DropDownButton|\Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\SplitDropdownButton> $groupButtons
     */
    public function setGroupButtons(ArrayCollection $groupButtons): static
    {
        $this->groupButtons = $groupButtons;

        return $this;
    }

    public function hasGroupButtons(): bool
    {
        return !$this->getGroupButtons()->isEmpty();
    }
}
