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

    public function initializeColumns()
    {
        $glyph = new FontAwesomeGlyph('link', [], null, 'fas');
        $this->addColumn(
            new DataClassPropertyTableColumn(
                Instance::class, Instance::PROPERTY_IMPLEMENTATION, $glyph->render()
            )
        );
        $this->addColumn(
            new DataClassPropertyTableColumn(
                Instance::class, Instance::PROPERTY_TITLE
            )
        );
    }
}
