<?php
namespace Chamilo\Core\Repository\ContentObject\HotspotQuestion\Storage\DataClass;

/**
 *
 * @package repository.lib.content_object.hotspot_question
 */
use Chamilo\Libraries\Utilities\StringUtilities;

class HotspotQuestionAnswer
{

    private $answer;

    private $comment;

    private $weight;

    private $hotspot_coordinates;

    public function __construct($answer, $comment, $weight, $coords)
    {
        $this->set_answer($answer);
        $this->set_comment($comment);
        $this->set_weight($weight);
        $this->set_hotspot_coordinates($coords);
    }

    public function set_answer($answer)
    {
        $this->answer = $answer;
    }

    public function set_comment($comment)
    {
        $this->comment = $comment;
    }

    public function set_hotspot_coordinates($coords)
    {
        $this->hotspot_coordinates = $coords;
    }

    public function set_weight($weight)
    {
        $this->weight = $weight;
    }

    public function get_answer()
    {
        return $this->answer;
    }

    public function get_comment()
    {
        return $this->comment;
    }

    public function get_weight()
    {
        return $this->weight;
    }

    public function get_hotspot_coordinates()
    {
        return $this->hotspot_coordinates;
    }

    public function get_hotspot_type()
    {
        return $this->hotspot_type;
    }

    public function has_comment()
    {
        return StringUtilities::getInstance()->hasValue($this->get_comment(), true);
    }
}
