<?php
namespace Chamilo\Core\Install;

/**
 * Class that holds the result from a package installation.
 * Keeps track of the success status, the installation messages
 * and the context of the installer
 * 
 * @author Phillipe
 * @author Sven Vanpoucke - Hogeschool Gent - added context
 */
class StepResult
{

    /**
     *
     * @var boolean has the step succeeded
     */
    private $success;

    /**
     *
     * @var array messages produced during installation step
     */
    private $messages;

    /**
     * The context of the installer.
     * 
     * @var String
     */
    private $context;

    public function __construct($success = false, $messages = null, $context = null)
    {
        if (is_null($messages))
        {
            $messages = [];
        }
        if (! is_array($messages))
        {
            $messages = array($messages);
        }
        
        $this->success = $success;
        $this->messages = $messages;
        $this->context = $context;
    }

    public function get_success()
    {
        return $this->success;
    }

    public function get_messages()
    {
        return $this->messages;
    }

    public function get_context()
    {
        return $this->context;
    }

    public function __toString()
    {
        return "InstallerTestResult : Success? : {$this->success}";
    }
}
