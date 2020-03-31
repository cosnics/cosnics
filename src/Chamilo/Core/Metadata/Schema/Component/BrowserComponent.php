<?php
namespace Chamilo\Core\Metadata\Schema\Component;

use Chamilo\Core\Metadata\Schema\Manager;
use Chamilo\Core\Metadata\Schema\Table\Schema\SchemaTable;
use Chamilo\Core\Metadata\Storage\DataClass\Schema;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Core\Metadata\Schema\Component
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class BrowserComponent extends Manager implements TableSupport
{

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    /**
     * Executes this controller
     */
    public function run()
    {
        if (!$this->get_user()->is_platform_admin())
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
        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();
        $html = array();

        $html[] = $this->buttonToolbarRenderer->render();

        $table = new SchemaTable($this);
        $html[] = $table->as_html();

        return implode(PHP_EOL, $html);
    }

    /**
     * Builds the action bar
     *
     * @return ButtonToolBarRenderer
     */
    protected function getButtonToolbarRenderer()
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar($this->get_url());
            $commonActions = new ButtonGroup();

            $commonActions->addButton(
                new Button(
                    Translation::get('Create', null, Utilities::COMMON_LIBRARIES), new FontAwesomeGlyph('plus'),
                    $this->get_url(array(self::PARAM_ACTION => self::ACTION_CREATE))
                )
            );

            $buttonToolbar->addButtonGroup($commonActions);
            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
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
        return $this->buttonToolbarRenderer->getConditions(
            array(new PropertyConditionVariable(Schema::class_name(), Schema::PROPERTY_NAME))
        );
    }
}
