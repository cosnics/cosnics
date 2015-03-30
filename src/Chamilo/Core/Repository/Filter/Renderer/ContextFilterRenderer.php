<?php
namespace Chamilo\Core\Repository\Filter\Renderer;

use Chamilo\Core\Repository\Filter\FilterData;
use Chamilo\Core\Repository\Filter\FilterRenderer;

/**
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class ContextFilterRenderer extends FilterRenderer
{

    /**
     *
     * @var int
     */
    private $user_id;

    /**
     *
     * @var string[]
     */
    private $content_object_types;

    /**
     *
     * @param \core\repository\filter\FilterData $filter_data
     * @param int $user_id
     * @param string[] $content_object_types
     */
    public function __construct(FilterData $filter_data, $user_id, $content_object_types)
    {
        parent :: __construct($filter_data);
        
        $this->user_id = $user_id;
        $this->content_object_types = $content_object_types;
    }

    /**
     *
     * @return int
     */
    public function get_user_id()
    {
        return $this->user_id;
    }

    /**
     *
     * @param int $user_id
     */
    public function set_user_id($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     *
     * @return string[]
     */
    public function get_content_object_types()
    {
        return $this->content_object_types;
    }

    /**
     *
     * @param string[] $content_object_types
     */
    public function set_content_object_types($content_object_types)
    {
        $this->content_object_types = $content_object_types;
    }
}