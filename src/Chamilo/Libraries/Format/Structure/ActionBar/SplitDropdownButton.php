<?php
namespace Chamilo\Libraries\Format\Structure\ActionBar;

/**
 *
 * @package Chamilo\Libraries\Format\Structure\ActionBar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SplitDropdownButton extends AbstractButton
{
    use \Chamilo\Libraries\Format\Structure\ActionBar\DropdownButtonTrait;
    use \Chamilo\Libraries\Format\Structure\ActionBar\ActionButtonTrait;

    /**
     *
     * @param string $label
     * @param string $imagePath
     * @param string $action
     * @param int|null|string $display
     * @param bool|string $confirmation
     * @param string $classes
     * @param string $target TODO: Move this to trait once everyone moves to PHP 5.6. Currently not working in trait due
     *            to bug
     *        https://bugs.php.net/bug.php?id=65576
     */
    public function __construct($label = null, $imagePath = null, $action = null, $display = self :: DISPLAY_ICON_AND_LABEL, $confirmation = false, $classes = null, 
        $target = null)
    {
        parent::__construct($label, $imagePath, $display, $classes);
        $this->initialize($action, $confirmation, $target);
    }
}