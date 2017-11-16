<?php
namespace Chamilo\Libraries\Format\DataAction;

/**
 * @package Chamilo\Libraries\Format\DataAction
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop - hans.de.bisschop@ehb.be
 */
abstract class AbstractNamedAction extends AbstractAction
{
    /**
     * The title for the action
     *
     * @var string
     */
    protected $title;

    /**
     * Either the path or the css class for the image
     *
     * @var string
     */
    protected $fontAwesomeIconClass;

    /**
     * AbstractNamedAction constructor.
     *
     * @param string $name
     * @param string $title
     * @param string $fontAwesomeIconClass
     */
    public function __construct($name, $title, $fontAwesomeIconClass = null)
    {
        parent::__construct($name);

        $this->title = $title;
        $this->fontAwesomeIconClass = $fontAwesomeIconClass;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getFontAwesomeIconClass(): string
    {
        return $this->fontAwesomeIconClass;
    }

    /**
     * @param string $fontAwesomeIconClass
     */
    public function setFontAwesomeIconClass(string $fontAwesomeIconClass)
    {
        $this->fontAwesomeIconClass = $fontAwesomeIconClass;
    }

    /**
     * Converts this object's properties to an array
     *
     * @param array $baseArray
     *
     * @return array
     */
    public function toArray($baseArray = [])
    {
        $baseArray['title'] = $this->getTitle();
        $baseArray['image'] = $this->getFontAwesomeIconClass();

        return parent::toArray($baseArray);
    }
}