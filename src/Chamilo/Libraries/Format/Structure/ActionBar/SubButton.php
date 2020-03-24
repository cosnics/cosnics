<?php
namespace Chamilo\Libraries\Format\Structure\ActionBar;

/**
 *
 * @package Chamilo\Libraries\Format\Structure\ActionBar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SubButton extends Button implements SubButtonInterface
{
    /**
     * @var boolean
     */
    private $isActive;

    /**
     * SubButton constructor.
     *
     * @param string $label
     * @param \Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph $imagePath
     * @param string $action
     * @param integer $display
     * @param boolean $confirmation
     * @param string[] $classes
     * @param string $target
     * @param boolean $isActive
     */
    public function __construct(
        $label = null, $imagePath = null, $action = null, $display = self::DISPLAY_ICON_AND_LABEL,
        $confirmation = false, $classes = null, $target = null, $isActive = false
    )
    {
        parent::__construct($label, $imagePath, $action, $display, $confirmation, $classes, $target);
        $this->isActive = $isActive;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     */
    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

}