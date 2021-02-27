<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Component;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\DataClass\Rubric;
use Chamilo\Core\Repository\Viewer\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use http\Exception\InvalidArgumentException;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ToggleRubricSelfEvaluationComponent extends Manager
{
    /**
     * @return string
     * @throws \Exception
     */
    public function run()
    {
        if (!$this->getRightsService()->canUserEditAssignment($this->getUser(), $this->getAssignment()))
        {
            throw new NotAllowedException();
        }

        try
        {
            $this->getAssignmentRubricService()->toggleRubricSelfEvaluationComponent($this->getAssignment());
            $success = true;
            $message = 'SelfEvaluationChanged';
        }
        catch(\Exception $ex)
        {
            $success = false;
            $message = 'SelfEvaluationNotChanged';
        }

        $this->redirect(
            $this->getTranslator()->trans($message, [], Manager::context()),
            !$success,
            [self::PARAM_ACTION => self::ACTION_VIEW, ViewerComponent::PARAM_SELECTED_TAB => 'rubric']
        );

        return null;
    }
}
