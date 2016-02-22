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
     * @var multitype:\configuration\package\Author
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
     * @return multitype:\common\package\libraries\Author
     */
    public function get_authors()
    {
        return $this->authors;
    }

    /**
     *
     * @param multitype:\configuration\package\Author $authors
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
