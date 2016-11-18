<?php
namespace Chamilo\Core\Repository\Implementation\Matterhorn;

use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Utilities\Utilities;

class Metadata
{

    private $type;

    private $id;

    private $mimetype;

    private $ref;

    private $url;

    private $tags;

    /**
     *
     * @return the $ref
     */
    /**
     *
     * @return the $ref
     */
    public function get_ref()
    {
        return $this->ref;
    }

    public function get_ref_type()
    {
        $ref = explode(':', $this->get_ref());
        return $ref[0];
    }

    public function get_ref_id()
    {
        $ref = explode(':', $this->get_ref());
        return $ref[1];
    }

    /**
     *
     * @return the $tags
     */
    public function get_tags()
    {
        return $this->tags;
    }

    /**
     *
     * @param $ref the $ref to set
     */
    public function set_ref($ref)
    {
        $this->ref = $ref;
    }

    /**
     *
     * @param $tags the $tags to set
     */
    public function set_tags($tags)
    {
        $this->tags = $tags;
    }

    /**
     *
     * @return the $type
     */
    public function get_type()
    {
        return $this->type;
    }

    /**
     *
     * @return the $id
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     *
     * @return the $mimetype
     */
    public function get_mimetype()
    {
        return $this->mimetype;
    }

    /**
     *
     * @return the $url
     */
    public function get_url()
    {
        return $this->url;
    }

    /**
     *
     * @param $type the $type to set
     */
    public function set_type($type)
    {
        $this->type = $type;
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
     * @param $mimetype the $mimetype to set
     */
    public function set_mimetype($mimetype)
    {
        $this->mimetype = $mimetype;
    }

    /**
     *
     * @param $url the $url to set
     */
    public function set_url($url)
    {
        $this->url = $url;
    }

    public function get_type_as_image()
    {
        $result = str_replace('/', '_', $this->get_type());
        $result = str_replace('+', '_', $result);
        $image_path = Theme::getInstance()->getImagePath(__NAMESPACE__, 'Metadata/' . $result);
        return '<img src="' . $image_path . '" title="' . $this->get_type() . '"/>';
    }

    public function as_string()
    {
        $html = array();
        
        $html[] = Utilities::mimetype_to_image($this->get_mimetype());
        $html[] = $this->get_type_as_image();
        return implode(" ", $html);
    }
}
