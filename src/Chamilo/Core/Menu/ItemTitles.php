<?php
namespace Chamilo\Core\Menu;

use Chamilo\Core\Menu\Storage\DataClass\ItemTitle;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\ResultSet\ArrayResultSet;

/**
 *
 * @package Chamilo\Core\Menu
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ItemTitles
{

    private $titles_by_isocode = array();

    private $titles = array();

    public function __construct($titles = array())
    {
        if ($titles instanceof ArrayResultSet)
        {
            while ($title = $titles->next_result())
            {
                $this->titles[] = $title;
                $this->titles_by_isocode[$title->get_isocode()] = $title;
            }
        }
    }

    /**
     *
     * @return the $titles_by_isocode
     */
    public function get_titles_by_isocode()
    {
        return $this->titles_by_isocode;
    }

    /**
     *
     * @param field_type $titles_by_isocode
     */
    public function set_titles_by_isocode($titles_by_isocode)
    {
        $this->titles_by_isocode = $titles_by_isocode;
    }

    /**
     *
     * @return the $titles
     */
    public function get_titles()
    {
        return $this->titles;
    }

    /**
     *
     * @param field_type $titles
     */
    public function set_titles($titles)
    {
        $this->titles = $titles;
    }

    public function get_title_by_isocode($isocode)
    {
        return $this->titles_by_isocode[$isocode];
    }

    public function get_translation($isocode, $fallback = true)
    {
        $title = $this->get_title_by_isocode($isocode);
        if (! $title instanceof ItemTitle && $fallback)
        {
            $title = $this->get_title(0);
        }
        if (! $title instanceof ItemTitle)
        {
            return '';
        }
        return $title->get_title();
    }

    public function get_current_translation()
    {
        return $this->get_translation(Translation::getInstance()->getLanguageIsocode());
    }

    public function get_title($sort)
    {
        return $this->titles[$sort];
    }

    public function add($title)
    {
        $this->titles[] = $title;
        $this->titles_by_isocode[$title->get_isocode()] = $title;
    }
}
