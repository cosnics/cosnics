<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Filter\FilterData;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;

/**
 * $Id: mover.class.php 204 2009-11-13 12:51:30Z kariboe $
 *
 * @package repository.lib.repository_manager.component
 */
/**
 * Repository manager component to move objects between categories in the repository.
 */
class MoverComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $ids = Request :: get(self :: PARAM_CONTENT_OBJECT_ID);
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }

            $object = DataManager :: retrieve_by_id(ContentObject :: class_name(), $ids[0]);
            $parent = $object->get_parent_id();

            $this->tree = array();
            if ($parent != 0)
                $this->tree[] = Translation :: get('Repository');

            $this->get_categories_for_select(0, $parent);
            $form = new FormValidator('move', 'post', $this->get_url(array(self :: PARAM_CONTENT_OBJECT_ID => $ids)));
            $form->addElement(
                'select',
                self :: PARAM_DESTINATION_CONTENT_OBJECT_ID,
                Translation :: get('NewCategory'),
                $this->tree);
            $form->addElement('submit', 'submit', Translation :: get('Move', null, Utilities :: COMMON_LIBRARIES));
            if ($form->validate())
            {
                $destination = $form->exportValue(self :: PARAM_DESTINATION_CONTENT_OBJECT_ID);
                $failures = 0;
                foreach ($ids as $id)
                {
                    $object = DataManager :: retrieve_by_id(ContentObject :: class_name(), $id);
                    $versions = DataManager :: get_version_ids($object);

                    foreach ($versions as $version)
                    {
                        $object = DataManager :: retrieve_by_id(ContentObject :: class_name(), $version);
                        // TODO: Roles & Rights.
                        if ($object->get_owner_id() != $this->get_user_id())
                        {
                            $failures ++;
                        }
                        elseif ($object->get_parent_id() != $destination)
                        {
                            if (! $object->move_allowed($destination))
                            {
                                $failures ++;
                            }
                            else
                            {
                                $object->move($destination);
                            }
                        }
                    }
                }

                // TODO: SCARA - Correctto reflect possible version errors
                if ($failures)
                {
                    if (count($ids) == 1)
                    {
                        $message = Translation :: get(
                            'ObjectNotMoved',
                            array('OBJECT' => Translation :: get('ContentObject')),
                            Utilities :: COMMON_LIBRARIES);
                    }
                    else
                    {
                        $message = Translation :: get(
                            'ObjectsNotMoved',
                            array('OBJECTS' => Translation :: get('ContentObjects')),
                            Utilities :: COMMON_LIBRARIES);
                    }
                }
                else
                {
                    if (count($ids) == 1)
                    {
                        $message = Translation :: get(
                            'ObjectMoved',
                            array('OBJECT' => Translation :: get('ContentObject')),
                            Utilities :: COMMON_LIBRARIES);
                    }
                    else
                    {
                        $message = Translation :: get(
                            'ObjectsMoved',
                            array('OBJECTS' => Translation :: get('ContentObjects')),
                            Utilities :: COMMON_LIBRARIES);
                    }
                }

                $parameters = array();
                $parameters[Application :: PARAM_ACTION] = self :: ACTION_BROWSE_CONTENT_OBJECTS;
                $parameters[FilterData :: FILTER_CATEGORY] = $object->get_parent_id();
                $this->redirect($message, ($failures ? true : false), $parameters);
            }
            else
            {
                $html = array();

                $html[] = $this->render_header();
                $html[] = $form->toHtml();
                $html[] = $this->render_footer();

                return implode(PHP_EOL, $html);
            }
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

    /**
     * Get all categories from which a user can select a target category when moving objects.
     *
     * @param array $exclude An array of category-id's which should be excluded from the resulting list.
     * @return array A list of possible categories from which a user can choose. Can be used as input for a QuickForm
     *         select field.
     */
    private $level = 1;

    private $tree = array();

    private function get_categories_for_select($parent_id, $current_parent)
    {
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory :: class_name(), RepositoryCategory :: PROPERTY_PARENT),
            new StaticConditionVariable($parent_id));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory :: class_name(), RepositoryCategory :: PROPERTY_USER_ID),
            new StaticConditionVariable($this->get_user_id()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory :: class_name(), RepositoryCategory :: PROPERTY_TYPE),
            new StaticConditionVariable(RepositoryCategory :: TYPE_NORMAL));

        $condition = new AndCondition($conditions);

        $categories = DataManager :: retrieve_categories($condition);

        $tree = array();
        while ($cat = $categories->next_result())
        {
            $this->tree[$cat->get_id()] = str_repeat('--', $this->level) . ' ' . $cat->get_name();

            if ($current_parent == $cat->get_id())
            {
                $this->tree[$cat->get_id()] .= ' (' . Translation :: get('Current', null, Utilities :: COMMON_LIBRARIES) .
                     ')';
            }

            $this->level ++;
            $this->get_categories_for_select($cat->get_id(), $current_parent);
            $this->level --;
        }
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CONTENT_OBJECTS)),
                Translation :: get('RepositoryManagerBrowserComponent')));
        $breadcrumbtrail->add_help('repository_mover');
    }

    public function get_additional_parameters()
    {
        return parent :: get_additional_parameters(array(self :: PARAM_CONTENT_OBJECT_ID));
    }
}
