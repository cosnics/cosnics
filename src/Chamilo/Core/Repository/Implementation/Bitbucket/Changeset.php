<?php
namespace Chamilo\Core\Repository\Implementation\Bitbucket;

/**
 *
 * @author magali.gillard
 */
class Changeset
{

    private $author;

    private $time;

    private $message;

    private $branch;

    private $revision;

    private $id;

    private $repository;

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

    public function get_message()
    {
        return $this->message;
    }

    public function set_message($message)
    {
        $this->message = $message;
    }

    public function get_branch()
    {
        return $this->branch;
    }

    public function set_branch($branch)
    {
        $this->branch = $branch;
    }

    public function get_revision()
    {
        return $this->revision;
    }

    public function set_revision($revision)
    {
        $this->revision = $revision;
    }

    public function get_repository()
    {
        return $this->repository;
    }

    public function set_repository($repository)
    {
        $this->repository = $repository;
    }

    public function get_id()
    {
        return $this->id;
    }

    public function set_id($id)
    {
        $this->id = $id;
    }

    public function get_download_link()
    {
        return sprintf(DataConnector :: BASIC_DOWNLOAD_URL, $this->repository, $this->id);
    }
}
