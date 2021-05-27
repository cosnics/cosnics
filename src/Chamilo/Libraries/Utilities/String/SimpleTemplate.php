<?php
namespace Chamilo\Libraries\Utilities\String;

/**
 * Class used to process simple text templates.
 * Mostly used to replace {$variables} in strings.
 *
 * @todo : remove that when we move to a templating system
 * @todo : could be more efficient to do an include or eval
 * @copyright (c) 2011 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author lopprecht
 */
class SimpleTemplate
{

    private static $instance = null;

    private $template_callback_context = [];

    private $glue;

    public function __construct($glue = PHP_EOL)
    {
        $this->glue = $glue;
    }

    /**
     * Process $template once for each entries in $vars.
     * Join result with $glue.
     *
     * @param string|array $template
     * @param array $vars
     * @param string $glue
     *
     * @return string
     */
    public static function all($template, $vars, $glue = null)
    {
        $instance = self::getInstance();

        return $instance->process_all($template, $vars, $glue);
    }

    /**
     * Replaces $name=>$value pairs comming from $vars from $template.
     *
     * @param string|array $template
     * @param array $vars
     *
     * @return string
     */
    public static function ex($template, $vars)
    {
        $instance = self::getInstance();

        return $instance->process_one($template, $vars);
    }

    /**
     *
     * @return SimpleTemplate
     */
    public static function getInstance()
    {
        if (empty(self::$instance))
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function process_all($template, $items, $glue = null)
    {
        $result = [];
        foreach ($items as $item)
        {
            $result[] = $this->process_one($template, $item);
        }
        $glue = is_null($glue) ? $this->glue : $glue;

        return implode($glue, $result);
    }

    public function process_one($template, $vars)
    {
        if (is_array($template))
        {
            $template = implode($this->glue, $template);
        }

        $pattern = '/\{\$[a-zA-Z_][a-zA-Z0-9_]*\}/';
        $this->template_callback_context = $vars;
        $result = preg_replace_callback($pattern, array($this, 'process_template_callback'), $template);
        $this->template_callback_context = [];

        return $result;
    }

    private function process_template_callback($matches)
    {
        $vars = $this->template_callback_context;
        $name = trim($matches[0], '{$}');
        $result = isset($vars[$name]) ? $vars[$name] : '';
        if (is_array($result))
        {
            $result = implode($this->glue, $result);
        }

        return $result;
    }
}
