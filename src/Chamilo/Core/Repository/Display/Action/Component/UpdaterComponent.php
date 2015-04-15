<?php
namespace Chamilo\Core\Repository\Display\Action\Component;

use Chamilo\Core\Repository\Display\Action\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 *
 * @author Original author unknown
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UpdaterComponent extends Manager
{

    public function run()
    {
        if ($this->get_parent()->get_parent()->is_allowed_to_edit_content_object())
        {
            $selected_complex_content_object_item = $this->get_selected_complex_content_object_item();
            $content_object = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_content_object(
                $selected_complex_content_object_item->get_ref());
            $form = \Chamilo\Core\Repository\Form\ContentObjectForm :: factory(
                \Chamilo\Core\Repository\Form\ContentObjectForm :: TYPE_EDIT,
                $content_object,
                'edit',
                'post',
                $this->get_url(
                    array(
                        \Chamilo\Core\Repository\Display\Manager :: PARAM_ACTION => \Chamilo\Core\Repository\Display\Manager :: ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM,
                        \Chamilo\Core\Repository\Display\Manager :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_selected_complex_content_object_item_id(),
                        \Chamilo\Core\Repository\Display\Manager :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id())));

            if ($form->validate())
            {
                $succes = $form->update_content_object();
                if ($succes && $form->is_version())
                {
                    $old_id = $selected_complex_content_object_item->get_ref();
                    $new_id = $content_object->get_latest_version()->get_id();
                    $selected_complex_content_object_item->set_ref($new_id);
                    $selected_complex_content_object_item->update();
                    $condition = new EqualityCondition(
                        new PropertyConditionVariable(
                            ComplexContentObjectItem :: class_name(),
                            ComplexContentObjectItem :: PROPERTY_PARENT),
                        new StaticConditionVariable($old_id),
                        ComplexContentObjectItem :: get_table_name());
                    $parameters = new DataClassRetrievesParameters($condition);
                    $children = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_complex_content_object_items(
                        ComplexContentObjectItem :: class_name(),
                        $parameters);
                    $failures = 0;
                    while ($child = $children->next_result())
                    {
                        $child->set_parent($new_id);
                        if (! $child->update())
                        {
                            $failures ++;
                        }
                    }

                    $succes = ($succes) && ($failures == 0);
                }

                $message = htmlentities(
                    Translation :: get(
                        ($succes ? 'ObjectUpdated' : 'ObjectNotUpdated'),
                        array('OBJECT' => Translation :: get('ContentObject')),
                        Utilities :: COMMON_LIBRARIES));

                $params = array();
                $params[\Chamilo\Core\Repository\Display\Manager :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id();
                $params[\Chamilo\Core\Repository\Display\Manager :: PARAM_ACTION] = \Chamilo\Core\Repository\Display\Manager :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT;

                $this->redirect($message, (! $succes), $params);
            }
            else
            {
                $trail = BreadcrumbTrail :: get_instance();
                $trail->add(
                    new Breadcrumb(
                        $this->get_url(
                            array(
                                \Chamilo\Core\Repository\Display\Manager :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_selected_complex_content_object_item_id(),
                                \Chamilo\Core\Repository\Display\Manager :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id())),
                        Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES)));

                $html = array();

                $html[] = $this->render_header();
                $html[] = $form->toHtml();
                $html[] = $this->render_footer();

                return implode(PHP_EOL, $html);
            }
        }
        else
        {
            throw new NotAllowedException();
        }
    }
}
