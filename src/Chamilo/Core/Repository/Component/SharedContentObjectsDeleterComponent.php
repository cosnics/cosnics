<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\RepositoryRights;
use Chamilo\Core\Repository\Storage\DataClass\RightsLocationEntityRight;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Default repository manager component which allows the user to delete a user entity share
 *
 * @author Pieterjan Broekaert Hogeschool Gent
 */
class SharedContentObjectsDeleterComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     *
     * @todo remove code duplication with repository component mover component
     */
    public function run()
    {
        $failures = 0;
        $ids = Request :: get(self :: PARAM_CONTENT_OBJECT_ID);
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }

            foreach ($ids as $id)
            {
                $content_object = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_content_object($id);
                // retrieve location_id
                $location_id = RepositoryRights :: get_instance()->get_location_id_by_identifier_from_user_subtree(
                    RepositoryRights :: TYPE_USER_CONTENT_OBJECT,
                    $id,
                    $content_object->get_owner_id());

                // retrieve the rights entity right
                $conditions = array();
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(
                        RightsLocationEntityRight :: class_name(),
                        RightsLocationEntityRight :: PROPERTY_ENTITY_TYPE),
                    new StaticConditionVariable(UserEntity :: ENTITY_TYPE));
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(
                        RightsLocationEntityRight :: class_name(),
                        RightsLocationEntityRight :: PROPERTY_LOCATION_ID),
                    new StaticConditionVariable($location_id));
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(
                        RightsLocationEntityRight :: class_name(),
                        RightsLocationEntityRight :: PROPERTY_ENTITY_ID),
                    new StaticConditionVariable($this->get_user_id()));
                $condition = new AndCondition($conditions);

                // delete the share
                // DMTODO: this could probably be replaced by a generic retrieves by passing the appropriate
                // RightsLocation subclass (and using the corresponding DM).
                $rights_entity_right = \Chamilo\Core\Rights\Storage\DataManager :: retrieve_rights_location_rights(
                    self :: context(),
                    $condition)->next_result();
                if ($rights_entity_right)
                {
                    $rights_entity_right->set_context(self :: context());

                    if (! $rights_entity_right->delete())
                    {
                        $failures ++;
                    }
                }
                else
                {
                    $failures ++;
                }

                // remove the share category link
                $share_category_relation_object = DataManager :: retrieve_shared_content_object_rel_category_for_user_and_content_object(
                    $this->get_user_id(),
                    $id);
                if ($share_category_relation_object)
                {
                    if (! $share_category_relation_object->delete())
                    {
                        $failures ++;
                    }
                }
            }

            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = Translation :: get(
                        'ObjectNotDeleted',
                        array('OBJECT' => Translation :: get('ContentObject')),
                        Utilities :: COMMON_LIBRARIES);
                }
                else
                {
                    $message = Translation :: get(
                        'ObjectsNotDeleted',
                        array('OBJECTS' => Translation :: get('ContentObjects')),
                        Utilities :: COMMON_LIBRARIES);
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = Translation :: get(
                        'ObjectDeleted',
                        array('OBJECT' => Translation :: get('ContentObject')),
                        Utilities :: COMMON_LIBRARIES);
                }
                else
                {
                    $message = Translation :: get(
                        'ObjectsDeleted',
                        array('OBJECTS' => Translation :: get('ContentObjects')),
                        Utilities :: COMMON_LIBRARIES);
                }
            }

            $parameters = array();
            $parameters[self :: PARAM_ACTION] = self :: ACTION_BROWSE_SHARED_CONTENT_OBJECTS;
            $this->redirect($message, ($failures ? true : false), $parameters);
        }
        else
        {
            return $this->display_error_page(
                htmlentities(
                    Translation :: get(
                        'NoObjectSelected',
                        array('OBJECT' => Translation :: get('ContentObject')),
                        Utilities :: COMMON_LIBRARIES)));
        }
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_SHARED_CONTENT_OBJECTS)),
                Translation :: get('RepositoryManagerSharedContentObjectBrowserComponent')));
        $breadcrumbtrail->add_help('share_mover');
    }

    public function get_additional_parameters()
    {
        return array(self :: PARAM_SHARED_VIEW, self :: PARAM_CONTENT_OBJECT_ID);
    }
}
