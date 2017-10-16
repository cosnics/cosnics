<?php
namespace Chamilo\Libraries\Format\Table\FormAction;

use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Platform\Translation;

/**
 * This class represents a table form action
 * Refactoring from ObjectTable to split between a table based on a record and based on an object
 *
 * @package Chamilo\Libraries\Format\Table\FormAction
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TableFormAction
{

    /**
     * The action parameter
     *
     * @var string
     */
    private $action;

    /**
     * The title
     *
     * @var string
     */
    private $title;

    /**
     * Whether or not a confirm box is necessary
     *
     * @var bool
     */
    private $confirm;

    /**
     *
     * @var string
     */
    private $confirmationMessage;

    /**
     *
     * @param string $action
     * @param string $title
     * @param boolean $confirm
     * @param string $confirmationMessage
     */
    public function __construct($action, $title, $confirm = true, $confirmationMessage = null)
    {
        $this->action = $action;
        $this->title = $title;
        $this->confirm = $confirm;
        $this->confirmationMessage = $confirmationMessage;
    }

    /**
     * Returns the action
     *
     * @return string
     */
    public function get_action()
    {
        return $this->action;
    }

    /**
     * Returns the title
     *
     * @return string
     */
    public function get_title()
    {
        return $this->title;
    }

    /**
     * Returns the confirm flag
     *
     * @return boolean
     */
    public function get_confirm()
    {
        return $this->confirm;
    }

    /**
     * Sets the action
     *
     * @param string $action
     */
    public function set_action($action)
    {
        $this->action = $action;
    }

    /**
     * Sets the title
     *
     * @param string $title
     */
    public function set_title($title)
    {
        $this->title = $title;
    }

    /**
     * Sets the confirm flag
     *
     * @param boolean $confirm
     */
    public function set_confirm($confirm)
    {
        $this->confirm = $confirm;
    }

    /**
     *
     * @return string
     */
    public function getConfirmationMessage()
    {
        return $this->confirmationMessage;
    }

    /**
     *
     * @param string $confirmationMessage
     */
    public function setConfirmationMessage($confirmationMessage)
    {
        $this->confirmationMessage = $confirmationMessage;
    }

    public function getConfirmation()
    {
        $confirmation = false;

        if ($this->get_confirm() == true)
        {
            $confirmation = $this->getConfirmationMessage() ? $this->getConfirmationMessage() : Translation::get(
                'ConfirmYourSelectionAndAction',
                null,
                Utilities::COMMON_LIBRARIES);
        }

        return $confirmation;
    }
}
