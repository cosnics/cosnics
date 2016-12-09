<?php
namespace Chamilo\Libraries\Format\Structure\ActionBar;

/**
 *
 * @package Chamilo\Libraries\Format\Structure\ActionBar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SubButtonHeader extends AbstractButtonToolBarItem implements SubButtonInterface
{

    /**
     *
     * @var string
     */
    private $label;

    /**
     *
     * @param string $label
     * @param string $classes
     */
    public function __construct($label, $classes = null)
    {
        parent::__construct($classes);
        
        $this->label = $label;
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
}