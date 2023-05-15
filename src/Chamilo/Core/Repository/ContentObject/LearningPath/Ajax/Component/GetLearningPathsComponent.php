<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\Filter\FilterData;
use Chamilo\Core\Repository\Service\TemplateRegistrationConsulter;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;

/**
 * Returns the available learning paths for the given user
 *
 * @author pjbro <pjbro@users.noreply.github.com>
 * @author Sven Vanpoucke - sven.vanpoucke@hogent.be
 */
class GetLearningPathsComponent extends GetContentObjectsComponent
{
    /**
     * Returns the filter data for the given category, search query and workspace
     *
     * @param int $categoryId
     * @param string $searchQuery
     *
     * @return FilterData
     */
    protected function getFilterData($categoryId = null, string $searchQuery, Workspace $workspace): FilterData
    {
        $templateRegistration = $this->getTemplateRegistrationConsulter()->getTemplateRegistrationDefaultByType(
            LearningPath::CONTEXT
        );

        $filterData = parent::getFilterData($categoryId, $searchQuery, $workspace);
        $filterData->set_filter_property(FilterData::FILTER_TYPE, $templateRegistration->getId());

        return $filterData;
    }

    /**
     * @return \Chamilo\Core\Repository\Service\TemplateRegistrationConsulter
     */
    public function getTemplateRegistrationConsulter()
    {
        return $this->getService(TemplateRegistrationConsulter::class);
    }

    /**
     * Validates the given content object
     *
     * @param ContentObject $contentObject
     *
     * @return bool
     */
    protected function validateContentObject(ContentObject $contentObject)
    {
        return ($contentObject instanceof LearningPath);
    }
}