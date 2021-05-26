<?php
namespace Chamilo\Configuration\Package\Properties\Authors;

/**
 *
 * @author Hans De Bisschop
 * @package core.lynx
 */
class Authors
{

    /**
     *
     * @var \Chamilo\Configuration\Package\Properties\Authors\Author[]
     */
    private $authors;

    /**
     *
     * @param \configuration\package\Author $authors
     */
    public function __construct($authors)
    {
        $this->set_authors($authors);
    }

    /**
     *
     * @return \Chamilo\Configuration\Package\Properties\Authors\Author[]
     */
    public function get_authors()
    {
        return $this->authors;
    }

    /**
     *
     * @param \Chamilo\Configuration\Package\Properties\Authors\Author[] $authors
     */
    public function set_authors($authors)
    {
        $this->authors = $authors;
    }

    /**
     *
     * @param \configuration\package\Author $author
     */
    public function add_author($author)
    {
        $this->authors[] = $author;
    }
}
