<?php
namespace Chamilo\Core\Repository\ContentObject\Hotpotatoes\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\Compression\ZipArchive\ZipArchiveFilecompression;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\SystemPathBuilder;
use Chamilo\Libraries\File\WebPathBuilder;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\String\Text;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @package Chamilo\Core\Repository\ContentObject\Hotpotatoes\Storage\DataClass
 */
class Hotpotatoes extends ContentObject implements Versionable
{
    public const CONTEXT = 'Chamilo\Core\Repository\ContentObject\Hotpotatoes';

    public const PROPERTY_MAXIMUM_ATTEMPTS = 'max_attempts';
    public const PROPERTY_PATH = 'path';

    public const TYPE_HOTPOTATOES = 3;

    public function add_javascript($postback_url, $goback_url, $tracker_id)
    {
        $content = $this->read_file_content();
        $js_content = $this->replace_javascript($content, $postback_url, $goback_url, $tracker_id);
        $path = $this->write_file_content($js_content);

        return $path;
    }

    public function delete($only_version = false): bool
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

    public static function getAdditionalPropertyNames(): array
    {
        return [self::PROPERTY_PATH, self::PROPERTY_MAXIMUM_ATTEMPTS];
    }

    public function getSession(): SessionInterface
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(SessionInterface::class);
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'repository_hotpotatoes';
    }

    protected function getZipArchiveFilecompression(): ZipArchiveFilecompression
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            ZipArchiveFilecompression::class
        );
    }

    public function get_assessment_type()
    {
        return self::TYPE_HOTPOTATOES;
    }

    public static function get_assessment_type_name()
    {
        return Translation::get('HotPotatoes');
    }

    public function get_full_path()
    {
        /**
         * @var \Chamilo\Libraries\File\SystemPathBuilder $systemPathBuilder
         */
        $systemPathBuilder =
            DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(SystemPathBuilder::class);

        return $systemPathBuilder->getPublicStoragePath(Hotpotatoes::CONTEXT) . $this->get_owner_id() . '/' .
            $this->get_path();
    }

    public function get_full_url()
    {
        /**
         * @var \Chamilo\Libraries\File\WebPathBuilder $webPathBuilder
         */
        $webPathBuilder =
            DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(WebPathBuilder::class);

        return $webPathBuilder->getPublicStoragePath(Hotpotatoes::CONTEXT) . $this->get_owner_id() . '/' .
            $this->get_path();
    }

    public function get_maximum_attempts()
    {
        return $this->getAdditionalProperty(self::PROPERTY_MAXIMUM_ATTEMPTS);
    }

    public function get_maximum_score()
    {
        return 100;
    }

    public function get_path()
    {
        return $this->getAdditionalProperty(self::PROPERTY_PATH);
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

        /**
         * @var \Chamilo\Libraries\File\SystemPathBuilder $systemPathBuilder
         */
        $systemPathBuilder =
            DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(SystemPathBuilder::class);

        $hotpot_path = $systemPathBuilder->getPublicStoragePath(Hotpotatoes::CONTEXT) .
            $this->getSession()->get(Manager::SESSION_USER_ID) . '/';
        $full_path = $hotpot_path . dirname($path_to_zip) . '/';

        $dir = $this->getZipArchiveFilecompression()->extractFile($full_path . $zip_file_name);
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

    private function read_file_content()
    {
        $full_file_path = $this->get_full_path();

        if (is_file($full_file_path))
        {
            if (!($fp = fopen(urldecode($full_file_path), 'r')))
            {
                return '';
            }
            $contents = fread($fp, filesize($full_file_path));
            fclose($fp);

            return $contents;
        }
    }

    private function replace_javascript($content, $postback_url, $goback_url, $tracker_id)
    {
        $mit = 'function Finish(){';
        $js_content = "var SaveScoreVariable = 0; // This variable included by Chamilo System\n" .
            "function mySaveScore() // This function included by Chamilo System\n" . "{\n" .
            "   if (SaveScoreVariable==0)\n" . "		{\n" . "			SaveScoreVariable = 1;\n" .
            "			var result=jQuery.ajax({type: 'POST', url:'" . $postback_url . "', data: {id: " . $tracker_id .
            ", score: Score}, async: false}).responseText;\n";
        // " alert(result);";

        if ($goback_url)
        {
            $js_content .= "		if (C.ie)\n" . "			{\n" . // " window.alert(Score);\n".
                "				document.parent.location.href=\"" . $goback_url . "\"\n" . "			}\n" .
                "			else\n" . "			{\n" . // "
                // window.alert(Score);\n".
                "				window.parent.location.href=\"" . $goback_url . "\"\n" . "			}\n";
        }

        $js_content .= "		}\n" . " }\n" . "// Must be included \n" . "function Finish(){\n" . ' mySaveScore();';
        $newcontent = str_replace($mit, $js_content, $content);
        $prehref = '<!-- BeginTopNavButtons -->';
        $posthref = '<!-- BeginTopNavButtons --><!-- edited by Chamilo -->';
        $newcontent = str_replace($prehref, $posthref, $newcontent);

        /**
         * @var \Chamilo\Libraries\File\WebPathBuilder $webPathBuilder
         */
        $webPathBuilder =
            DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(WebPathBuilder::class);

        $jquery_content = "<head>\n<script src='" . $webPathBuilder->getJavascriptPath(StringUtilities::LIBRARIES) .
            "Plugin/Jquery/jquery.min.js' type='text/javascript'></script>";
        $add_to = '<head>';
        $newcontent = str_replace($add_to, $jquery_content, $newcontent);

        return $newcontent;
    }

    public function set_maximum_attempts($value)
    {
        $this->setAdditionalProperty(self::PROPERTY_MAXIMUM_ATTEMPTS, $value);
    }

    public function set_path($path)
    {
        return $this->setAdditionalProperty(self::PROPERTY_PATH, $path);
    }

    private function write_file_content($content)
    {
        $full_file_path = $this->get_full_path() . '.t.htm';
        $full_web_path = $this->get_full_url() . '.t.htm';
        Filesystem::remove($full_file_path);

        if (($fp = fopen(urldecode($full_file_path), 'w')))
        {
            fwrite($fp, $content);
            fclose($fp);
        }

        return $full_web_path;
    }
}
