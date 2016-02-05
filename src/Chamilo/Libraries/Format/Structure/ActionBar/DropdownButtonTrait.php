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
     * @var \Chamilo\Libraries\Format\Structure\ActionBar\SubButton[]
     */
    private $subButtons;

    /**
     *
     * @var string
     */
    private $dropdownClasses;

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\SubButton[]
     */
    public function getSubButtons()
    {
        return $this->subButtons;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\SubButton[] $subButtons
     */
    public function setSubButtons($subButtons)
    {
        $this->subButtons = $subButtons;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\SubButton $subButton
     */
    public function addSubButton(SubButton $subButton)
    {
        $this->subButtons[] = $subButton;
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
}
