<?php
namespace Chamilo\Libraries\Format\Table\FormAction;

use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

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
     * @var boolean
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

    public function getConfirmation()
    {
        $confirmation = false;

        if ($this->get_confirm() == true)
        {
            $confirmation = $this->getConfirmationMessage() ?: Translation::get(
                'ConfirmYourSelectionAndAction', null, Utilities::COMMON_LIBRARIES
            );
        }

        return $confirmation;
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
     * Sets the action
     *
     * @param string $action
     */
    public function set_action($action)
    {
        $this->action = $action;
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
     * Sets the confirm flag
     *
     * @param boolean $confirm
     */
    public function set_confirm($confirm)
    {
        $this->confirm = $confirm;
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
     * Sets the title
     *
     * @param string $title
     */
    public function set_title($title)
    {
        $this->title = $title;
    }
}
