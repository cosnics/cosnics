<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Builder\Table\AnswerFeedbackType;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package core\repository\content_object\assessment\builder
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class AnswerFeedbackTypeTableColumnModel extends DataClassTableColumnModel implements 
    TableColumnModelActionsColumnSupport
{
    const PROPERTY_TYPE = 'Type';
    const PROPERTY_FEEDBACK_TYPE = 'FeedbackType';

    /**
     */
    public function initialize_columns()
    {
        $type_image = Theme::getInstance()->getCommonImage(
            'Action/Category', 
            'png', 
            Translation::get(self::PROPERTY_TYPE), 
            null, 
            ToolbarItem::DISPLAY_ICON);
        
        $this->add_column(new StaticTableColumn(self::PROPERTY_TYPE, $type_image));
        $this->add_column(
            new DataClassPropertyTableColumn(ContentObject::class_name(), ContentObject::PROPERTY_TITLE, null, false));
        
        $feedback_type_image = Theme::getInstance()->getImage(
            'AnswerFeedbackType/Logo', 
            'png', 
            Translation::get(self::PROPERTY_FEEDBACK_TYPE), 
            null, 
            ToolbarItem::DISPLAY_ICON, 
            false, 
            'Chamilo/Core/Repository/ContentObject/Assessment');
        
        $this->add_column(new StaticTableColumn(self::PROPERTY_FEEDBACK_TYPE, $feedback_type_image));
    }
}
