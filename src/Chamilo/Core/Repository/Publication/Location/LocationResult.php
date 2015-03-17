<?php
namespace Chamilo\Core\Repository\Publication\Location;

use Chamilo\Core\Repository\Publication\LocationSupport;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\SortableTableFromArray;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package core\repository\publication
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class LocationResult
{

    /**
     *
     * @var \HTML_QuickForm_Action
     */
    private $parent;

    /**
     *
     * @var string
     */
    private $context;

    /**
     *
     * @var \libraries\format\SortableTableFromArray
     */
    private $table;

    private $table_data;

    /**
     *
     * @param \HTML_QuickForm_Action $parent
     * @param string $context
     */
    function __construct($parent, $context)
    {
        $this->parent = $parent;
        $this->context = $context;
        $this->table_data = array();
    }

    /**
     *
     * @return \libraries\format\FormValidator
     */
    public function get_parent()
    {
        return $this->parent;
    }

    /**
     *
     * @param \libraries\format\FormValidator $parent
     */
    public function set_parent($parent)
    {
        $this->parent = $parent;
    }

    public function get_context()
    {
        return $this->context;
    }

    /**
     *
     * @param \core\repository\publication\Locations $context
     */
    public function set_context($context)
    {
        $this->context = $context;
    }

    /**
     *
     * @return \libraries\format\SortableTableFromArray
     */
    public function get_table()
    {
        return $this->table;
    }

    /**
     *
     * @param LocationSupport $location
     * @param \core\repository\ContentObject $content_object
     * @param mixed $success
     */
    public function add(LocationSupport $location, ContentObject $content_object, $result)
    {
        $data_row = array();

        foreach ($this->get_location($location) as $cell)
        {
            $data_row[] = $cell;
        }

        $data_row[] = $content_object->get_title();

        if ($result)
        {
            $link = $this->get_link($location, $result);

            if (! empty($link))
            {
                $data_row[] = Theme :: getInstance()->getCommonImage(
                    'action_right',
                    'png',
                    Translation :: get('ViewPublication'),
                    $link,
                    ToolbarItem :: DISPLAY_ICON);
            }
            else
            {
                $data_row[] = '';
            }

            $data_row[] = Theme :: getInstance()->getCommonImage(
                'Status/ConfirmMini',
                'png',
                Translation :: get('PublicationCreated'),
                null,
                ToolbarItem :: DISPLAY_ICON);
        }
        else
        {
            $data_row[] = '';
            $data_row[] = Theme :: getInstance()->getCommonImage(
                'Status/ErrorMini',
                'png',
                Translation :: get('PublicationFailed'),
                null,
                ToolbarItem :: DISPLAY_ICON);
        }

        $this->table_data[] = $data_row;
    }

    public function as_html()
    {
        $this->table = new SortableTableFromArray($this->table_data, 0, count($this->table_data));
        $table_header = $this->table->getHeader();

        $headers = $this->get_header();

        foreach ($headers as $key => $header)
        {
            $this->table->set_header($key, $header, false);
        }
        $key ++;

        $this->table->set_header($key ++, Translation :: get('ContentObject'), false);

        $table_header->setColAttributes($key, 'class="action"');
        $this->table->set_header(
            $key ++,
            Theme :: getInstance()->getCommonImage(
                'action_search',
                'png',
                Translation :: get('ViewPublication'),
                null,
                ToolbarItem :: DISPLAY_ICON),
            false);

        $table_header->setColAttributes($key, 'class="action"');
        $this->table->set_header(
            $key ++,
            Theme :: getInstance()->getCommonImage(
                'Status/NormalMini',
                'png',
                Translation :: get('Result'),
                null,
                ToolbarItem :: DISPLAY_ICON),
            false);

        return $this->table->as_html();
    }

    /**
     *
     * @return string[]
     */
    abstract public function get_header();

    /**
     *
     * @param LocationSupport $location
     * @return string[]
     */
    abstract public function get_location(LocationSupport $location);

    /**
     *
     * @param LocationSupport $location
     * @param mixed $result
     * @return string
     */
    abstract public function get_link(LocationSupport $location, $result);
}