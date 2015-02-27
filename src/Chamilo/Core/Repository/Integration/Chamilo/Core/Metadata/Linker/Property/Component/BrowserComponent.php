<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Linker\Property\Component;

use Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Linker\Property\Manager;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Linker\Property\Storage\DataClass\ContentObjectPropertyRelMetadataElement;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Linker\Property\Table\ContentObjectPropertyRelMetadataElement\ContentObjectPropertyRelMetadataElementTable;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBarRenderer;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * This component shows the table for the ContentObjectPropertyRelMetadataElement data class
 *
 * @package repository\content_object_property_metadata_linker
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class BrowserComponent extends Manager implements TableSupport
{

    /**
     * The action bar of this browser
     *
     * @var ActionBarRenderer
     */
    private $action_bar;

    /**
     * Executes this controller
     */
    public function run()
    {
        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $html = array();

        $html[] = $this->render_header();
        $html[] = $this->as_html();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * Renders this components output as html
     */
    public function as_html()
    {
        $html = array();

        $this->action_bar = $this->get_action_bar();
        $html[] = $this->action_bar->as_html();

        $table = new ContentObjectPropertyRelMetadataElementTable($this);
        $html[] = $table->as_html();

        return implode(PHP_EOL, $html);
    }

    /**
     * Builds the action bar
     *
     * @return ActionBarRenderer
     */
    protected function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->set_search_url($this->get_url());

        $action_bar->add_common_action(
            new ToolbarItem(
                Translation :: get('Create', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getCommonImagePath() . 'action_create.png',
                $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE))));

        return $action_bar;
    }

    /**
     * Returns the condition
     *
     * @param string $table_class_name
     *
     * @return \libraries\storage\Condition
     */
    public function get_table_condition($table_class_name)
    {
        return $this->action_bar->get_conditions(
            array(
                new PropertyConditionVariable(
                    ContentObjectPropertyRelMetadataElement :: class_name(),
                    ContentObjectPropertyRelMetadataElement :: PROPERTY_PROPERTY_NAME)),
            new PropertyConditionVariable(
                ContentObjectPropertyRelMetadataElement :: class_name(),
                ContentObjectPropertyRelMetadataElement :: PROPERTY_CONTENT_OBJECT_TYPE));
    }
}
