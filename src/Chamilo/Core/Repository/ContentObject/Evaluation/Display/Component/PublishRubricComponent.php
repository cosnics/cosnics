<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Component;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\DataClass\Rubric;
use Chamilo\Core\Repository\Viewer\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use http\Exception\InvalidArgumentException;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Component
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
        if (!$this->getEvaluationServiceBridge()->canEditEvaluation())
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
            try
            {
                $selectedRubricId = \Chamilo\Core\Repository\Viewer\Manager::get_selected_objects($this->getUser());

                $object = $this->get_root_content_object();
                $object->setRubricId((int) $selectedRubricId);
                $object->update();

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
                [self::PARAM_ACTION => self::DEFAULT_ACTION]
            );
        }
    }
}
