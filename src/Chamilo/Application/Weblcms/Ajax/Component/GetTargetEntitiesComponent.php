<?php

namespace Chamilo\Application\Weblcms\Ajax\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataManager;

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

    /**
     * Executes this component and returns it's result
     */
    public function run()
    {
        $targetEntities = $this->getTargetEntities();

        var_dump($targetEntities);
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

        if (isset($publicationId) && !empty($publicationId))
        {
            return $this->getTargetEntitiesFromRightsSystem($publicationId, WeblcmsRights::TYPE_PUBLICATION);
        }

        if (isset($publicationCategoryId) && !empty($publicationCategoryId))
        {
            return $this->getTargetEntitiesFromRightsSystem($publicationId, WeblcmsRights::TYPE_COURSE_CATEGORY);
        }

        if (isset($tool) && !empty($tool) && $tool != 'Home')
        {
            $toolRegistration = DataManager:: retrieve_course_tool_by_name($tool);
            if ($toolRegistration)
            {
                return $this->getTargetEntitiesFromRightsSystem(
                    $toolRegistration->getId(), WeblcmsRights::TYPE_COURSE_MODULE
                );
            }
        }

        return $this->getTargetEntitiesFromRightsSystem(0, WeblcmsRights::TYPE_ROOT);
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
        return WeblcmsRights:: get_instance()->get_target_entities(
            $this->getRight(),
            \Chamilo\Application\Weblcms\Manager:: context(),
            $identifier,
            $type,
            $this->getCourseId(),
            WeblcmsRights :: TREE_TYPE_COURSE
        );
    }

    /**
     * Returns the required post parameters. The course id is the only one that is really required,
     * the others are optional.
     *
     * @return array
     */
    public function getRequiredPostParameters()
    {return array();
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
        return $this->getRequest()->get(self::PARAM_PUBLICATION_CATEGORY_ID);
    }

    /**
     * Returns the right that is selected
     *
     * @return int
     */
    protected function getRight()
    {
        $right = $this->getRequest()->get(self::PARAM_RIGHT);
        if (!isset($right))
        {
            $right = WeblcmsRights::VIEW_RIGHT;
        }

        return $right;
    }
}