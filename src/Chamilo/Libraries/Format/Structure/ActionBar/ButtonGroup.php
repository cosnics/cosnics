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
     * @var \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButton[]
     */
    private array $buttons;

    /**
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButton[] $buttons
     * @param string[] $classes
     */
    public function __construct($buttons = [], array $classes = [])
    {
        parent::__construct($classes);

        $this->buttons = $buttons;
    }

    public function addButton(AbstractButton $button)
    {
        $this->buttons[] = $button;
    }

    /**
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButton[] $buttons
     */
    public function addButtons(array $buttons)
    {
        foreach ($buttons as $button)
        {
            $this->addButton($button);
        }
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButton[]
     */
    public function getButtons(): array
    {
        return $this->buttons;
    }

    /**
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButton[] $buttons
     */
    public function setButtons(array $buttons)
    {
        $this->buttons = $buttons;
    }

    public function hasButtons(): bool
    {
        return count($this->buttons) > 0;
    }

    public function prependButton(AbstractButton $button)
    {
        array_unshift($this->buttons, $button);
    }

    /**
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButton[] $buttons
     */
    public function prependButtons(array $buttons)
    {
        foreach ($buttons as $button)
        {
            $this->prependButton($button);
        }
    }
}