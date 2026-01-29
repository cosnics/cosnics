<?php
namespace Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface ButtonDropDownInterface extends ButtonInterface
{
    /**
     * @return ArrayCollection<\Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\SubButton|\Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\SubButtonDivider|\Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\SubButtonHeader>
     */
    public function getDropDownButtons(): ArrayCollection;

    public function getDropDownClasses(): array;

    /**
     * @param ArrayCollection<\Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\SubButton|\Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\SubButtonDivider|\Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\SubButtonHeader> $dropDownButtons
     */
    public function setDropDownButtons(ArrayCollection $dropDownButtons): static;

    public function setDropDownClasses(array $classes): static;
}
