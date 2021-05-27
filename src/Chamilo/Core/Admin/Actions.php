<?php
namespace Chamilo\Core\Admin;

class Actions
{

    private $context;

    private $links = [];

    private $search;

    public function __construct($context, $links = [], $search = null)
    {
        $this->context = $context;
        $this->links = $links;
        $this->search = $search;
    }

    public function set_context($context)
    {
        $this->context = $context;
    }

    public function get_context()
    {
        return $this->context;
    }

    public function set_links(array $links)
    {
        $this->links = $links;
    }

    public function get_links()
    {
        return $this->links;
    }

    public function set_search($search)
    {
        $this->search = $search;
    }

    public function get_search()
    {
        return $this->search;
    }

    public function add_link($link)
    {
        $this->links[] = $link;
    }
}
