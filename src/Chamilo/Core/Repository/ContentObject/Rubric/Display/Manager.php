<?php
namespace Chamilo\Core\Repository\ContentObject\Rubric\Display;

use Chamilo\Core\Repository\ContentObject\Rubric\Display\Bridge\RubricBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Rubric\Service\RubricService;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\DataClass\Rubric;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;

/**
 * Class Manager
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Display
 */
abstract class Manager extends \Chamilo\Core\Repository\Display\Manager
{
    const PARAM_ACTION = 'RubricAction';
    const ACTION_BUILDER = 'Builder';
    const ACTION_ENTRY = 'Entry';
    const ACTION_RESULT = 'Result';
    const ACTION_AJAX = 'Ajax';

    const DEFAULT_ACTION = self::ACTION_BUILDER;

    const PARAM_RUBRIC_CONTENT_OBJECT = 'RubricContentObject';

    /**
     * @var RubricBridgeInterface
     */
    protected $rubricBridge;

    /**
     * Manager constructor.
     *
     * @param ApplicationConfiguration $applicationConfiguration
     */
    public function __construct(ApplicationConfiguration $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);

        $this->rubricBridge = $this->getBridgeManager()->getBridgeByInterface(RubricBridgeInterface::class);
    }

    /**
     * @return RubricBridgeInterface
     */
    protected function getRubricBridge()
    {
        return $this->rubricBridge;
    }

    /**
     * @return RubricService
     */
    protected function getRubricService()
    {
        return $this->getService(RubricService::class);
    }

    /**
     * @return Rubric|\Chamilo\Core\Repository\Storage\DataClass\ContentObject
     */
    protected function getRubric()
    {
        $rubricContentObject = $this->getApplicationConfiguration()->get(self::PARAM_RUBRIC_CONTENT_OBJECT);
        if($rubricContentObject instanceof Rubric)
        {
            return $rubricContentObject;
        }

        return $this->get_root_content_object();
    }
}

