<?php
namespace Chamilo\Core\Repository\ContentObject\Hotpotatoes\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;
use Chamilo\Libraries\File\Compression\Filecompression;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\String\Text;

/**
 *
 * @package repository.lib.content_object.hotpotatoes
 */

/**
 * This class represents an open question
 */
class Hotpotatoes extends ContentObject implements Versionable
{
    const PROPERTY_PATH = 'path';
    const PROPERTY_MAXIMUM_ATTEMPTS = 'max_attempts';

    public static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class_name(), true);
    }

    public static function get_assessment_type_name()
    {
        return Translation::get('HotPotatoes');
    }

    public static function get_additional_property_names()
    {
        return array(self::PROPERTY_PATH, self::PROPERTY_MAXIMUM_ATTEMPTS);
    }
    const TYPE_HOTPOTATOES = 3;

    public function get_assessment_type()
    {
        return self::TYPE_HOTPOTATOES;
    }

    public function get_maximum_score()
    {
        // return WeblcmsDataManager :: getInstance()->get_maximum_score($this);
        return 100;
    }

    public function get_maximum_attempts()
    {
        return $this->get_additional_property(self::PROPERTY_MAXIMUM_ATTEMPTS);
    }

    public function set_maximum_attempts($value)
    {
        $this->set_additional_property(self::PROPERTY_MAXIMUM_ATTEMPTS, $value);
    }

    public function get_path()
    {
        return $this->get_additional_property(self::PROPERTY_PATH);
    }

    public function set_path($path)
    {
        return $this->set_additional_property(self::PROPERTY_PATH, $path);
    }

    public function get_full_path()
    {
        return Path::getInstance()->getPublicStoragePath(Hotpotatoes::package()) . $this->get_owner_id() . '/' .
             $this->get_path();
    }

    public function get_full_url()
    {
        return Path::getInstance()->getPublicStoragePath(Hotpotatoes::package(), true) . $this->get_owner_id() . '/' .
             $this->get_path();
    }

    public function delete($only_version = false)
    {
        if ($only_version)
        {
            $this->delete_file();
        }

        return parent::delete($only_version);
    }

    public function delete_file()
    {
        if (Text::is_valid_path($this->get_path()))
        {
            $dir = dirname($this->get_full_path());
            Filesystem::remove($dir);
        }
    }

    public function add_javascript($postback_url, $goback_url, $tracker_id)
    {
        $content = $this->read_file_content();
        $js_content = $this->replace_javascript($content, $postback_url, $goback_url, $tracker_id);
        $path = $this->write_file_content($js_content);

        return $path;
    }

    private function read_file_content()
    {
        $full_file_path = $this->get_full_path();

        if (is_file($full_file_path))
        {
            if (! ($fp = fopen(urldecode($full_file_path), "r")))
            {
                return "";
            }
            $contents = fread($fp, filesize($full_file_path));
            fclose($fp);
            return $contents;
        }
    }

    private function write_file_content($content)
    {
        $full_file_path = $this->get_full_path() . '.t.htm';
        $full_web_path = $this->get_full_url() . '.t.htm';
        Filesystem::remove($full_file_path);

        if (($fp = fopen(urldecode($full_file_path), "w")))
        {
            fwrite($fp, $content);
            fclose($fp);
        }

        return $full_web_path;
    }

    private function replace_javascript($content, $postback_url, $goback_url, $tracker_id)
    {
        $mit = "function Finish(){";
        $js_content = "var SaveScoreVariable = 0; // This variable included by Chamilo System\n" .
             "function mySaveScore() // This function included by Chamilo System\n" . "{\n" .
             "   if (SaveScoreVariable==0)\n" . "		{\n" . "			SaveScoreVariable = 1;\n" .
             "			var result=jQuery.ajax({type: 'POST', url:'" . $postback_url . "', data: {id: " . $tracker_id .
             ", score: Score}, async: false}).responseText;\n";
        // " alert(result);";

        if ($goback_url)
        {
            $js_content .= "		if (C.ie)\n" . "			{\n" . // " window.alert(Score);\n".
"				document.parent.location.href=\"" . $goback_url . "\"\n" . "			}\n" . "			else\n" . "			{\n" . // "
                                                                                                                    // window.alert(Score);\n".
                "				window.parent.location.href=\"" . $goback_url . "\"\n" . "			}\n";
        }

        $js_content .= "		}\n" . " }\n" . "// Must be included \n" . "function Finish(){\n" . " mySaveScore();";
        $newcontent = str_replace($mit, $js_content, $content);
        $prehref = "<!-- BeginTopNavButtons -->";
        $posthref = "<!-- BeginTopNavButtons --><!-- edited by Chamilo -->";
        $newcontent = str_replace($prehref, $posthref, $newcontent);

        $jquery_content = "<head>\n<script src='" . Path::getInstance()->getJavascriptPath('Chamilo\Libraries', true) .
             "Plugin/Jquery/jquery.min.js' type='text/javascript'></script>";
        $add_to = '<head>';
        $newcontent = str_replace($add_to, $jquery_content, $newcontent);

        return $newcontent;
    }

    /**
     * This function 'loads' the hotpotatoes excercise.
     *
     * @param String $path_to_zip
     */
    public function load_from_zip($path_to_zip)
    {
        $zip_file_name = basename($path_to_zip);
        $file_name_parts = explode('.', $zip_file_name);
        array_pop($file_name_parts);
        $file_name = implode(' ', $file_name_parts);

        $this->set_title($file_name);
        $this->set_description($file_name);

        $hotpot_path = Path::getInstance()->getPublicStoragePath(Hotpotatoes::package()) . Session::get_user_id() . '/';
        $full_path = $hotpot_path . dirname($path_to_zip) . '/';

        $filecompression = Filecompression::factory();
        $dir = $filecompression->extract_file($full_path . $zip_file_name);
        $entries = Filesystem::get_directory_content($dir);

        foreach ($entries as $entry)
        {
            $filename = substr($entry, strlen($dir));
            $full_new_path = $full_path . $filename;
            $new_path = substr($full_new_path, strlen($hotpot_path));

            Filesystem::move_file($entry, $full_new_path, false);
            if (substr($filename, - 4) == '.htm' || substr($filename, - 5) == '.html')
            {
                $this->set_path($new_path);
            }
        }
        Filesystem::remove($dir);
        Filesystem::remove($full_path . $zip_file_name);
    }
}
