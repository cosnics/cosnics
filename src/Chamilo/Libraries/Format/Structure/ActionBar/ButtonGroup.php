<?php
namespace Chamilo\Libraries\Format\Structure\ActionBar;

/**
 *
 * @package Chamilo\Libraries\Format\Structure\ActionBar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ButtonGroup extends AbstractButtonToolBarItem
{

    /**
     *
     * @var \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButton[]
     */
    private $buttons;

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButton[] $buttons
     * @param string[] $classes
     */
    public function __construct($buttons = array(), $classes = array())
    {
        parent :: __construct($classes);

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

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButton $button
     */
    public function prependButton(AbstractButton $button)
    {
        array_unshift($this->buttons, $button);
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButton[] $button
     */
    public function addButtons($buttons)
    {
        foreach ($buttons as $button)
        {
            $this->addButton($button);
        }
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButton[] $button
     */
    public function prependButtons($buttons)
    {
        foreach ($buttons as $button)
        {
            $this->prependButton($button);
        }
    }
}