<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Builder\Table\AnswerFeedbackType;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @package core\repository\content_object\assessment\builder
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class AnswerFeedbackTypeTableDataProvider extends DataClassTableDataProvider
{

    public function countData(?Condition $condition = null): int
    {
        return DataManager::count(
            ComplexContentObjectItem::class, new DataClassCountParameters($condition)
        );
    }

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        if (is_null($orderBy))
        {
            $orderBy = new OrderBy();
        }

        $orderBy->add(
            new OrderProperty(
                new PropertyConditionVariable(
                    ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_DISPLAY_ORDER
                )
            )
        );

        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $orderBy);

        return DataManager::retrieves(
            ComplexContentObjectItem::class, $parameters
        );
    }
}
