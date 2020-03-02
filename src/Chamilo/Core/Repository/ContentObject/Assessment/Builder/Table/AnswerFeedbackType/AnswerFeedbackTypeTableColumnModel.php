<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Builder\Table\AnswerFeedbackType;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
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
class AnswerFeedbackTypeTableColumnModel extends DataClassTableColumnModel
    implements TableColumnModelActionsColumnSupport
{
    const PROPERTY_FEEDBACK_TYPE = 'FeedbackType';

    const PROPERTY_TYPE = 'Type';

    /**
     */
    public function initialize_columns()
    {
        $glyph = new FontAwesomeGlyph('folder', array(), Translation::get(self::PROPERTY_TYPE));

        $this->add_column(new StaticTableColumn(self::PROPERTY_TYPE, $glyph->render()));
        $this->add_column(
            new DataClassPropertyTableColumn(ContentObject::class_name(), ContentObject::PROPERTY_TITLE, false)
        );

        $feedback_type_image = Theme::getInstance()->getImage(
            'AnswerFeedbackType/Logo', 'png', Translation::get(self::PROPERTY_FEEDBACK_TYPE), null,
            ToolbarItem::DISPLAY_ICON, false, 'Chamilo/Core/Repository/ContentObject/Assessment'
        );

        $this->add_column(new StaticTableColumn(self::PROPERTY_FEEDBACK_TYPE, $feedback_type_image));
    }
}
