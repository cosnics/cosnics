<?php
namespace Chamilo\Libraries\UserInterface\ActionBar\Architecture\Trait;

use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\SubButton;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\SubButtonDivider;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\SubButtonHeader;
use Doctrine\Common\Collections\ArrayCollection;

trait ButtonDropDownTrait
{
    /**
     * @var ArrayCollection<\Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\SubButton|\Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\SubButtonDivider|\Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\SubButtonHeader>
     */
    private ArrayCollection $dropDownButtons;

    /**
     * @var string[]
     */
    private array $dropDownClasses = [];

    public function addDropDownButton(SubButton|SubButtonDivider|SubButtonHeader $dropDownButton): static
    {
        $this->getDropDownButtons()->add($dropDownButton);

        return $this;
    }

    /**
     * @param ArrayCollection<\Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\SubButton|\Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\SubButtonDivider|\Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\SubButtonHeader> $dropDownButtons
     */
    public function addDropDownButtons(ArrayCollection $dropDownButtons): static
    {
        foreach ($dropDownButtons as $dropDownButton)
        {
            $this->addDropDownButton($dropDownButton);
        }

        return $this;
    }

    /**
     * @return ArrayCollection<\Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\SubButton|\Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\SubButtonDivider|\Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\SubButtonHeader>
     */
    public function getDropDownButtons(): ArrayCollection
    {
        return $this->dropDownButtons;
    }

    /**
     * @param ArrayCollection<\Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\SubButton|\Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\SubButtonDivider|\Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\SubButtonHeader> $dropDownButtons
     */
    public function setDropDownButtons(ArrayCollection $dropDownButtons): static
    {
        $this->dropDownButtons = $dropDownButtons;

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

    public function hasDropDownButtons(): bool
    {
        return !$this->getDropDownButtons()->isEmpty();
    }

}
