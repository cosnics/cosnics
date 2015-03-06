<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table;

use Chamilo\Core\Repository\Builder\Action\Manager;
use Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass\Assessment;
use Chamilo\Core\Repository\ContentObject\LearningPathItem\Storage\DataClass\LearningPathItem;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupport;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Condition\SubselectCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: learning_path_browser_table_cell_renderer.class.php 200 2009-11-13 12:30:04Z kariboe $
 *
 * @package repository.lib.complex_builder.learning_path.component.browser
 */
/**
 * Cell rendere for the learning object browser table
 */
class ComplexTableCellRenderer extends \Chamilo\Core\Repository\Table\Complex\ComplexTableCellRenderer
{

    private $lpi_ref_object;

    // Inherited
    function render_cell($column, $complex_content_object_item)
    {
        $content_object = $complex_content_object_item->get_ref_object();

        if ($content_object->get_type() == LearningPathItem :: class_name())
        {
            if (! $this->lpi_ref_object || $this->lpi_ref_object->get_id() != $content_object->get_reference())
            {
                $content_object = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_content_object(
                    $content_object->get_reference());
                $this->lpi_ref_object = $content_object;
            }
            else
            {
                $content_object = $this->lpi_ref_object;
            }
        }

        switch ($column->get_name())
        {
            case ContentObject :: PROPERTY_TITLE :
                $title = htmlspecialchars($content_object->get_title());
                $title_short = $title;

                $title_short = Utilities :: truncate_string($title_short, 53, false);

                if ($content_object->get_type() == LearningPath :: class_name())
                {
                    $title_short = '<a href="' .
                         $this->get_component()->get_url(
                            array(
                                Manager :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item->get_id())) .
                         '">' . $title_short . '</a>';
                }
                else
                {
                    if ($content_object instanceof ComplexContentObjectSupport)
                    {
                        $url = $this->get_build_complex_url($content_object->get_id());
                        $title_short = '<a href="#" onclick="javascript:openPopup(\'' . $url . '\'); return false">' .
                             $title_short . '</a>';
                    }
                    else
                    {
                        $object_url = $this->get_component->get_complex_content_object_item_view_url(
                            $complex_content_object_item->get_id());
                        $title_short = '<a onclick="javascript:openPopup(\'' . $object_url .
                             '\'); return false;" href="#" >' . $title_short . '</a>';
                    }
                }

                return $title_short;
        }

        return parent :: render_cell($column, $complex_content_object_item);
    }

    /**
     * Returns the url for the complex content object builder
     *
     * @param int $content_object_id
     *
     * @return string
     */
    protected function get_build_complex_url($content_object_id)
    {
        return Path :: getInstance()->getBasePath(true) . 'index.php?' . Application :: PARAM_CONTEXT . '=' .
             \Chamilo\Core\Repository\Manager :: context() . '&' . Application :: PARAM_ACTION . '=' .
             \Chamilo\Core\Repository\Manager :: ACTION_BUILD_COMPLEX_CONTENT_OBJECT . '&' .
             \Chamilo\Core\Repository\Manager :: PARAM_CONTENT_OBJECT_ID . '=' . $content_object_id . '&' .
             \Chamilo\Core\Repository\Component\BuilderComponent :: PARAM_POPUP . '=1';
    }

    public function get_actions($complex_content_object_item)
    {
        $content_object = $complex_content_object_item->get_ref_object();

        $toolbar = parent :: get_actions($complex_content_object_item);

        $parent = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_content_object(
            $complex_content_object_item->get_parent());

        if ($content_object->get_type() == LearningPathItem :: class_name())
        {

            $condition = $this->get_component()->get_table_condition(__CLASS__);
            if ($condition)
            {
                $count_conditions[] = $condition;
            }

            $subselect_condition = new NotCondition(
                new EqualityCondition(ContentObject :: PROPERTY_TYPE, LearningPath :: class_name()));
            $count_conditions[] = new SubselectCondition(
                new PropertyConditionVariable(
                    ComplexContentObjectItem :: class_name(),
                    ComplexContentObjectItem :: PROPERTY_REF),
                new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_ID),
                'content_object',
                $subselect_condition);
            $count_condition = new AndCondition($count_conditions);

            $count = \Chamilo\Core\Repository\Storage\DataManager :: count_complex_content_object_items(
                ComplexContentObjectItem :: class_name(),
                $count_condition);

            if ($parent->get_version() == 'chamilo' && $count > 1)
            {
                $prerequisites = $complex_content_object_item->get_prerequisites();
                if (! empty($prerequisites))
                {
                    $toolbar->add_item(
                        new ToolbarItem(
                            Translation :: get('EditPrerequisites'),
                            Theme :: getInstance()->getCommonImagePath('Action/EditPrerequisites'),
                            $this->get_component()->get_prerequisites_url($complex_content_object_item->get_id()),
                            ToolbarItem :: DISPLAY_ICON));
                }
                else
                {
                    $toolbar->add_item(
                        new ToolbarItem(
                            Translation :: get('BuildPrerequisites'),
                            Theme :: getInstance()->getCommonImagePath('Action/BuildPrerequisites'),
                            $this->get_component()->get_prerequisites_url($complex_content_object_item->get_id()),
                            ToolbarItem :: DISPLAY_ICON));
                }
            }

            if ($this->lpi_ref_object->get_type() == Assessment :: class_name())
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation :: get('SetMasteryScore'),
                        Theme :: getInstance()->getCommonImagePath('Action/Quota'),
                        $this->get_component()->get_mastery_score_url($complex_content_object_item->get_id()),
                        ToolbarItem :: DISPLAY_ICON));
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation :: get('ConfigureAssessment'),
                        Theme :: getInstance()->getCommonImagePath('Action/Config'),
                        $this->get_component()->get_configuration_url($complex_content_object_item->get_id()),
                        ToolbarItem :: DISPLAY_ICON));
            }
        }

        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getCommonImagePath('Action/Edit'),
                $this->get_component()->get_complex_content_object_item_edit_url($complex_content_object_item->get_id()),
                ToolbarItem :: DISPLAY_ICON));

        if ($parent->get_version() == 'chamilo')
        {

            $delete_url = $this->get_component()->get_complex_content_object_item_delete_url(
                $complex_content_object_item->get_id());
            $moveup_url = $this->get_component()->get_complex_content_object_item_move_url(
                $complex_content_object_item->get_id(),
                \Chamilo\Core\Repository\Manager :: PARAM_DIRECTION_UP);
            $movedown_url = $this->get_component()->get_complex_content_object_item_move_url(
                $complex_content_object_item->get_id(),
                \Chamilo\Core\Repository\Manager :: PARAM_DIRECTION_DOWN);
            $change_parent_url = $this->get_component()->get_complex_content_object_parent_changer_url(
                $complex_content_object_item->get_id());

            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/Delete'),
                    $delete_url,
                    ToolbarItem :: DISPLAY_ICON,
                    true));
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Move', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/Move'),
                    $change_parent_url,
                    ToolbarItem :: DISPLAY_ICON));

            $allowed = $this->check_move_allowed($complex_content_object_item);

            if ($allowed["moveup"])
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation :: get('MoveUp', null, Utilities :: COMMON_LIBRARIES),
                        Theme :: getInstance()->getCommonImagePath('Action/Up'),
                        $moveup_url,
                        ToolbarItem :: DISPLAY_ICON));
            }
            else
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation :: get('MoveUpNA', null, Utilities :: COMMON_LIBRARIES),
                        Theme :: getInstance()->getCommonImagePath('Action/UpNa'),
                        null,
                        ToolbarItem :: DISPLAY_ICON));
            }

            if ($allowed["movedown"])
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation :: get('MoveDown', null, Utilities :: COMMON_LIBRARIES),
                        Theme :: getInstance()->getCommonImagePath('Action/Down'),
                        $movedown_url,
                        ToolbarItem :: DISPLAY_ICON));
            }
            else
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation :: get('MoveDownNA', null, Utilities :: COMMON_LIBRARIES),
                        Theme :: getInstance()->getCommonImagePath('Action/DownNa'),
                        null,
                        ToolbarItem :: DISPLAY_ICON));
            }
        }

        return $toolbar->as_html();
    }
}
?>