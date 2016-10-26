<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Common\Export\Cpo;

use Chamilo\Core\Repository\Common\Export\ContentObjectExport;
use Chamilo\Core\Repository\ContentObject\LearningPath\Common\Export\CpoExportImplementation;

class CpoDefaultExportImplementation extends CpoExportImplementation
{

    public function render()
    {
        ContentObjectExport :: launch($this);
        if ($this->get_content_object()->get_path())
        {
            $this->get_context()->add_files(
                $this->get_content_object()->get_full_path(), 
                'scorm/' . basename(rtrim($this->get_content_object()->get_full_path(), '/')));
        }
    }
}
