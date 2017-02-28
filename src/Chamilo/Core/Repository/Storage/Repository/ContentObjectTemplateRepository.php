<?php

namespace Chamilo\Core\Repository\Storage\Repository;

use Chamilo\Core\Repository\Storage\DataClass\TemplateRegistration;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ContentObjectTemplateRepository
{
    /**
     * Returns a content object template registration for a given content object context and template name
     *
     * @param string $contentObjectType
     * @param string $templateName
     *
     * @return TemplateRegistration
     */
    public function getTemplateRegistrationByContentObjectTypeAndTemplateName($contentObjectType, $templateName)
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                TemplateRegistration::class_name(), TemplateRegistration::PROPERTY_CONTENT_OBJECT_TYPE
            ),
            new StaticConditionVariable($contentObjectType)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                TemplateRegistration::class_name(), TemplateRegistration::PROPERTY_NAME
            ),
            new StaticConditionVariable($templateName)
        );

        $condition = new AndCondition($conditions);

        return DataManager::retrieve(TemplateRegistration::class_name(), new DataClassRetrieveParameters($condition));
    }
}