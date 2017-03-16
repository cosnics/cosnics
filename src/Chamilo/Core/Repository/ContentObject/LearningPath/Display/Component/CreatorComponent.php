<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\ComplexLearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPathChild;
use Chamilo\Core\Repository\ContentObject\LearningPathItem\Storage\DataClass\ComplexLearningPathItem;
use Chamilo\Core\Repository\ContentObject\LearningPathItem\Storage\DataClass\LearningPathItem;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataClass\Activity;
use Chamilo\Core\Repository\Selector\TypeSelector;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Component that allows the user to add content to the learning_path
 *
 * @package repository\content_object\learning_path\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CreatorComponent extends TabComponent implements \Chamilo\Core\Repository\Viewer\ViewerInterface,
    DelegateComponent
{
    const PARAM_CREATE_MODE = 'CreateMode';
    const CREATE_MODE_FOLDER = 'Folder';

    /**
     * Executes this component
     */
    public function build()
    {
        $this->validateAndFixCurrentStep();

        if (!$this->canEditLearningPathTreeNode($this->getCurrentLearningPathTreeNode()))
        {
            throw new NotAllowedException();
        }

        if ($this->isFolderCreateMode())
        {
            $variable = 'AddFolder';
        }
        else
        {
            $variable = 'CreatorComponent';
        }

        BreadcrumbTrail:: getInstance()->add(new Breadcrumb($this->get_url(), Translation:: get($variable)));

        if (!\Chamilo\Core\Repository\Viewer\Manager::is_ready_to_be_published())
        {
            $exclude = $this->detemine_excluded_content_object_ids();

            $applicationConfiguration = new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this);
            $applicationConfiguration->set(\Chamilo\Core\Repository\Viewer\Manager::SETTING_TABS_DISABLED, true);

            $factory = new ApplicationFactory(
                \Chamilo\Core\Repository\Viewer\Manager::context(),
                $applicationConfiguration
            );
            $component = $factory->getComponent();
            $component->set_excluded_objects($exclude);

            return $component->run();
        }
        else
        {
            $object_ids = \Chamilo\Core\Repository\Viewer\Manager::get_selected_objects();
            if (!is_array($object_ids))
            {
                $object_ids = array($object_ids);
            }

            $failures = 0;

            foreach ($object_ids as $object_id)
            {
                /** @var ContentObject $object */
                $object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                    ContentObject::class_name(),
                    $object_id
                );

                $learningPathChildService = $this->getLearningPathChildService();

                try
                {
                    $learningPathChildService->addContentObjectToLearningPath(
                        $this->getCurrentLearningPathTreeNode(), $object
                    );

                    Event::trigger(
                        'Activity',
                        \Chamilo\Core\Repository\Manager::context(),
                        array(
                            Activity::PROPERTY_TYPE => Activity::ACTIVITY_ADD_ITEM,
                            Activity::PROPERTY_USER_ID => $this->get_user_id(),
                            Activity::PROPERTY_DATE => time(),
                            Activity::PROPERTY_CONTENT_OBJECT_ID => $this->get_current_node()->get_content_object()
                                ->get_id(),
                            Activity::PROPERTY_CONTENT => $this->get_current_node()->get_content_object()->get_title() .
                                ' > ' . $object->get_title()
                        )
                    );
                }
                catch (\Exception $ex)
                {
                    $failures ++;
                }
            }

            if (count($object_ids) > 0 && !$failures)
            {
                $parentContentObjectIds = $this->getCurrentLearningPathTreeNode()->getPathAsContentObjectIds();

                if (count($object_ids) == 1)
                {
                    $parentContentObjectIds[] = $object_ids[0];
                }

                $learningPathTree = $this->recalculateLearningPathTree();

                $learningPathTreeNode =
                    $learningPathTree->getLearningPathTreeNodeForContentObjectIdentifiedByParentContentObjects(
                        $parentContentObjectIds
                    );

                $next_step = $learningPathTreeNode->getStep();
            }
            else
            {
                $next_step = $this->get_current_step();
            }

            if ($failures)
            {
                if (count($object_ids) == 1)
                {
                    $message = 'ObjectNotAdded';
                }
                else
                {
                    $message = 'ObjectsNotAdded';
                }
            }
            else
            {
                if (count($object_ids) == 1)
                {
                    $message = 'ObjectAdded';
                }
                else
                {
                    $message = 'ObjectsAdded';
                }
            }

            $this->redirect(
                Translation::get(
                    $message,
                    array('OBJECT' => Translation::get('Item'), 'OBJECTS' => Translation::get('Items')),
                    Utilities::COMMON_LIBRARIES
                ),
                ($failures ? true : false),
                array(self::PARAM_ACTION => self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT, self::PARAM_STEP => $next_step)
            );
        }
    }

    /**
     * Determine which content objects can't be added to this learning_path
     *
     * @return int[]
     */
    private function detemine_excluded_content_object_ids()
    {
        $learningPathChildValidator = $this->getLearningPathChildValidator();

        return $learningPathChildValidator->getContentObjectIdsThatCanNotBeAddedTo(
            $this->getCurrentLearningPathTreeNode()
        );
    }

    /**
     *
     * @see \libraries\SubManager::get_additional_parameters()
     */
    public function get_additional_parameters()
    {
        return array(self::PARAM_STEP, self::PARAM_CONTENT_OBJECT_ID, self::PARAM_CREATE_MODE,);
    }

    /**
     *
     * @see \core\repository\viewer\ViewerInterface::get_allowed_content_object_types()
     */
    public function get_allowed_content_object_types()
    {
        if ($this->isFolderCreateMode())
        {
            return array(LearningPath::class_name());
        }

        return $this->get_root_content_object()->get_allowed_types();
    }

    /**
     * @return bool
     */
    public function isFolderCreateMode()
    {
        return $this->getRequest()->get(self::PARAM_CREATE_MODE) == self::CREATE_MODE_FOLDER;
    }
}
