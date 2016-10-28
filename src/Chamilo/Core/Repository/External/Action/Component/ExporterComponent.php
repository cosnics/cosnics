<?php
namespace Chamilo\Core\Repository\External\Action\Component;

use Chamilo\Core\Repository\External\Action\Manager;
use Chamilo\Core\Repository\External\Table\Export\ExportTable;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class ExporterComponent extends Manager implements TableSupport
{

    public function run()
    {
        $external_repository_id = Request :: get(
            \Chamilo\Core\Repository\External\Manager :: PARAM_EXTERNAL_REPOSITORY_ID);
        if (isset($external_repository_id))
        {
            $object = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
                ContentObject :: class_name(),
                $external_repository_id);
            $success = $this->export_external_repository_object($object);
        }
        else
        {
            $html = array();

            $html[] = $this->render_header();
            $table = new ExportTable($this);
            $html[] = $table->as_html();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    /*
     * (non-PHPdoc) @see common\libraries.NewObjectTableSupport::get_object_table_condition()
     */
    public function get_table_condition($object_table_class_name)
    {
        $conditions = array();
        $type_conditions = array();
        $types = array(\Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File :: class_name());

        foreach ($types as $type)
        {
            $type_conditions[] = new EqualityCondition(
                new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_TYPE),
                new StaticConditionVariable($type));
        }

        $conditions[] = new OrCondition($type_conditions);

        if ($this->get_parent()->get_content_object_type_conditions())
        {
            $conditions[] = $this->get_parent()->get_content_object_type_conditions();
        }

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_OWNER_ID),
            new StaticConditionVariable($this->get_user_id()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_STATE),
            new StaticConditionVariable(ContentObject :: STATE_NORMAL));

        return new AndCondition($conditions);
    }
}
