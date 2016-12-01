<?php
namespace Chamilo\Core\Repository\Common\Import;

use Symfony\Component\HttpFoundation\Request;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;

/**
 *
 * @package Chamilo\Core\Repository\Common\Import
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class FormProcessorFactory
{

    /**
     *
     * @var \Chamilo\Core\Repository\Common\Import\FormProcessorFactory
     */
    private static $instance;

    /**
     *
     * @var \Chamilo\Libraries\Utilities\StringUtilities
     */
    private $stringUtilities;

    public function __construct(StringUtilities $stringUtilities)
    {
        $this->stringUtilities = $stringUtilities;
    }

    /**
     *
     * @return \Chamilo\Libraries\Utilities\StringUtilities
     */
    public function getStringUtilities()
    {
        return $this->stringUtilities;
    }

    /**
     *
     * @param \Chamilo\Libraries\Utilities\StringUtilities $stringUtilities
     */
    public function setStringUtilities(StringUtilities $stringUtilities)
    {
        $this->stringUtilities = $stringUtilities;
    }

    /**
     *
     * @param string $type
     * @param integer $userIdentifier
     * @param \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface $workspace
     * @param string[] $formValues
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Chamilo\Core\Repository\Common\Import\FormProcessor
     */
    public function getFormProcessor($type, $userIdentifier, WorkspaceInterface $workspace, $formValues, 
        Request $request)
    {
        $type = (string) StringUtilities::getInstance()->createString($type)->upperCamelize();
        $className = __NAMESPACE__ . '\\' . $type . '\FormProcessor';
        
        return new $className($userIdentifier, $workspace, $formValues, $request);
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Common\Import\FormProcessorFactory
     */
    static public function getInstance()
    {
        if (is_null(static::$instance))
        {
            self::$instance = new static(StringUtilities::getInstance());
        }
        
        return static::$instance;
    }
}