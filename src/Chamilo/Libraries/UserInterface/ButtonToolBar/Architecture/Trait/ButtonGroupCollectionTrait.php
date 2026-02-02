<?php
namespace Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait;

use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\Button;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\DropDownButtonCollection;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\SplitDropdownButtonCollection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait ButtonGroupCollectionTrait
{
    use ButtonCollectionTrait {
        addButton as public addBaseButton;
        addButtons as public addBaseButtons;
        getButtons as public getBaseButtons;
        hasButtons as public hasBaseButtons;
        setButtons as public setBaseButtons;
    }

    public function addButton(Button|DropDownButtonCollection|SplitDropdownButtonCollection $groupButton): static
    {
        $this->addBaseButton($groupButton);

        return $this;
    }

    /**
     * @param ArrayCollection<\Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\Button|\Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\DropDownButtonCollection|\Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\SplitDropdownButtonCollection> $groupButtons
     */
    public function addButtons(ArrayCollection $groupButtons): static
    {
        return $this->addBaseButtons($groupButtons);
    }

    /**
     * @return ArrayCollection<\Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\Button|\Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\DropDownButtonCollection|\Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\SplitDropdownButtonCollection>
     */
    public function getButtons(): ArrayCollection
    {
        return $this->getBaseButtons();
    }

    public function hasButtons(): bool
    {
        return $this->hasBaseButtons();
    }

    /**
     * @param ArrayCollection<\Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\Button|\Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\DropDownButtonCollection|\Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\SplitDropdownButtonCollection> $groupButtons
     */
    public function setButtons(ArrayCollection $groupButtons): static
    {
        $this->setBaseButtons($groupButtons);

        return $this;
    }
}
