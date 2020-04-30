<?php
namespace Chamilo\Application\Weblcms\Ajax\Component;

use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Rights\Entities\CourseGroupEntity;
use Chamilo\Application\Weblcms\Rights\Entities\CoursePlatformGroupEntity;
use Chamilo\Application\Weblcms\Rights\Entities\CourseUserEntity;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Returns the entities that have a given right on a given publication, publication category or tool
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GetTargetEntitiesComponent extends \Chamilo\Application\Weblcms\Ajax\Manager
{
    /**
     * The parameters
     */
    const PARAM_COURSE_ID = 'course_id';
    const PARAM_TOOL_NAME = 'tool_name';
    const PARAM_PUBLICATION_CATEGORY_ID = 'publication_category_id';
    const PARAM_PUBLICATION_ID = 'publication_id';
    const PARAM_RIGHT = 'right';
    const TARGET_ENTITY_USERS = 'users';
    const TARGET_ENTITY_PLATFORM_GROUPS = 'platform_groups';
    const TARGET_ENTITY_COURSE_GROUPS = 'course_groups';
    const TARGET_ENTITY_EVERYONE = 'everyone';

    /**
     * Executes this component and returns it's result
     */
    public function run()
    {
        $targetEntities = $this->getTargetEntities();
        $namedTargetEntities = $this->getTargetEntityNames($targetEntities);
        
        $result = new JsonAjaxResult(200, $namedTargetEntities);
        $result->display();
    }

    /**
     * Retrieves the target entities
     * 
     * @return array
     */
    protected function getTargetEntities()
    {
        $tool = $this->getToolName();
        $publicationId = $this->getPublicationId();
        $publicationCategoryId = $this->getPublicationCategoryId();
        
        if (isset($publicationId) && ! empty($publicationId))
        {
            return $this->getTargetEntitiesFromRightsSystem($publicationId, WeblcmsRights::TYPE_PUBLICATION);
        }
        
        if (isset($publicationCategoryId) && ! empty($publicationCategoryId))
        {
            return $this->getTargetEntitiesFromRightsSystem($publicationCategoryId, WeblcmsRights::TYPE_COURSE_CATEGORY);
        }
        
        if (isset($tool) && ! empty($tool) && $tool != 'Home')
        {
            $toolRegistration = DataManager::retrieve_course_tool_by_name($tool);
            if ($toolRegistration)
            {
                return $this->getTargetEntitiesFromRightsSystem(
                    $toolRegistration->getId(), 
                    WeblcmsRights::TYPE_COURSE_MODULE);
            }
        }
        
        return $this->getTargetEntitiesFromRightsSystem(0, WeblcmsRights::TYPE_ROOT);
    }

    /**
     * Retrieves the visual names for the target entities
     * 
     * @param array $targetEntities
     *
     * @return string
     */
    protected function getTargetEntityNames($targetEntities)
    {
        $translator = Translation::getInstance();
        $targetEntityNames = array();
        
        if (array_key_exists(0, $targetEntities[0]))
        {
            $targetEntityNames[self::TARGET_ENTITY_EVERYONE] = $translator->getTranslation(
                'Everybody', 
                null, 
                Utilities::COMMON_LIBRARIES);
        }
        else
        {
            foreach ($targetEntities as $entity_type => $entity_ids)
            {
                switch ($entity_type)
                {
                    case CoursePlatformGroupEntity::ENTITY_TYPE :
                        foreach ($entity_ids as $group_id)
                        {
                            $group = \Chamilo\Core\Group\Storage\DataManager::retrieve_by_id(
                                Group::class, 
                                $group_id);
                            if ($group)
                            {
                                $targetEntityNames[self::TARGET_ENTITY_PLATFORM_GROUPS][] = $group->get_name();
                            }
                        }
                        break;
                    case CourseUserEntity::ENTITY_TYPE :
                        foreach ($entity_ids as $user_id)
                        {
                            $targetEntityNames[self::TARGET_ENTITY_USERS][] = \Chamilo\Core\User\Storage\DataManager::get_fullname_from_user(
                                $user_id);
                        }
                        break;
                    case CourseGroupEntity::ENTITY_TYPE :
                        foreach ($entity_ids as $course_group_id)
                        {
                            $course_group = \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager::retrieve_by_id(
                                CourseGroup::class, 
                                $course_group_id);
                            
                            if ($course_group)
                            {
                                $targetEntityNames[self::TARGET_ENTITY_COURSE_GROUPS][] = $course_group->get_name();
                            }
                        }
                        break;
                    
                    case 0 :
                        $targetEntityNames[self::TARGET_ENTITY_EVERYONE] = Translation::get(
                            'Everybody', 
                            null, 
                            Utilities::COMMON_LIBRARIES);
                        break;
                }
            }
        }
        
        return $targetEntityNames;
    }

    /**
     * Helper function to retrieve the target entities from the rights system
     * 
     * @param int $identifier
     * @param int $type
     *
     * @return array
     */
    protected function getTargetEntitiesFromRightsSystem($identifier, $type)
    {
        return WeblcmsRights::getInstance()->get_target_entities(
            $this->getRight(), 
            Manager::context(),
            $identifier, 
            $type, 
            $this->getCourseId(), 
            WeblcmsRights::TREE_TYPE_COURSE);
    }

    /**
     * Returns the required post parameters.
     * The course id is the only one that is really required,
     * the others are optional.
     * 
     * @return array
     */
    public function getRequiredPostParameters()
    {
        return array(self::PARAM_COURSE_ID);
    }

    /**
     * Returns the course id
     * 
     * @return int
     */
    protected function getCourseId()
    {
        return $this->getRequest()->get(self::PARAM_COURSE_ID);
    }

    /**
     * Returns the name of the tool
     * 
     * @return string
     */
    protected function getToolName()
    {
        return $this->getRequest()->get(self::PARAM_TOOL_NAME);
    }

    /**
     * Returns the selected category id
     * 
     * @return int
     */
    protected function getPublicationCategoryId()
    {
        return $this->getRequest()->get(self::PARAM_PUBLICATION_CATEGORY_ID);
    }

    /**
     * Returns the selected publication id
     * 
     * @return int
     */
    protected function getPublicationId()
    {
        return $this->getRequest()->get(self::PARAM_PUBLICATION_ID);
    }

    /**
     * Returns the right that is selected
     * 
     * @return int
     */
    protected function getRight()
    {
        $right = $this->getRequest()->get(self::PARAM_RIGHT);
        if (! isset($right))
        {
            $right = WeblcmsRights::VIEW_RIGHT;
        }
        
        return $right;
    }
}