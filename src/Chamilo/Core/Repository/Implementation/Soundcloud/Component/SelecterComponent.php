<?php
namespace Chamilo\Core\Repository\Implementation\Soundcloud\Component;

use Chamilo\Core\Repository\Implementation\Soundcloud\Manager;
use Chamilo\Core\Repository\Instance\Storage\DataClass\SynchronizationData;
use Chamilo\Libraries\Platform\Session\Request;

class SelecterComponent extends Manager
{

    public function run()
    {
        $id = Request :: get(Manager :: PARAM_EXTERNAL_REPOSITORY_ID);
        $object = $this->retrieve_external_repository_object($id);

        $html = array();

        $html[] = $this->render_header();
        $html[] = '<script type="text/javascript">';
        $html[] = 'window.opener.$("input[name=' . SynchronizationData :: PROPERTY_EXTERNAL_ID . ']").val("' .
             $this->get_external_repository()->get_id() . '");';
        $html[] = 'window.opener.$("input[name=' . SynchronizationData :: PROPERTY_EXTERNAL_OBJECT_ID . ']").val("' .
             $object->get_id() . '");';
        $html[] = 'window.opener.$("input#title").val("' . addslashes($object->get_title()) . '");';
        $description = preg_replace(
            '/((\\\\n)+)/',
            "$1\"+\n\"",
            preg_replace("/(\r\n|\n)/", '\\n', addslashes(nl2br($object->get_description()))));
        $html[] = 'window.opener.$("textarea[name=description]").val("' . $description . '");';
        $html[] = 'window.close();';
        $html[] = '</script>';
        $html[] = $this->render_footer();

        return implode("\n", $html);
    }
}
