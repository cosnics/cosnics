<?php
namespace Chamilo\Libraries\Format\Structure\ActionBar;

/**
 *
 * @package Chamilo\Libraries\Format\Structure\ActionBar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class AbstractButtonToolBarItem
{

    /**
     * @var string[]
     */
    private array $classes;

    /**
     * @param string[] $classes
     */
    public function __construct(array $classes = [])
    {
        $this->classes = $classes;
    }

    /**
     * @return string[]
     */
    public function getClasses(): array
    {
        return $this->classes;
    }

    /**
     * @param string[] $classes
     */
    public function setClasses(array $classes)
    {
        $this->classes = $classes;
    }
}