<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Domain;

/**
 * Describes an action with a label, an image and a link
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Action
{
    /**
     * A name for the action, used as identifier
     *
     * @var string
     */
    protected $name;

    /**
     * The url for the action
     *
     * @var string
     */
    protected $url;

    /**
     * The title for the action
     *
     * @var string
     */
    protected $title;

    /**
     * Either the path or the css class for the image
     *
     * @var string
     */
    protected $image;

    /**
     * Confirmation message. If empty there is no confirmation necessary
     *
     * @var string
     */
    protected $confirmationMessage;

    /**
     * Action constructor.
     *
     * @param $name
     * @param string $title
     * @param string $url
     * @param string $image
     * @param string $confirmationMessage
     */
    public function __construct($name, $title, $url, $image = null, $confirmationMessage = null)
    {
        $this->name = $name;
        $this->url = $url;
        $this->title = $title;
        $this->image = $image;
        $this->confirmationMessage = $confirmationMessage;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url)
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getImage(): string
    {
        return $this->image;
    }

    /**
     * @param string $image
     */
    public function setImage(string $image)
    {
        $this->image = $image;
    }

    /**
     * @return string
     */
    public function getConfirmationMessage()
    {
        return $this->confirmationMessage;
    }

    /**
     * @param string $confirmationMessage
     */
    public function setConfirmationMessage(string $confirmationMessage)
    {
        $this->confirmationMessage = $confirmationMessage;
    }

    /**
     * Checks whether or not the action needs confirmation
     *
     * @return bool
     */
    public function needsConfirmation()
    {
        return !empty($this->getConfirmationMessage());
    }

    /**
     * Converts this object's properties to an array
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'name' => $this->getName(),
            'title' => $this->getTitle(),
            'url' => $this->getUrl(),
            'image' => $this->getImage(),
            'confirm' => $this->needsConfirmation(),
            'confirmation_message' => $this->getConfirmationMessage()
        );
    }
}