<?php
namespace Chamilo\Application\Survey\Rights\Table\EntityRelation;

use Chamilo\Application\Survey\Storage\DataClass\PublicationEntityRelation;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

/**
 *
 * @package Chamilo\Application\Survey\Table\Publication
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntityRelationTableDataProvider extends DataClassTableDataProvider
{

    public function retrieve_data($condition, $offset, $count, $orderProperty = null)
    {
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $orderProperty);
        return DataManager :: retrieves(PublicationEntityRelation :: class_name(), $parameters);
    }

    public function count_data($condition)
    {
        $parameters = new DataClassCountParameters($condition);
        return DataManager :: count(PublicationEntityRelation :: class_name(), $parameters);
    }
}