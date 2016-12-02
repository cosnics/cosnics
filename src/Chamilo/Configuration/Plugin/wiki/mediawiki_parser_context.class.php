<?php
use Chamilo\Core\Repository\ContentObject\Wiki\Storage\DataClass\Wiki;
class MediawikiParserContext
{

    /**
     * The Wiki object which should be used as a reference to resolve a.o.
     * links
     * 
     * @var Wiki
     */
    private $wiki;

    /**
     * The title of the wiki element to be parsed
     * 
     * @var string
     */
    private $title;

    /**
     * The main body of text to be parsed
     * 
     * @var string
     */
    private $body;

    /**
     * Parameters that should be added to the wiki links by default
     * 
     * @var array
     */
    private $parameters;

    /**
     * Construct a new MediawikiParserContext
     * 
     * @param Wiki $wiki
     * @param string $title
     * @param string $body
     * @param array $parameters
     */
    function __construct(Wiki $wiki, $title, $body, $parameters)
    {
        $this->wiki = $wiki;
        $this->title = $title;
        $this->body = $body;
        $this->parameters = $parameters;
    }

    /**
     *
     * @return the $wiki
     */
    public function get_wiki()
    {
        return $this->wiki;
    }

    /**
     *
     * @param Wiki $wiki
     */
    public function set_wiki($wiki)
    {
        $this->wiki = $wiki;
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
     * @param string $title
     */
    public function set_title($title)
    {
        $this->title = $title;
    }

    /**
     *
     * @return the $body
     */
    public function get_body()
    {
        return $this->body;
    }

    /**
     *
     * @param string $body
     */
    public function set_body($body)
    {
        $this->body = $body;
    }

    /**
     *
     * @return the $parameters
     */
    public function get_parameters()
    {
        return $this->parameters;
    }

    /**
     *
     * @param array $parameters
     */
    public function set_parameters($parameters)
    {
        $this->parameters = $parameters;
    }
}
?>