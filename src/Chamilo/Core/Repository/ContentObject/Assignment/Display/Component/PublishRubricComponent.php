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
class PublishRubricComponent extends Manager
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

        if (!\Chamilo\Core\Repository\Viewer\Manager::any_object_selected())
        {
            $applicationConfiguration = new ApplicationConfiguration($this->getRequest(), $this->getUser(), $this);
            $applicationConfiguration->setAllowedContentObjectTypes([Rubric::class]);
            $applicationConfiguration->disableBreadcrumbs();
            $applicationConfiguration->setMaximumSelect(ApplicationConfiguration::MAXIMUM_SELECT_SINGLE);

            return $this->getApplicationFactory()->getApplication(
                \Chamilo\Core\Repository\Viewer\Manager::context(),
                $applicationConfiguration
            )->run();
        }
        else
        {
            $selectedRubricId = \Chamilo\Core\Repository\Viewer\Manager::get_selected_objects($this->getUser());

            try
            {
                $this->getAssignmentRubricService()->attachRubricToAssignmentById(
                    $this->getAssignment(), $selectedRubricId
                );

                $success = true;
                $message = 'RubricPublished';
            }
            catch (\Exception $ex)
            {
                $success = false;
                $message = 'RubricNotPublished';
                $this->getExceptionLogger()->logException($ex);
            }

            $this->redirect(
                $this->getTranslator()->trans($message, [], Manager::context()),
                !$success,
                [self::PARAM_ACTION => self::ACTION_VIEW, ViewerComponent::PARAM_SELECTED_TAB => 'rubric']
            );

            return null;
        }
    }
}
