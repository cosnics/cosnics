<?php
namespace Chamilo\Core\Repository\ContentObject\Wiki\Display\Component;

use Chamilo\Core\Repository\ContentObject\Wiki\Display\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Table\ContentObject\Version\VersionTable;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: wiki_history.class.php 200 2009-11-13 12:30:04Z kariboe $
 *
 * @package repository.lib.complex_display.wiki.component
 */
class WikiHistoryComponent extends Manager
{

    private $complex_wiki_page_id;

    public function run()
    {
        if (! $this->is_allowed(VIEW_RIGHT))
        {
            throw new NotAllowedException();
        }

        $this->complex_wiki_page_id = Request :: get(self :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID);

        if ($this->complex_wiki_page_id)
        {
            $complex_wiki_page = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_complex_content_object_item(
                $this->complex_wiki_page_id,
                ComplexContentObjectItem :: class_name());
            $compare_object_ids = Request :: post(VersionTable :: DEFAULT_NAME . VersionTable :: CHECKBOX_NAME_SUFFIX);

            $html = array();

            $html[] = $this->render_header($complex_wiki_page);

            if ($compare_object_ids)
            {
                if (count($compare_object_ids) < 2)
                {
                    $this->redirect(Translation :: get('TooFewItems'), true);
                }
                $compare_object_id = $compare_object_ids[0];
                $compare_version_id = $compare_object_ids[1];

                $compare_object = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_content_object(
                    $compare_object_id);

                $html[] = $compare_object->get_difference($compare_version_id);
            }
            else
            {
                $wiki_page = $complex_wiki_page->get_ref_object();
                $version_parameters = $this->get_parameters();
                $version_parameters[self :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->complex_wiki_page_id;

                $version_browser = new VersionTable(
                    $this,
                    $version_parameters,
                    new EqualityCondition(ContentObject :: PROPERTY_OBJECT_NUMBER, $wiki_page->get_object_number()));
                $actions = new TableFormActions(__NAMESPACE__);
                $actions->add_form_action(
                    new TableFormAction(
                        array(self :: PARAM_ACTION => self :: ACTION_COMPARE),
                        Translation :: get('CompareSelected'),
                        false));
                $version_browser->set_form_actions($actions);

                $html[] = '<div class="wiki-pane-content-title">' . Translation :: get('RevisionHistory') . ': ' .
                     $wiki_page->get_title() . '</div>';
                $html[] = '<div class="wiki-pane-content-subtitle">' .
                     Translation :: get('From', null, Utilities :: COMMON_LIBRARIES) . ' ' .
                     $this->get_root_content_object()->get_title() . '</div>';
                $html[] = '<div class="wiki-pane-content-history">';
                $html[] = $version_browser->as_html();
                $html[] = ResourceManager :: get_instance()->get_resource_html(
                    Path :: getInstance()->namespaceToFullPath('Chamilo\Core\Repository\\', true) .
                         'Resources/Javascript/Repository.js');
                $html[] = '<div class="clear"></div>';
                $html[] = '</div>';
            }

            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
        else
        {
            $this->redirect(null, false, array(self :: PARAM_ACTION => self :: ACTION_VIEW_WIKI));
        }
    }

    public function count_content_object_versions_resultset($condition = null)
    {
        return \Chamilo\Core\Repository\Storage\DataManager :: count_content_objects(
            ContentObject :: class_name(),
            $condition);
    }

    public function retrieve_content_object_versions_resultset($condition = null, $order_by = array(), $offset = 0,
        $max_objects = -1)
    {
        return \Chamilo\Core\Repository\Storage\DataManager :: retrieve_content_objects(
            ContentObject :: class_name(),
            $condition);
    }

    public function get_content_object_viewing_url($content_object)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_VIEW_WIKI_PAGE,
                self :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->complex_wiki_page_id,
                self :: PARAM_WIKI_VERSION_ID => $content_object->get_id()));
    }

    public function get_content_object_deletion_url($content_object, $type = null)
    {
        $delete_allowed = \Chamilo\Core\Repository\Storage\DataManager :: content_object_deletion_allowed(
            $content_object,
            $type);

        if (! $delete_allowed)
        {
            return null;
        }

        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_VERSION_DELETE,
                self :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->complex_wiki_page_id,
                self :: PARAM_WIKI_VERSION_ID => $content_object->get_id()));
    }

    public function get_content_object_revert_url($content_object)
    {
        $revert_allowed = \Chamilo\Core\Repository\Storage\DataManager :: content_object_revert_allowed($content_object);

        if (! $revert_allowed)
        {
            return null;
        }

        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_VERSION_REVERT,
                self :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->complex_wiki_page_id,
                self :: PARAM_WIKI_VERSION_ID => $content_object->get_id()));
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail = $this->get_breadcrumbtrail();
    }
}
