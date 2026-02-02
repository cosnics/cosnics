<?php
namespace Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface;

use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\SubButton;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\SubButtonDivider;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\SubButtonHeader;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface ButtonDropDownCollectionInterface extends ButtonInterface
{
    public function addButton(SubButton|SubButtonDivider|SubButtonHeader $dropDownButton): static;

    /**
     * @param ArrayCollection<\Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\SubButton|\Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\SubButtonDivider|\Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\SubButtonHeader> $dropDownButtons
     */
    public function addButtons(ArrayCollection $dropDownButtons): static;

    /**
     * @return ArrayCollection<\Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\SubButton|\Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\SubButtonDivider|\Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\SubButtonHeader>
     */
    public function getButtons(): ArrayCollection;

    public function getDropDownClasses(): array;

    public function hasButtons(): bool;

    /**
     * @param ArrayCollection<\Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\SubButton|\Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\SubButtonDivider|\Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\SubButtonHeader> $dropDownButtons
     */
    public function setButtons(ArrayCollection $dropDownButtons): static;

    public function setDropDownClasses(array $classes): static;
}
