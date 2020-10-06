<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Component;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Component
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class RemoveRubricComponent extends Manager
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
            $this->getAssignmentRubricService()->removeRubricFromAssigment($this->getAssignment());
            $success = true;
            $message = 'RubricRemoved';
        }
        catch (\Exception $ex)
        {
            $success = false;
            $message = 'RubricNotRemoved';
            $this->getExceptionLogger()->logException($ex);
        }

        $this->redirect($message, !$success, [self::PARAM_ACTION => self::ACTION_VIEW, ViewerComponent::PARAM_SELECTED_TAB => 'rubric']);

        return null;
    }
}
