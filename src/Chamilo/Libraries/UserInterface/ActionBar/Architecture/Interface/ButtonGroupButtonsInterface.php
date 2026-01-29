<?php
namespace Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface ButtonGroupButtonsInterface extends ButtonInterface
{
    /**
     * @return ArrayCollection<\Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\Button|\Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\DropDownButton|\Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\SplitDropdownButton>
     */
    public function getGroupButtons(): ArrayCollection;

    /**
     * @param ArrayCollection<\Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\Button|\Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\DropDownButton|\Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\SplitDropdownButton> $groupButtons
     */
    public function setGroupButtons(ArrayCollection $groupButtons): static;
}
