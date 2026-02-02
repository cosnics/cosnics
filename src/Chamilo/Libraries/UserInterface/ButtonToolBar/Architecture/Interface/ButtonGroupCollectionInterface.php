<?php
namespace Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface;

use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\Button;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\DropDownButtonCollection;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\SplitDropdownButtonCollection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface ButtonGroupCollectionInterface extends ButtonInterface
{
    public function addButton(Button|DropDownButtonCollection|SplitDropdownButtonCollection $groupButton): static;

    /**
     * @param ArrayCollection<\Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\Button|\Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\DropDownButtonCollection|\Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\SplitDropdownButtonCollection> $dropDownButtons
     */
    public function addButtons(ArrayCollection $dropDownButtons): static;

    /**
     * @return ArrayCollection<\Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\Button|\Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\DropDownButtonCollection|\Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\SplitDropdownButtonCollection>
     */
    public function getButtons(): ArrayCollection;

    public function hasButtons(): bool;

    /**
     * @param ArrayCollection<\Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\Button|\Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\DropDownButtonCollection|\Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\SplitDropdownButtonCollection> $groupButtons
     */
    public function setButtons(ArrayCollection $groupButtons): static;
}
