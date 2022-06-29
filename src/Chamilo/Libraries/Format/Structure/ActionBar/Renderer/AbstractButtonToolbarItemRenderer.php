<?php
namespace Chamilo\Libraries\Format\Structure\ActionBar\Renderer;

use Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem;

/**
 *
 * @package Chamilo\Libraries\Format\Structure\ActionBar\Renderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class AbstractButtonToolbarItemRenderer
{

    private AbstractButtonToolBarItem $button;

    public function __construct(AbstractButtonToolBarItem $button)
    {
        $this->button = $button;
    }

    public abstract function render(): string;

    public function getButton()
    {
        return $this->button;
    }

    public function setButton(AbstractButtonToolBarItem $button)
    {
        $this->button = $button;
    }
}