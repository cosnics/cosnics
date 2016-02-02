<?php
namespace Chamilo\Libraries\Format\Structure\ActionBar;

/**
 *
 * @package Chamilo\Libraries\Format\Structure\ActionBar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ButtonGroup
{

    /**
     *
     * @var \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButton[]
     */
    private $buttons;

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButton[] $buttons
     */
    public function __construct($buttons = array())
    {
        $this->buttons = $buttons;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButton[]
     */
    public function getButtons()
    {
        return $this->buttons;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButton[] $buttons
     */
    public function setButtons($buttons)
    {
        $this->buttons = $buttons;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButton $button
     */
    public function addButton(AbstractButton $button)
    {
        $this->buttons[] = $button;
    }
}