<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Display;
use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Bridge\Interfaces\GradeBookServiceBridgeInterface;


/**
 * Class ApplicationFactory
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Display
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class ApplicationFactory extends \Chamilo\Libraries\Architecture\Factory\ApplicationFactory
{
    /**
     * @var \Chamilo\Core\Repository\ContentObject\GradeBook\Display\Bridge\Interfaces\GradeBookServiceBridgeInterface
     */
    protected $gradebookServiceBridge;

    /**
     * @param \Chamilo\Core\Repository\ContentObject\GradeBook\Display\Bridge\Interfaces\GradeBookServiceBridgeInterface $gradebookServiceBridge
     */
    public function setGradeBookServiceBridge(GradeBookServiceBridgeInterface $gradebookServiceBridge)
    {
        $this->gradebookServiceBridge = $gradebookServiceBridge;
    }

    public function getDefaultAction($context)
    {
        if ($this->gradebookServiceBridge->canEditGradeBook()) {
            return Manager::ACTION_BROWSE;
        }
        return Manager::ACTION_USER_SCORES;
    }
}