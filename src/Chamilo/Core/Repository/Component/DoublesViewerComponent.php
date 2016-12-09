<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Table\Doubles\DoublesTable;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * $Id: comparer.class.php 204 2009-11-13 12:51:30Z kariboe $
 *
 * @package repository.lib.repository_manager.component
 */

/**
 * Repository manager component which can be used to view doubles in the repository
 */
class DoublesViewerComponent extends Manager implements TableSupport
{

    private $content_object;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $id = Request :: get(self :: PARAM_CONTENT_OBJECT_ID);
        $trail = BreadcrumbTrail :: get_instance();

        $html = array();

        $html[] = $this->render_header();

        if (isset($id))
        {
            $this->content_object = $content_object = DataManager :: retrieve_by_id(ContentObject :: class_name(), $id);
            $html[] = ContentObjectRenditionImplementation :: launch(
                $content_object,
                ContentObjectRendition :: FORMAT_HTML,
                ContentObjectRendition :: VIEW_FULL,
                $this);
            $html[] = '<br />';
            $html[] = $this->get_detail_table_html();

            $params = array(self :: PARAM_CONTENT_OBJECT_ID => $this->content_object->get_id());
            $trail->add(new Breadcrumb($this->get_url($params), $this->content_object->get_title()));
        }
        else
        {
            $html[] = $this->get_full_table_html();
        }

        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    private function get_full_table_html()
    {
        $condition = $this->get_full_condition();
        $parameters = $this->get_parameters(true);
        $table = new DoublesTable($this);
        return $table->as_html();
    }

    public function get_full_condition()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_OWNER_ID),
            new StaticConditionVariable($this->get_user_id()));
        $conditions[] = new NotCondition(
            new EqualityCondition(
                new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_STATE),
                new StaticConditionVariable(ContentObject :: STATE_RECYCLED)));
        return new AndCondition($conditions);
    }

    public function get_detail_table_html()
    {
        $condition = $this->get_detail_condition();
        $parameters = $this->get_parameters(true);
        $parameters[self :: PARAM_CONTENT_OBJECT_ID] = $this->content_object->get_id();
        $table = new DoublesTable($this, true);
        return $table->as_html();
    }

    public function get_detail_condition()
    {
        $conditions = array();
        $conditions[] = $this->get_full_condition();
        $conditions[] = new NotCondition(
            new EqualityCondition(
                new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_ID),
                new StaticConditionVariable($this->content_object->get_id())));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_CONTENT_HASH),
            new StaticConditionVariable($this->content_object->get_content_hash()));

        return new AndCondition($conditions);
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CONTENT_OBJECTS)),
                Translation :: get('BrowserComponent')));
        $breadcrumbtrail->add_help('repository_doubles_viewer');
    }

    public function get_table_condition($table_class_name)
    {
        $conditions = array();
        if (isset($this->content_object))
        {
            $conditions[true] = $this->get_detail_condition();
        }

        $conditions[false] = $this->get_full_condition();
        return $conditions;
    }

    public function get_repository_browser()
    {
        return $this;
    }
}
