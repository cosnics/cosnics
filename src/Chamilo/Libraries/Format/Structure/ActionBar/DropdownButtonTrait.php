<?php
namespace Chamilo\Libraries\Format\Structure\ActionBar;

/**
 * @package Chamilo\Libraries\Format\Structure\ActionBar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
trait DropdownButtonTrait
{

    /**
     * @var string[]
     */
    private array $dropdownClasses;

    /**
     * @var \Chamilo\Libraries\Format\Structure\ActionBar\SubButtonInterface[]
     */
    private array $subButtons;

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\SubButtonInterface $subButton
     */
    public function addSubButton(SubButtonInterface $subButton)
    {
        $this->subButtons[] = $subButton;
    }

    /**
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\SubButtonInterface[] $subButtons
     */
    public function addSubButtons(array $subButtons)
    {
        foreach ($subButtons as $subButton)
        {
            $this->addSubButton($subButton);
        }
    }

    /**
     *
     * @return string[]
     */
    public function getDropdownClasses(): array
    {
        return $this->dropdownClasses;
    }

    /**
     *
     * @param string[] $dropdownClasses
     */
    public function setDropdownClasses(array $dropdownClasses)
    {
        $this->dropdownClasses = $dropdownClasses;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\SubButtonInterface[]
     */
    public function getSubButtons(): array
    {
        return $this->subButtons;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\SubButtonInterface[] $subButtons
     */
    public function setSubButtons(array $subButtons)
    {
        $this->subButtons = $subButtons;
    }

    public function hasButtons(): bool
    {
        return count($this->subButtons) > 0;
    }

    /**
     * Initialize method as replacement for constructor due to PHP issue
     * https://bugs.php.net/bug.php?id=65576
     * TODO: fix this once everyone moves to PHP 5.6
     */
    public function initializeDropdownButton(array $dropdownClasses = [], array $subButtons = [])
    {
        $this->setDropdownClasses($dropdownClasses);
        $this->setSubButtons($subButtons);
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\SubButtonInterface $subButton
     */
    public function prependSubButton(SubButtonInterface $subButton)
    {
        array_unshift($this->subButtons, $subButton);
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\SubButtonInterface[] $subButtons
     */
    public function prependSubButtons(array $subButtons)
    {
        foreach ($subButtons as $subButton)
        {
            $this->prependSubButton($subButton);
        }
    }
}
