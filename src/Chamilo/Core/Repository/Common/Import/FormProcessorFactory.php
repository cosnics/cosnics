<?php
namespace Chamilo\Core\Repository\Common\Import;

use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Repository\Common\Import
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class FormProcessorFactory
{

    /**
     * @var \Chamilo\Core\Repository\Common\Import\FormProcessorFactory
     */
    private static $instance;

    /**
     * @var \Chamilo\Libraries\Utilities\StringUtilities
     */
    private $stringUtilities;

    public function __construct(StringUtilities $stringUtilities)
    {
        $this->stringUtilities = $stringUtilities;
    }

    /**
     * @param string $type
     * @param int $userIdentifier
     * @param string[] $formValues
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     *
     * @return \Chamilo\Core\Repository\Common\Import\FormProcessor
     */
    public function getFormProcessor(
        $type, $userIdentifier, Workspace $workspace, $formValues, ChamiloRequest $request
    )
    {
        $type = (string) StringUtilities::getInstance()->createString($type)->upperCamelize();
        $className = __NAMESPACE__ . '\\' . $type . '\FormProcessor';

        return new $className($userIdentifier, $workspace, $formValues, $request);
    }

    /**
     * @return \Chamilo\Core\Repository\Common\Import\FormProcessorFactory
     */
    public static function getInstance()
    {
        if (is_null(static::$instance))
        {
            self::$instance = new static(StringUtilities::getInstance());
        }

        return static::$instance;
    }

    /**
     * @return \Chamilo\Libraries\Utilities\StringUtilities
     */
    public function getStringUtilities()
    {
        return $this->stringUtilities;
    }

    /**
     * @param \Chamilo\Libraries\Utilities\StringUtilities $stringUtilities
     */
    public function setStringUtilities(StringUtilities $stringUtilities)
    {
        $this->stringUtilities = $stringUtilities;
    }
}