<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\Section\Storage\DataClass\Section;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataClass\Activity;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Component that allows the user to add content to the learning_path
 *
 * @package repository\content_object\learning_path\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CreatorComponent extends BaseHtmlTreeComponent implements \Chamilo\Core\Repository\Viewer\ViewerInterface,
    DelegateComponent
{
    const PARAM_CREATE_MODE = 'CreateMode';
    const CREATE_MODE_FOLDER = 'Folder';

    /**
     * Executes this component
     */
    public function build()
    {
        $this->validateSelectedTreeNodeData();

        if (! $this->canEditCurrentTreeNode())
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

        BreadcrumbTrail::getInstance()->add(new Breadcrumb($this->get_url(), Translation::get($variable)));

        if (! \Chamilo\Core\Repository\Viewer\Manager::is_ready_to_be_published())
        {
            $exclude = $this->determine_excluded_content_object_ids();

            $applicationConfiguration = new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this);
            $applicationConfiguration->set(\Chamilo\Core\Repository\Viewer\Manager::SETTING_TABS_DISABLED, true);

            $component = $this->getApplicationFactory()->getApplication(
                \Chamilo\Core\Repository\Viewer\Manager::context(),
                $applicationConfiguration);
            $component->set_excluded_objects($exclude);

            return $component->run();
        }
        else
        {
            $object_ids = \Chamilo\Core\Repository\Viewer\Manager::get_selected_objects();
            if (! is_array($object_ids))
            {
                $object_ids = array($object_ids);
            }

            $failures = 0;

            foreach ($object_ids as $object_id)
            {
                /** @var ContentObject $object */
                $object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                    ContentObject::class_name(),
                    $object_id);

                $learningPathService = $this->getLearningPathService();

                try
                {
                    $parentNode = $this->getCurrentTreeNode();

                    $treeNodeData = $learningPathService->addContentObjectToLearningPath(
                        $this->get_root_content_object(),
                        $parentNode,
                        $object,
                        $this->getUser());

                    $nextStep = $treeNodeData->getId();

                    Event::trigger(
                        'Activity',
                        \Chamilo\Core\Repository\Manager::context(),
                        array(
                            Activity::PROPERTY_TYPE => Activity::ACTIVITY_ADD_ITEM,
                            Activity::PROPERTY_USER_ID => $this->get_user_id(),
                            Activity::PROPERTY_DATE => time(),
                            Activity::PROPERTY_CONTENT_OBJECT_ID => $this->getCurrentContentObject()->getId(),
                            Activity::PROPERTY_CONTENT => $this->getCurrentContentObject()->get_title() . ' > ' .
                                 $object->get_title()));
                }
                catch (\Exception $ex)
                {
                    $failures ++;
                }
            }

            if (! isset($nextStep))
            {
                $nextStep = $this->getCurrentTreeNodeDataId();
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
                    Utilities::COMMON_LIBRARIES),
                ($failures ? true : false),
                array(self::PARAM_ACTION => self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT, self::PARAM_CHILD_ID => $nextStep));
        }
    }

    /**
     * Determine which content objects can't be added to this learning_path
     *
     * @return int[]
     */
    private function determine_excluded_content_object_ids()
    {
        return array();
    }

    /**
     *
     * @return array
     */
    public function get_additional_parameters()
    {
        return array(self::PARAM_CHILD_ID, self::PARAM_CREATE_MODE);
    }

    /**
     *
     * @return array
     */
    public function get_allowed_content_object_types()
    {
        if ($this->isFolderCreateMode())
        {
            return array(Section::class_name());
        }

        return $this->get_root_content_object()->get_allowed_types();
    }

    /**
     *
     * @return bool
     */
    public function isFolderCreateMode()
    {
        return $this->getRequest()->get(self::PARAM_CREATE_MODE) == self::CREATE_MODE_FOLDER;
    }

    /**
     *
     * @return string
     */
    public function render_header()
    {
        $html = array();
        $html[] = parent::render_header();
        $html[] = $this->renderRepoDragPanel();

        return implode(PHP_EOL, $html);
    }
}
