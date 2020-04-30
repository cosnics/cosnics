<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Component;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\LearningPathAssignmentService;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathTreeNodeAttempt;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Storage\DataManager;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package application.lib.weblcms.tool.learning_path.component
 */
class DeleterComponent extends Manager
{

    public function run()
    {
        if (Request::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID))
        {
            $publication_ids = Request::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);
        }
        else
        {
            $publication_ids = $_POST[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID];
        }

        if (!is_array($publication_ids))
        {
            $publication_ids = array($publication_ids);
        }

        foreach ($publication_ids as $pid)
        {
            /** @var ContentObjectPublication $publication */
            $publication = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
                ContentObjectPublication::class, $pid
            );

            if ($this->is_allowed(WeblcmsRights::DELETE_RIGHT, $publication) ||
                $publication->get_publisher_id() == $this->get_user_id())
            {
                $condition = new EqualityCondition(
                    new PropertyConditionVariable(
                        LearningPathTreeNodeAttempt::class_name(), LearningPathTreeNodeAttempt::PROPERTY_PUBLICATION_ID
                    ), new StaticConditionVariable($pid)
                );

                $attempts = DataManager::retrieves(
                    LearningPathTreeNodeAttempt::class_name(), new DataClassRetrievesParameters($condition)
                );

                while ($attempt = $attempts->next_result())
                {
                    $attempt->delete();
                }

                $contentObject = $publication->getContentObject();

                if ($contentObject instanceof LearningPath)
                {
                    $assignmentTreeNodes = $this->getLearningPathService()->getTreeNodesBySpecificTypes(
                        $contentObject, Assignment::class
                    );

                    foreach ($assignmentTreeNodes as $treeNode)
                    {
                        $this->getLearningPathAssignmentService()->deleteEntriesByTreeNodeData(
                            $publication, $treeNode->getTreeNodeData()
                        );
                    }
                }

                $publication->delete();
            }

            else
            {
                throw new NotAllowedException();
            }
        }
        if (count($publication_ids) > 1)
        {
            $message = htmlentities(
                Translation::get(
                    'ObjectsDeleted', array('OBJECT' => Translation::get('LearningPath')), Utilities::COMMON_LIBRARIES
                )
            );
        }
        else
        {
            $message = htmlentities(
                Translation::get(
                    'ObjectDeleted', array('OBJECT' => Translation::get('LearningPath')), Utilities::COMMON_LIBRARIES
                )
            );
        }

        $this->redirect(
            $message, '',
            array('tool_action' => null, \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => null)
        );
    }

    /**
     * @return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\LearningPathAssignmentService
     */
    protected function getLearningPathAssignmentService()
    {
        return $this->getService(LearningPathAssignmentService::class);
    }
}
