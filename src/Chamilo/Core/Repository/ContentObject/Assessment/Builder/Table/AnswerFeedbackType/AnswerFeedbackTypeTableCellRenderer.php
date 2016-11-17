<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Builder\Table\AnswerFeedbackType;

use Chamilo\Core\Repository\ContentObject\Assessment\Builder\Manager;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Configuration;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package core\repository\content_object\assessment\builder
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class AnswerFeedbackTypeTableCellRenderer extends DataClassTableCellRenderer implements 
    TableCellRendererActionsColumnSupport
{

    /**
     *
     * @see \libraries\format\DataClassTableCellRenderer::render_cell()
     */
    public function render_cell($column, $complex_content_object_item)
    {
        $content_object = $complex_content_object_item->get_ref_object();
        
        switch ($column->get_name())
        {
            case AnswerFeedbackTypeTableColumnModel::PROPERTY_TYPE :
                return $content_object->get_icon_image(Theme::ICON_MINI);
            case ContentObject::PROPERTY_TITLE :
                $title = parent::render_cell($column, $content_object);
                return StringUtilities::getInstance()->truncate($title, 53, false);
            case AnswerFeedbackTypeTableColumnModel::PROPERTY_FEEDBACK_TYPE :
                return Theme::getInstance()->getImage(
                    'AnswerFeedbackType/' . $complex_content_object_item->get_show_answer_feedback(), 
                    'png', 
                    Configuration::answer_feedback_string($complex_content_object_item->get_show_answer_feedback()), 
                    null, 
                    ToolbarItem::DISPLAY_ICON, 
                    false, 
                    $this->get_component()->get_root_content_object()->package());
        }
        
        return parent::render_cell($column, $complex_content_object_item);
    }

    /**
     *
     * @see \libraries\format\TableCellRendererActionsColumnSupport::get_actions()
     */
    public function get_actions($complex_content_object_item)
    {
        $toolbar = new Toolbar();
        $context = $this->get_component()->get_root_content_object()->package();
        
        $types = array(
            Configuration::ANSWER_FEEDBACK_TYPE_NONE, 
            Configuration::ANSWER_FEEDBACK_TYPE_GIVEN, 
            Configuration::ANSWER_FEEDBACK_TYPE_GIVEN_CORRECT, 
            Configuration::ANSWER_FEEDBACK_TYPE_GIVEN_WRONG, 
            Configuration::ANSWER_FEEDBACK_TYPE_CORRECT, 
            Configuration::ANSWER_FEEDBACK_TYPE_WRONG, 
            Configuration::ANSWER_FEEDBACK_TYPE_ALL);
        
        foreach ($types as $type)
        {
            if ($complex_content_object_item->get_show_answer_feedback() != $type)
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Configuration::answer_feedback_string($type), 
                        Theme::getInstance()->getImagePath($context, 'AnswerFeedbackType/' . $type), 
                        $this->get_component()->get_url(
                            array(
                                Manager::PARAM_ANSWER_FEEDBACK_TYPE => $type, 
                                Manager::PARAM_COMPLEX_QUESTION_ID => $complex_content_object_item->get_id())), 
                        ToolbarItem::DISPLAY_ICON));
            }
            else
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Configuration::answer_feedback_string($type), 
                        Theme::getInstance()->getImagePath($context, 'AnswerFeedbackType/' . $type . 'Na'), 
                        null, 
                        ToolbarItem::DISPLAY_ICON));
            }
        }
        
        return $toolbar->as_html();
    }
}
