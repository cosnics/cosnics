<?php
namespace Chamilo\Core\Repository\Implementation\Matterhorn\DublinCore;

/**
 *
 * @package core\repository\implementation\matterhorn
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class DublinCore
{

    /**
     *
     * @var string
     */
    private $identifier;

    /**
     *
     * @var string
     */
    private $title;

    /**
     *
     * @var string
     */
    private $creator;

    /**
     *
     * @var string
     */
    private $contributor;

    /**
     *
     * @var string
     */
    private $description;

    /**
     *
     * @var string
     */
    private $subject;

    /**
     *
     * @var string
     */
    private $license;

    /**
     *
     * @return string
     */
    public function get_identifier()
    {
        return $this->identifier;
    }

    /**
     *
     * @param string $identifier
     */
    public function set_identifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     *
     * @return string
     */
    public function get_title()
    {
        return $this->title;
    }

    /**
     *
     * @param string $title
     */
    public function set_title($title)
    {
        $this->title = $title;
    }

    /**
     *
     * @return string
     */
    public function get_creator()
    {
        return $this->creator;
    }

    /**
     *
     * @param string $creator
     */
    public function set_creator($creator)
    {
        $this->creator = $creator;
    }

    /**
     *
     * @return string
     */
    public function get_contributor()
    {
        return $this->contributor;
    }

    /**
     *
     * @param string $contributor
     */
    public function set_contributor($contributor)
    {
        $this->contributor = $contributor;
    }

    /**
     *
     * @return string
     */
    public function get_description()
    {
        return $this->description;
    }

    /**
     *
     * @param string $description
     */
    public function set_description($description)
    {
        $this->description = $description;
    }

    /**
     *
     * @return string
     */
    public function get_subject()
    {
        return $this->subject;
    }

    /**
     *
     * @param string $subject
     */
    public function set_subject($subject)
    {
        $this->subject = $subject;
    }

    /**
     *
     * @return string
     */
    public function get_license()
    {
        return $this->license;
    }

    /**
     *
     * @param string $license
     */
    public function set_license($license)
    {
        $this->license = $license;
    }

    /**
     *
     * @param string $title
     * @param string $creator
     * @param string $contributor
     * @param string $description
     * @param string $subject
     * @param string $license
     */
    public function __construct($identifier, $title = null, $creator = null, $contributor = null, $description = null, $subject = null, 
        $license = null)
    {
        $this->identifier = $identifier;
        $this->title = $title;
        $this->creator = $creator;
        $this->contributor = $contributor;
        $this->description = $description;
        $this->subject = $subject;
        $this->license = $license;
    }

    /**
     *
     * @return \DOMDocument
     */
    abstract public function as_dom_document();

    /**
     *
     * @return string
     */
    public function as_string()
    {
        return $this->as_dom_document()->saveXML();
    }
}
