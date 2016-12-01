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

    /**
     *
     * @var \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem
     */
    private $button;

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem $button
     */
    public function __construct(AbstractButtonToolBarItem $button)
    {
        $this->button = $button;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem
     */
    public function getButton()
    {
        return $this->button;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem $button
     */
    public function setButton(AbstractButtonToolBarItem $button)
    {
        $this->button = $button;
    }

    /**
     *
     * @return string
     */
    abstract function render();
}