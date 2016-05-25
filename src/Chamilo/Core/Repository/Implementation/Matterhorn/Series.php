<?php
namespace Chamilo\Core\Repository\Implementation\Matterhorn;

class Series
{

    private $id;

    private $description;

    private $contributor;

    private $license;

    private $creator;

    private $language;

    private $subject;

    private $title;
    const PROPERTY_TITLE = 'title';

    /**
     *
     * @return the $id
     */
    /**
     *
     * @return the $contributor
     */
    public function get_contributor()
    {
        return $this->contributor;
    }

    /**
     *
     * @return the $license
     */
    public function get_license()
    {
        return $this->license;
    }

    /**
     *
     * @return the $creator
     */
    public function get_creator()
    {
        return $this->creator;
    }

    /**
     *
     * @return the $language
     */
    public function get_language()
    {
        return $this->language;
    }

    /**
     *
     * @return the $subject
     */
    public function get_subject()
    {
        return $this->subject;
    }

    /**
     *
     * @return the $title
     */
    public function get_title()
    {
        return $this->title;
    }

    /**
     *
     * @param $contributor the $contributor to set
     */
    public function set_contributor($contributor)
    {
        $this->contributor = $contributor;
    }

    /**
     *
     * @param $license the $license to set
     */
    public function set_license($license)
    {
        $this->license = $license;
    }

    /**
     *
     * @param $creator the $creator to set
     */
    public function set_creator($creator)
    {
        $this->creator = $creator;
    }

    /**
     *
     * @param $language the $language to set
     */
    public function set_language($language)
    {
        $this->language = $language;
    }

    /**
     *
     * @param $subject the $subject to set
     */
    public function set_subject($subject)
    {
        $this->subject = $subject;
    }

    /**
     *
     * @param $title the $title to set
     */
    public function set_title($title)
    {
        $this->title = $title;
    }

    public function get_id()
    {
        return $this->id;
    }

    /**
     *
     * @param $id the $id to set
     */
    public function set_id($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @return the $description
     */
    public function get_description()
    {
        return $this->description;
    }

    /**
     *
     * @param $description the $description to set
     */
    public function set_description($description)
    {
        $this->description = $description;
    }
}
