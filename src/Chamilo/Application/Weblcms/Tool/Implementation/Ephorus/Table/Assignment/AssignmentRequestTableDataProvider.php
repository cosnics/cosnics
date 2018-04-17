<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Table\Assignment;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataManager\Implementation\DoctrineExtension;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * Data provider for ephorus requests browser table.
 *
 * @author Tom Goethals - Hogeschool Gent
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AssignmentRequestTableDataProvider extends DataClassTableDataProvider
{

    private $extension;

    /**
     * Gets the objects to display in the table.
     * For now, objects are composed in the code itself from several source
     * objects.
     *
     * @param $offset
     * @param $count
     * @param null $order_property
     *
     * @return mixed
     */
    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        if ($order_property == null)
        {
            $order_property = new OrderBy(
                new PropertyConditionVariable(Request::class, Request::PROPERTY_REQUEST_TIME)
            );
        }

        return $this->getExtension()->retrieve_results_by_assignment(
            new DataClassRetrievesParameters($condition, $count, $offset, $order_property), $this->determineEntryClass()
        );
    }

    /**
     * Returns the count of the objects
     *
     * @return int
     */
    public function count_data($condition)
    {
        return $this->getExtension()->count_results_content_objects_by_assignment(
            $condition, $this->determineEntryClass()
        );
    }

    public function getExtension()
    {
        if (!isset($this->extension))
        {
            $this->extension = new DoctrineExtension(DataManager::getInstance());
        }

        return $this->extension;
    }

    public function determineEntryClass()
    {
        return $this->get_component()->getSource() == Manager::SOURCE_LEARNING_PATH_ASSIGNMENT ?
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPath\Assignment\Entry::class :
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\Assignment\Entry::class;
    }
}
