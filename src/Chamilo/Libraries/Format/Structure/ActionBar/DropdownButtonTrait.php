<?php
namespace Chamilo\Libraries\Format\Structure\ActionBar;

/**
 *
 * @package Chamilo\Libraries\Format\Structure\ActionBar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
trait DropdownButtonTrait
{

    /**
     *
     * @var \Chamilo\Libraries\Format\Structure\ActionBar\SubButtonInterface[]
     */
    private $subButtons;

    /**
     *
     * @var string
     */
    private $dropdownClasses;

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\SubButtonInterface $subButton
     */
    public function addSubButton(SubButtonInterface $subButton)
    {
        $this->subButtons[] = $subButton;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\SubButtonInterface[] $subButtons
     */
    public function addSubButtons($subButtons)
    {
        foreach ($subButtons as $subButton)
        {
            $this->addSubButton($subButton);
        }
    }

    /**
     *
     * @return string
     */
    public function getDropdownClasses()
    {
        return $this->dropdownClasses;
    }

    /**
     *
     * @param string $dropdownClasses
     */
    public function setDropdownClasses($dropdownClasses)
    {
        $this->dropdownClasses = $dropdownClasses;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\SubButtonInterface[]
     */
    public function getSubButtons()
    {
        return $this->subButtons;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\SubButtonInterface[] $subButtons
     */
    public function setSubButtons($subButtons)
    {
        $this->subButtons = $subButtons;
    }

    /**
     * Returns whether or not this dropdown button has sub buttons
     *
     * @return boolean
     */
    public function hasButtons()
    {
        return count($this->subButtons) > 0;
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
    public function prependSubButtons($subButtons)
    {
        foreach ($subButtons as $subButton)
        {
            $this->prependSubButton($subButton);
        }
    }
}
