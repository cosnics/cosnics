<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Document\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Document\Manager;
use Chamilo\Core\Repository\Common\Export\ContentObjectExport;
use Chamilo\Core\Repository\Common\Export\ContentObjectExportController;
use Chamilo\Core\Repository\Common\Export\ExportParameters;
use Chamilo\Core\Repository\Common\Export\Zip\ZipContentObjectExport;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;

/**
 * Will retrieve the selected publications and return a downloadable zip
 *
 * @author Minas Zilyas - Hogeschool Gent
 * @package application\weblcms\tool\document
 */
class DownloadSelectedPublicationsComponent extends Manager
{

    public function run()
    {
        $publications_ids = Request :: get('publication');

        $content_object_ids = array();
        foreach ($publications_ids as $publication_id)
        {
            $publication = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieve_content_object_publication_with_content_object(
                $publication_id);

            if ($this->is_allowed(WeblcmsRights :: VIEW_RIGHT, $publication))
            {
                $content_object_ids[] = $publication[ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID];
            }
        }

        if (count($content_object_ids) == 0)
        {
            $this->redirect(
                Translation :: get("NoFileSelected"),
                true,
                array(),
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION,
                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID));
        }

        $parameters = new ExportParameters(
            new PersonalWorkspace($this->get_user()),
            $this->get_user_id(),
            ContentObjectExport :: FORMAT_ZIP,
            $content_object_ids,
            array(),
            ZipContentObjectExport :: TYPE_FLAT);

        $exporter = ContentObjectExportController :: factory($parameters);
        $exporter->download();
    }
}