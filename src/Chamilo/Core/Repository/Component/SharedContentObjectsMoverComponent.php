<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory;
use Chamilo\Core\Repository\Storage\DataClass\SharedContentObjectRelCategory;
use Chamilo\Core\Repository\Storage\DataManager;
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

/**
 * Default repository manager component which allows the user to move a share to a different share category
 *
 * @author Pieterjan Broekaert Hogeschool Gent
 */
class SharedContentObjectsMoverComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     *
     * @todo remove code duplication with repository component mover component
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

            $this->tree[] = Translation :: get('ContentObjectsSharedWithMe');

            $this->get_categories_for_select(0, Request :: get(self :: PARAM_SHARED_CATEGORY_ID));
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
                    // Does this user already have a share category relation for this object?
                    $share_category_relation_object = DataManager :: retrieve_shared_content_object_rel_category_for_user_and_content_object(
                        $this->get_user_id(),
                        $id);
                    if ($share_category_relation_object)
                    {
                        if ($destination == 0)
                        {
                            if (! $share_category_relation_object->delete())
                            {
                                $failures ++;
                            }
                        }
                        else
                        {
                            $share_category_relation_object->set_category_id($destination);
                            if (! $share_category_relation_object->update())
                            {
                                $failures ++;
                            }
                        }
                    }
                    else
                    {
                        $share_category_relation_object = new SharedContentObjectRelCategory();
                        $share_category_relation_object->set_content_object_id($id);
                        $share_category_relation_object->set_category_id($destination);
                        if (! $share_category_relation_object->create())
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
                $parameters[self :: PARAM_ACTION] = self :: ACTION_BROWSE_SHARED_CONTENT_OBJECTS;
                $parameters[self :: PARAM_SHARED_CATEGORY_ID] = $destination;
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
            new StaticConditionVariable(RepositoryCategory :: TYPE_SHARED));

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
                $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_SHARED_CONTENT_OBJECTS)),
                Translation :: get('RepositoryManagerSharedContentObjectBrowserComponent')));
        $breadcrumbtrail->add_help('share_mover');
    }

    public function get_additional_parameters()
    {
        return parent :: get_additional_parameters(array(self :: PARAM_SHARED_VIEW, self :: PARAM_CONTENT_OBJECT_ID));
    }
}
