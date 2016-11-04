<?php
namespace Chamilo\Libraries\Format\Structure;

/**
 *
 * @package Chamilo\Libraries\Format\Structure
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class IdentRenderer
{
    const SIZE_XS = 'xs';
    const SIZE_SM = 'sm';
    const SIZE_MD = 'md';
    const SIZE_LG = 'lg';

    /**
     *
     * @var string
     */
    private $identifier;

    /**
     *
     * @var string
     */
    private $size;

    /**
     *
     * @var boolean
     */
    private $isNew;

    /**
     *
     * @var boolean
     */
    private $isDisabled;

    /**
     *
     * @param string $identifier
     * @param string $size
     * @param boolean $isNew
     * @param boolean $isDisabled
     */
    public function __construct($identifier, $isNew = false, $isDisabled = false, $size = self :: SIZE_MD)
    {
        $this->identifier = $identifier;
        $this->size = $size;
        $this->isNew = $isNew;
        $this->isDisabled = $isDisabled;
    }

    /**
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     *
     * @param string $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     *
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     *
     * @param string $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     *
     * @return boolean
     */
    public function getIsNew()
    {
        return $this->isNew;
    }

    /**
     *
     * @param boolean $isNew
     */
    public function setIsNew($isNew)
    {
        $this->isNew = $isNew;
    }

    /**
     *
     * @return boolean
     */
    public function getIsDisabled()
    {
        return $this->isDisabled;
    }

    /**
     *
     * @param boolean $isDisabled
     */
    public function setIsDisabled($isDisabled)
    {
        $this->isDisabled = $isDisabled;
    }

    /**
     *
     * @return string
     */
    public function render()
    {
        $classes = array();

        $classes[] = 'ident';
        $classes[] = 'ident-' . $this->getSize();

        if ($this->getIsNew())
        {
            $classes[] = 'ident-new';
        }

        if ($this->getIsDisabled())
        {
            $classes[] = 'ident-disabled';
        }

        $classes[] = 'ident-' . md5($this->getIdentifier());

        return '<span class="' . implode(' ', $classes) . '"></span>';
    }
}