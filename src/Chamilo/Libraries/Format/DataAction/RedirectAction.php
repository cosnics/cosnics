<?php

namespace Chamilo\Libraries\Format\DataAction;

/**
 * Describes an action with a label, an image and a link
 *
 * @package Chamilo\Libraries\Format\DataAction
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop - hans.de.bisschop@ehb.be
 */
class RedirectAction extends AbstractNamedAction
{
    const TYPE_NAME = 'redirect';

    /**
     * The url for the action
     *
     * @var string
     */
    protected $url;

    /**
     * Confirmation message. If empty there is no confirmation necessary
     *
     * @var string
     */
    protected $confirmationMessage;

    /**
     * Action constructor.
     *
     * @param string $name
     * @param string $title
     * @param string $url
     * @param string $fontAwesomeIconClass
     * @param string $confirmationMessage
     */
    public function __construct($name, $title, $url, $fontAwesomeIconClass = null, $confirmationMessage = null)
    {
        parent::__construct($name, $title, $fontAwesomeIconClass);

        $this->url = $url;
        $this->confirmationMessage = $confirmationMessage;
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
     * @param array $baseArray
     *
     * @return array
     */
    public function toArray($baseArray = [])
    {
        $baseArray['url'] = $this->getUrl();
        $baseArray['confirm'] = $this->needsConfirmation();
        $baseArray['confirmation_message'] = $this->getConfirmationMessage();

        return parent::toArray($baseArray);
    }
}