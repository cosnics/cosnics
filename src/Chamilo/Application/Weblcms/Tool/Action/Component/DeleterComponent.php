<?php
namespace Chamilo\Application\Weblcms\Tool\Action\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Action\Manager;
use Chamilo\Core\Repository\ContentObject\Introduction\Storage\DataClass\Introduction;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: delete.class.php 216 2009-11-13 14:08:06Z kariboe $
 *
 * @package application.lib.weblcms.tool.component
 */
class DeleterComponent extends Manager implements DelegateComponent
{

    public function run()
    {
        $publication_ids = $this->getRequest()->get(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID);

        if (!isset($publication_ids))
        {
            throw new NoObjectSelectedException(
                Translation::getInstance()->getTranslation(
                    'ContentObjectPublication', array(), 'Chamilo\Application\Weblcms'
                )
            );
        }

        if (!is_array($publication_ids))
        {
            $publication_ids = array($publication_ids);
        }

        $failures = 0;

        foreach ($publication_ids as $pid)
        {
            $publication = \Chamilo\Application\Weblcms\Storage\DataManager:: retrieve_by_id(
                ContentObjectPublication:: class_name(),
                $pid
            );

            if(empty($publication)) {
                $failures ++;
                break;
            }

            $content_object = $publication->get_content_object();

            if ($content_object->get_type() == Introduction:: class_name())
            {
                $publication->ignore_display_order();
            }

            if ($this->is_allowed(WeblcmsRights :: DELETE_RIGHT, $publication))
            {
                $publication->delete();
            }
            else
            {
                $failures ++;
            }
        }
        if ($failures == 0)
        {
            if (count($publication_ids) > 1)
            {
                $message = htmlentities(Translation:: get('ContentObjectPublicationsDeleted'));
            }
            else
            {
                $message = htmlentities(Translation:: get('ContentObjectPublicationDeleted'));
            }
        }
        else
        {
            $message = htmlentities(Translation:: get('ContentObjectPublicationsNotDeleted'));
        }

        $this->redirect(
            $message,
            $failures > 0,
            array(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID => null, 'tool_action' => null)
        );
    }
}
