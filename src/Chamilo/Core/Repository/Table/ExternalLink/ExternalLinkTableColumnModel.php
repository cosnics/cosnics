<?php
namespace Chamilo\Core\Repository\Table\ExternalLink;

use Chamilo\Core\Repository\Instance\Storage\DataClass\Instance;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

/**
 * @package Chamilo\Core\Repository\Table\ExternalLink
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ExternalLinkTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{

    public function initialize_columns()
    {
        $glyph = new FontAwesomeGlyph('link', array(), null, 'fas');
        $this->add_column(
            new DataClassPropertyTableColumn(
                Instance::class_name(), Instance::PROPERTY_IMPLEMENTATION, $glyph->render()
            )
        );
        $this->add_column(
            new DataClassPropertyTableColumn(
                Instance::class_name(), Instance::PROPERTY_TITLE
            )
        );
    }
}
