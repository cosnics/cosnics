<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Repository manager component which can be used to share content objects
 */
class ShareContentObjectsComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $object_ids = Request :: get(self :: PARAM_CONTENT_OBJECT_ID);

        if (! empty($object_ids))
        {
            if (! is_array($object_ids))
            {
                $object_ids = array($object_ids);
            }

            $condition = new InCondition(
                new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_ID),
                $object_ids,
                ContentObject :: get_table_name());
            $parameters = new DataClassRetrievesParameters($condition);
            $content_objects = DataManager :: retrieve_active_content_objects(
                ContentObject :: class_name(),
                $parameters);

            $factory = new ApplicationFactory(
                $this->getRequest(),
                \Chamilo\Core\Repository\Share\Manager :: context(),
                $this->get_user(),
                $this);
            $component = $factory->getComponent();
            $component->set_content_objects($content_objects->as_array());
            return $component->run();
        }
        else
        {
            $this->display_warning_page(
                htmlentities(
                    Translation :: get(
                        'NoObjectSelected',
                        array('OBJECT' => Translation :: get('ContentObject')),
                        Utilities :: COMMON_LIBRARIES)));
        }
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('repository_share_content_objects');
    }

    public function get_additional_parameters()
    {
        return array(self :: PARAM_CONTENT_OBJECT_ID);
    }
}
