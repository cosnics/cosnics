<?php
namespace Chamilo\Libraries\Calendar\Event;

use Chamilo\Libraries\Architecture\Interfaces\Hashable;

/**
 *
 * @package Chamilo\Libraries\Calendar\Event
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class EventSource implements Hashable
{
    use \Chamilo\Libraries\Architecture\Traits\HashableTrait;

    /**
     *
     * @var string
     */
    private $id;

    /**
     *
     * @var string
     */
    private $context;

    /**
     *
     * @var string
     */
    private $title;

    /**
     *
     * @var string
     */
    private $content;

    /**
     *
     * @var string
     */
    private $url;

    /**
     *
     * @param string $id
     * @param string $context
     * @param string $title
     * @param string $content
     * @param string $url
     */
    public function __construct(Hashable $id = null, $context = null, $title = null, $content = null, $url = null)
    {
        $this->id = $id;
        $this->context = $context;
        $this->title = $title;
        $this->content = $content;
        $this->url = $url;
    }

    /**
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     *
     * @param string $context
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    /**
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     *
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     *
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Interfaces\Hashable::getHashParts()
     */
    public function getHashParts()
    {
        return [$this->getId(), $this->getContext(), $this->getTitle(), $this->getContent(), $this->getUrl()];
    }
}

