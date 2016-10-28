<?php
namespace Chamilo\Libraries\Format\Table\FormAction;

use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Platform\Translation;

/**
 * This class represents a table form action
 * Refactoring from ObjectTable to split between a table based on a record and based on an object
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TableFormAction
{

    /**
     * **************************************************************************************************************
     * Properties *
     * **************************************************************************************************************
     */

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
     * **************************************************************************************************************
     * Constructor *
     * **************************************************************************************************************
     */

    /**
     * Constructor
     *
     * @param string $action
     * @param string $title
     * @param bool $confirm
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
     * **************************************************************************************************************
     * Getters & Setters *
     * **************************************************************************************************************
     */

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
     * @return bool
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
     * @param bool $confirm
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
            $confirmation = $this->getConfirmationMessage() ? $this->getConfirmationMessage() : Translation :: get(
                'ConfirmYourSelectionAndAction',
                null,
                Utilities :: COMMON_LIBRARIES);
        }

        return $confirmation;
    }
}
