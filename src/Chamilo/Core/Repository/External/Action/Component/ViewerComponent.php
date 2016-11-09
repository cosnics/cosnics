<?php
namespace Chamilo\Core\Repository\External\Action\Component;

use Chamilo\Core\Repository\External\Action\Manager;
use Chamilo\Core\Repository\External\ExternalObjectDisplay;
use Chamilo\Core\Repository\Instance\Storage\DataClass\SynchronizationData;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class ViewerComponent extends Manager implements DelegateComponent
{

    public function run()
    {
        $id = Request :: get(\Chamilo\Core\Repository\External\Manager :: PARAM_EXTERNAL_REPOSITORY_ID);

        if ($id)
        {
            $object = $this->retrieve_external_repository_object($id);
            BreadcrumbTrail :: getInstance()->add(new Breadcrumb(null, $object->get_title()));

            $display = ExternalObjectDisplay :: factory($object);

            if (! $object->is_importable())
            {
                switch ($object->get_synchronization_status())
                {
                    case SynchronizationData :: SYNC_STATUS_INTERNAL :
                        $this->display_warning_message(
                            Translation :: get('ExternalObjectSynchronizationUpdateInternal'));
                        break;
                    case SynchronizationData :: SYNC_STATUS_EXTERNAL :
                        if ($object->is_editable())
                        {
                            $this->display_warning_message(
                                Translation :: get('ExternalObjectSynchronizationUpdateExternal'));
                        }
                        break;
                    case SynchronizationData :: SYNC_STATUS_CONFLICT :
                        $this->display_warning_message(Translation :: get('ExternalObjectSynchronizationConflict'));
                        break;
                    case SynchronizationData :: SYNC_STATUS_IDENTICAL :
                        $this->display_message(Translation :: get('ExternalObjectSynchronizationIdentical'));
                        break;
                    case SynchronizationData :: SYNC_STATUS_ERROR :
                        $this->display_warning_message(Translation :: get('ExternalObjectSynchronizationError'));
                        break;
                }
            }

            $html = array();

            $html[] = $this->render_header();
            $html[] = $display->as_html();

            $toolbar = new Toolbar();
            $toolbar_item = new ToolbarItem(
                Translation :: get('Back', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getCommonImagePath('Action/Prev'),
                'javascript:history.back();');
            $toolbar->add_item($toolbar_item);

            $type_actions = $this->get_external_repository_object_actions($object);

            foreach ($type_actions as $type_action)
            {
                $toolbar_item = new ToolbarItem(
                    Translation :: get($type_action->get_label()),
                    $type_action->get_image(),
                    $type_action->get_href(),
                    ToolbarItem :: DISPLAY_ICON_AND_LABEL,
                    $type_action->get_confirmation(),
                    null,
                    $type_action->get_target(),
                    $type_action->get_confirm_message());
                $toolbar->add_item($toolbar_item);
            }

            $html[] = '<br/>' . $toolbar->as_html();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
        else
        {
            return $this->display_error_page(
                htmlentities(
                    Translation :: get(
                        'NoObjectSelected',
                        array('OBJECT' => Translation :: get('ExternalObject')),
                        Utilities :: COMMON_LIBRARIES)));
        }
    }
}
