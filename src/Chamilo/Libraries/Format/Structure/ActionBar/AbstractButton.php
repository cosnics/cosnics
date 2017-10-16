<?php
namespace Chamilo\Libraries\Format\Structure\ActionBar;

/**
 *
 * @package Chamilo\Libraries\Format\Structure\ActionBar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class AbstractButton extends AbstractButtonToolBarItem
{
    const DISPLAY_ICON = 1;
    const DISPLAY_LABEL = 2;
    const DISPLAY_ICON_AND_LABEL = 3;

    /**
     *
     * @var string
     */
    private $label;

    /**
     *
     * @var int
     */
    private $display;

    /**
     *
     * @var string
     */
    private $imagePath;

    /**
     *
     * @param string $label
     * @param string $imagePath
     * @param integer $display
     * @param string $classes
     */
    public function __construct($label = null, $imagePath = null, $display = self :: DISPLAY_ICON_AND_LABEL, $classes = null)
    {
        parent::__construct($classes);

        $this->label = $label;
        $this->display = $display;
        $this->imagePath = $imagePath;
    }

    /**
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     *
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     *
     * @return integer
     */
    public function getDisplay()
    {
        return $this->display;
    }

    /**
     *
     * @param integer $display
     */
    public function setDisplay($display)
    {
        $this->display = $display;
    }

    /**
     *
     * @return string
     */
    public function getImagePath()
    {
        return $this->imagePath;
    }

    /**
     *
     * @param string $imagePath
     */
    public function setImagePath($imagePath)
    {
        $this->imagePath = $imagePath;
    }
}