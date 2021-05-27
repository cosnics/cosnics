<?php
namespace Chamilo\Core\Repository\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\File\FileType;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * @package Chamilo\Core\Repository\Ajax\Component
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class AttachmentImagesFeedComponent extends AttachmentContentObjectsFeedComponent
{

    /**
     * @return integer
     */
    protected function countContentObjects()
    {
        return DataManager::count_active_content_objects(
            File::class, new DataClassCountParameters($this->getContentObjectConditions())
        );
    }

    /**
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     */
    protected function getContentObjectConditions()
    {
        $conditions = [];

        $conditions[] = parent:: getContentObjectConditions();

        $imageTypes = FileType::get_type_extensions(FileType::TYPE_IMAGE);
        $imageTypeConditions = [];

        foreach ($imageTypes as $type)
        {
            $imageTypeConditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(File::class, File::PROPERTY_FILENAME), '*.' . $type
            );
        }

        $conditions[] = new OrCondition($imageTypeConditions);

        return new AndCondition($conditions);
    }

    /**
     * @return \Chamilo\Core\Repository\Storage\DataClass\ContentObject[]
     */
    protected function retrieveContentObjects()
    {
        $parameters = new DataClassRetrievesParameters(
            $this->getContentObjectConditions(), 100, $this->getOffset(), array(
                new OrderBy(
                    new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_TITLE)
                )
            )
        );

        return DataManager::retrieve_active_content_objects(File::class, $parameters);
    }
}
