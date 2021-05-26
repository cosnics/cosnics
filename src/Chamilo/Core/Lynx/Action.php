<?php
namespace Chamilo\Core\Lynx;

use Chamilo\Configuration\Package\Storage\DataClass\Package;
use Chamilo\Libraries\Format\MessageLogger;
use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;

/**
 * Abstract class that describes an action for a package.
 * Makes use of the message logger to log messages.
 *
 * @author Sven Vanpoucke
 */
abstract class Action extends MessageLogger
{

    private $package;

    /**
     *
     * @var string
     */
    private $result;

    /**
     * Constructor Initializes the source action
     *
     * @param $source_type String
     */
    public function __construct($context)
    {
        parent::__construct();
        $this->package = Package::get($context);
    }

    /**
     * Sets the action as failed for a given block with a given message
     *
     * @param $type String
     * @param $error_message String
     *
     * @return false
     */
    public function action_failed($title, $image, $error_message = null)
    {
        $this->add_result($this->process_result($title, $image, $error_message, self::TYPE_ERROR));

        return false;
    }

    /**
     * Sets the action for a given block as success with a given message
     *
     * @param $type String
     * @param $message String
     *
     * @return true
     */
    public function action_successful($title, $image, $message = null)
    {
        $this->add_result($this->process_result($title, $image, $message));

        return true;
    }

    /**
     *
     * @param string $result
     */
    public function add_result($result)
    {
        $this->result[] = $result;
    }

    /**
     * Returns the context
     *
     * @return string
     */
    public function get_context()
    {
        return $this->package->get_context();
    }

    public function get_package()
    {
        return $this->package;
    }

    /**
     *
     * @return string
     */
    public function get_result($as_string = false)
    {
        if ($as_string)
        {
            return implode(PHP_EOL, $this->result);
        }
        else
        {
            return $this->result;
        }
    }

    /**
     *
     * @param string $result
     */
    public function set_result($result)
    {
        $this->result = $result;
    }

    /**
     * Processes a result for a given block
     *
     * @param $type String
     */
    public function process_result($title, $image, $final_message = null, $final_message_type = self::TYPE_CONFIRM)
    {
        if ($final_message)
        {
            $this->add_message($final_message, $final_message_type);
        }

        $html[] = '<div class="panel panel-default">';
        $html[] = '<div class="panel-heading">';
        $html[] = '<h3 class="panel-title">';

        if ($image instanceof InlineGlyph)
        {
            $html[] = $image->render();
        }
        else
        {
            $html[] = '<img src="' . $image . '" />';
        }

        $html[] = ' ' . $title . '</h3>';
        $html[] = '</div>';

        $html[] = '<div class="panel-body">';
        $html[] = $this->render();
        $html[] = '</div>';

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}
