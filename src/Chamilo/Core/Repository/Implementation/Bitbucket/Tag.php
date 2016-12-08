<?php
namespace Chamilo\Core\Repository\Implementation\Bitbucket;

/**
 *
 * @author magali.gillard
 */
class Tag
{

    private $name;

    private $author;

    private $branch;

    private $time;

    private $revision;

    private $id_repository;

    public function get_name()
    {
        return $this->name;
    }

    public function set_name($name)
    {
        $this->name = $name;
    }

    public function get_author()
    {
        return $this->author;
    }

    public function set_author($author)
    {
        $this->author = $author;
    }

    public function get_time()
    {
        return $this->time;
    }

    public function set_time($time)
    {
        $this->time = $time;
    }

    public function get_branch()
    {
        return $this->branch;
    }

    public function set_branch($branch)
    {
        $this->branch = $branch;
    }

    public function get_id()
    {
        return $this->id;
    }

    public function set_id($id)
    {
        $this->id = $id;
    }

    public function get_repository()
    {
        return $this->repository;
    }

    public function set_repository($repository)
    {
        $this->repository = $repository;
    }

    public function get_download_link()
    {
        return sprintf(DataConnector::BASIC_DOWNLOAD_URL, $this->repository, $this->id);
    }
}
