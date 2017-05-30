<?php
namespace Chamilo\Core\Repository\Implementation\Matterhorn;

class InboxFile
{

    private $path;

    private $url_parts;

    private $path_info;

    /**
     *
     * @return the $mimetype
     */
    /**
     *
     * @return the $id
     */
    public function get_id()
    {
        $url_parts = $this->get_path_info();
        return $url_parts['basename'];
    }

    public function get_path_info()
    {
        if (! isset($this->path_info))
        {
            $url_parts = $this->get_url_parts();
            $this->path_info = pathinfo($url_parts['path']);
        }
        return $this->path_info;
    }

    /**
     *
     * @return the $ref
     */
    public function get_path()
    {
        return $this->path;
    }

    /**
     *
     * @param $ref the $ref to set
     */
    public function set_path($path)
    {
        $this->path = $path;
    }

    public function get_url_parts()
    {
        if (! isset($this->url_parts))
        {
            $this->url_parts = parse_url($this->get_path());
        }
        return $this->url_parts;
    }
}
