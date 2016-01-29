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
     * @var \Chamilo\Libraries\Format\Structure\ActionBar\Button[]
     */
    private $buttons;

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\Button[]
     */
    public function getButtons()
    {
        return $this->buttons;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\Button[] $buttons
     */
    public function setButtons($buttons)
    {
        $this->buttons = $buttons;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\Button $button
     */
    public function addButton(Button $button)
    {
        $this->buttons[] = $button;
    }
}
