<?php
namespace Chamilo\Core\Repository\ContentObject\ForumTopic\Common\Export\Cpo;

use Chamilo\Core\Repository\Common\Export\ContentObjectExport;
use Chamilo\Core\Repository\ContentObject\ForumTopic\Common\Export\CpoExportImplementation;

/**
 *
 * @author Maarten Volckaert - Hogschool Gent
 */
class CpoDefaultExportImplementation extends CpoExportImplementation
{

    public function render()
    {
        ContentObjectExport::launch($this);
        // @TODO: Add all posts to the export cpo file.
    }
}
