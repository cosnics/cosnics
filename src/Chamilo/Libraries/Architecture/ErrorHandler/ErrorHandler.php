<?php
namespace Chamilo\Libraries\Architecture\ErrorHandler;

use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

/**
 * Manages the error handler, the exception handler and the shutdown function
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ErrorHandler
{

    /**
     * The Exception Logger
     * 
     * @var \Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface
     */
    protected $exceptionLogger;

    /**
     *
     * @var \Chamilo\Libraries\Platform\Translation
     */
    protected $translator;

    /**
     *
     * @var \Chamilo\Libraries\Format\Theme
     */
    protected $themeUtilities;

    /**
     * ErrorHandlerManager constructor.
     * 
     * @param \Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface $exceptionLogger
     * @param \Chamilo\Libraries\Platform\Translation $translator
     * @param \Chamilo\Libraries\Format\Theme $themeUtilities
     */
    public function __construct(ExceptionLoggerInterface $exceptionLogger, Translation $translator, 
        Theme $themeUtilities)
    {
        $this->exceptionLogger = $exceptionLogger;
        $this->translator = $translator;
        $this->themeUtilities = $themeUtilities;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface
     */
    public function getExceptionLogger()
    {
        return $this->exceptionLogger;
    }

    /**
     *
     * @param \Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface $exceptionLogger
     */
    public function setExceptionLogger(ExceptionLoggerInterface $exceptionLogger)
    {
        $this->exceptionLogger = $exceptionLogger;
    }

    /**
     *
     * @return \Chamilo\Libraries\Platform\Translation
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     *
     * @param \Chamilo\Libraries\Platform\Translation $translator
     */
    public function setTranslator(Translation $translator)
    {
        $this->translator = $translator;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Theme
     */
    public function getThemeUtilities()
    {
        return $this->themeUtilities;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Theme $themeUtilities
     */
    public function setThemeUtilities(Theme $themeUtilities)
    {
        $this->themeUtilities = $themeUtilities;
    }

    /**
     * General shutdown handler for fatal errors in PHP
     */
    public function handleShutdown()
    {
        $error = error_get_last();
        
        if (! is_null($error) && $error['type'] == E_ERROR)
        {
            $this->getExceptionLogger()->logException(
                new \Exception($error['message'] . '. File: ' . $error['file'] . '. Line: ' . $error['line'] . '.'),
                ExceptionLoggerInterface::EXCEPTION_LEVEL_FATAL_ERROR, 
                $error['file'], 
                $error['line']);
            $this->displayGeneralErrorPage();
        }
    }

    /**
     * General error handler for (catchable) errors in PHP
     * 
     * @param int $errorNumber
     * @param string $errorString
     * @param string $file
     * @param int $line
     *
     * @return bool
     */
    public function handleError($errorNumber, $errorString, $file, $line)
    {
        $exceptionTypes = array(
            E_USER_ERROR => ExceptionLoggerInterface::EXCEPTION_LEVEL_ERROR, 
            E_USER_WARNING => ExceptionLoggerInterface::EXCEPTION_LEVEL_WARNING, 
            E_USER_NOTICE => ExceptionLoggerInterface::EXCEPTION_LEVEL_WARNING, 
            E_RECOVERABLE_ERROR => ExceptionLoggerInterface::EXCEPTION_LEVEL_ERROR);

        if (! array_key_exists($errorNumber, $exceptionTypes))
        {
            return true;
        }

        $exceptionLevel = $exceptionTypes[$errorNumber];
        
        $this->getExceptionLogger()->logException(new \Exception($errorString), $exceptionLevel, $file, $line);
        
        return true;
    }

    /**
     * General exception handler for exceptions in PHP
     * 
     * @param \Exception $exception
     */
    public function handleException($exception)
    {
        $this->getExceptionLogger()->logException($exception, ExceptionLoggerInterface::EXCEPTION_LEVEL_FATAL_ERROR);
        $this->displayGeneralErrorPage();
    }

    /**
     * Registers the error handler, the exception handler and the shutdown function
     */
    public function registerErrorHandlers()
    {
        set_exception_handler(array($this, 'handleException'));
        set_error_handler(array($this, 'handleError'));
        
        register_shutdown_function(array($this, 'handleShutdown'));
    }

    /**
     * Displays a general error page
     */
    protected function displayGeneralErrorPage()
    {
        $path = $this->getThemeUtilities()->getTemplatePath('Chamilo\Configuration', false) . 'Error.html.tpl';
        
        $template = file_get_contents($path);
        
        $variables = array(
            'error_code' => 500, 
            'error_title' => $this->getTranslation('FatalErrorTitle'), 
            'error_content' => $this->getTranslation('FatalErrorContent'), 
            'return_button_content' => $this->getTranslation('ReturnToPreviousPage'));
        
        foreach ($variables as $variable => $value)
        {
            $template = str_replace('{ ' . $variable . ' }', $value, $template);
        }
        
        echo $template;
    }

    /**
     * Helper function for translations
     * 
     * @param string $variable
     * @param array $parameters
     * @param string $context
     *
     * @return string
     */
    protected function getTranslation($variable, $parameters = array(), $context = 'Chamilo\Configuration')
    {
        return $this->getTranslator()->getTranslation($variable, $parameters, $context);
    }
}