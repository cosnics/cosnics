<?php
namespace Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface;

use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\Button;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\ButtonGroup;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\DropDownButtonCollection;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\SplitDropdownButtonCollection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface ButtonCollectionInterface extends ButtonInterface
{
    public function addButton(ButtonGroup|Button|DropDownButtonCollection|SplitDropdownButtonCollection $button
    ): static;

    /**
     * @param ArrayCollection<ButtonGroup|Button|DropDownButtonCollection|SplitDropdownButtonCollection> $buttons
     */
    public function addButtons(ArrayCollection $buttons): static;

    /**
     * @return ArrayCollection<ButtonGroup|Button|DropDownButtonCollection|SplitDropdownButtonCollection>
     */
    public function getButtons(): ArrayCollection;

    public function hasButtons(): bool;

    /**
     * @param ArrayCollection<ButtonGroup|Button|DropDownButtonCollection|SplitDropdownButtonCollection> $buttons
     */
    public function setButtons(ArrayCollection $buttons): static;
}
