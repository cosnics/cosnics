<?php
namespace Chamilo\Core\Repository\ContentObject\ForumTopic\Common\Export\Html;

use Chamilo\Core\Repository\ContentObject\ForumTopic\Common\Export\HtmlExportImplementation;
use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataManager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use DOMDocument;
use DOMXPath;
use HTML_Table;

/**
 * Class responsible for the creation of the HTML Export File of a Forum Topic.
 * 
 * @author Maarten Volckaert - Hogeschool Gent
 */
class HtmlDefaultExportImplementation extends HtmlExportImplementation
{

    /**
     * **************************************************************************************************************
     * Variables *
     * **************************************************************************************************************
     */
    
    /**
     * Contains the Content object that needs to be rendered in a HTML file.
     * 
     * @var ContentObject
     */
    private $content_object;

    /**
     * An Array filled with all the Forum posts.
     * 
     * @var Array
     */
    private $data;

    /**
     * The path to the file that will be made.
     * 
     * @var string
     */
    private $file;

    /**
     * Variable used for all writes to the file.
     * 
     * @var fopen
     */
    private $handle;

    /**
     * **************************************************************************************************************
     * Main functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Render the export in HTML Format of Forum Topic.
     */
    public function render()
    {
        $this->content_object = $this->get_content_object();
        $this->data = DataManager::retrieve_forum_posts($this->content_object->get_id())->as_array();
        
        // Set filename
        $this->file = Path::getInstance()->getTemporaryPath() . $this->content_object->get_owner_id() .
             '/export_content_objects/content_object.html';
        
        // Open file and start writing the HTML
        $this->handle = fopen($this->file, 'w');
        fwrite($this->handle, '<html><body>');
        $css = $this->define_css();
        fwrite($this->handle, $css);
        fwrite($this->handle, $this->get_table($this->data));
        fwrite($this->handle, '</body></html>');
        fclose($this->handle);
    }

    /**
     * Build a the table of topic post and render the HTML.
     * 
     * @param Array $data
     *
     * @return \HTML_Table
     *
     */
    public function get_table($data)
    {
        $row = 0;
        $table = new HTML_Table(array('class' => 'forum', 'cellspacing' => 2));
        $post_counter = 0;
        
        foreach ($data as $post)
        {
            $class = ($post_counter % 2 == 0 ? 'row1' : 'row2');
            $table->setCellContents(
                $row, 
                0, 
                '<div style="float:right;"><b>' . Translation::get('Subject') . ':</b> ' . $post->get_title() . '</div>');
            $table->setCellAttributes(
                $row, 
                0, 
                array('class' => $class, 'height' => 25, 'style' => 'padding-left: 10px;'));
            
            $row ++;
            
            $info = DatetimeUtilities::format_locale_date(null, $post->get_creation_date());
            
            $message = $this->format_message($post->get_content());
            $message .= '</ul></div>';
            
            $table->setCellContents($row, 0, $message);
            $table->setCellAttributes(
                $row, 
                0, 
                array('class' => $class, 'valign' => 'top', 'style' => 'padding: 10px; padding-top: 10px;'));
            
            $row ++;
            
            $bottom_bar = '<div style="float: right;"><a name="post_' . $post->get_id() . '"></a>' .
                 Translation::get('Created by') . '<b> ' . $post->get_user()->get_fullname() . '</b> ' .
                 Translation::get('On') . ' <b> ' . $info . '</b></div>';
            $table->setCellContents($row, 0, $bottom_bar);
            $table->setCellAttributes($row, 0, array('class' => $class, 'style' => 'padding: 10px;', 'width' => 500));
            
            $row ++;
            
            $table->setCellContents($row, 0, ' ');
            $table->setCellAttributes($row, 0, array('colspan' => '1', 'class' => 'spacer'));
            
            $row ++;
        }
        
        $html = $this->convert_images($table->toHtml());
        return $html;
    }

    /**
     * Function used for formating the content.
     * 
     * @param string $message
     *
     * @return string
     *
     */
    private function format_message($message)
    {
        $message = preg_replace(
            '/\[quote=("|&quot;)(.*)("|&quot;)\]/', 
            "<div class=\"quotetitle\">$2 " . Translation::get('Wrote') . ":</div><div class=\"quotecontent\">", 
            $message);
        $message = str_replace('[/quote]', '</div>', $message);
        return $message;
    }

    /**
     * Convert all image files in the table to encoded base64 string format.
     * 
     * @param HTML Table $body
     * @return HTML Table
     */
    private function convert_images($body)
    {
        $doc = new DOMDocument();
        $doc->loadHTML($body);
        $xpath = new DOMXPath($doc);
        
        $elements = $xpath->query('//resource');
        
        // replace image document resource tags with a html img tag with base64 data
        // remove all other resource tags
        foreach ($elements as $i => $element)
        {
            $type = $element->attributes->getNamedItem('type')->value;
            $id = $element->attributes->getNamedItem('source')->value;
            if ($type == 'document')
            {
                $obj = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(ContentObject::class_name(), $id);
                
                if ($obj->is_image())
                {
                    $img_src = $obj->get_full_path();
                    $imgbinary = fread(fopen($img_src, "r"), filesize($img_src));
                    $img_str = base64_encode($imgbinary);
                    
                    fclose($img_src);
                    
                    $elem = $doc->createElement('img');
                    $elem->setAttribute('src', "data:image/jpg;base64," . $img_str);
                    $elem->setAttribute('alt', $obj->get_filename());
                    $element->parentNode->replaceChild($elem, $element);
                }
                else
                    $element->parentNode->removeChild($element);
            }
            else
                $element->parentNode->removeChild($element);
        }
        
        $body = $doc->saveHTML();
        return $body;
    }

    /**
     * Function used for the inline css build up.
     * 
     * @return string
     *
     */
    private function define_css()
    {
        return '<style type="text/css">
        /*****************************************************
        *  FORUM PHPBB STYLE                                *
        *****************************************************/
        table.forum
        {
            background-color: #A9B8C2;
            width: 100%;
            font-family: "Lucida Grande", Verdana, Helvetica, Arial, sans-serif;
            font-size: 90%;
        }

        table.forum .row1
        {
            background-color: #ECECEC;
            padding: 4px;
        }

        table.forum .row2
        {
            background-color: #DCE1E5;
            padding: 4px;
        }

        table.forum th
        {
            background-color: #006699;
            color: #FFA34F;
            font-weight: bold;
            padding: 7px 5px;
            white-space: nowrap;
            font-size: 1.1em;
        }

        table.forum td.spacer
        {
            background-color: #D1D7DC;
            height: 5px;
        }

        /*****************************************************
        *  FORUM CHAMILO STYLE                                *
        *****************************************************/
        table.forum
        {
            background-color: #c6d8ec;
            width: 100%;
            font-family: "Lucida Grande", Verdana, Helvetica, Arial, sans-serif;
            font-size: 90%;
        }

        table.forum .row1
        {
            background-color: #f4f8fb;
            padding: 4px;
        }

        table.forum .row2
        {
            background-color: #e8eff7;
            padding: 4px;
        }

        table.forum th
        {
            background-color: #6694cc;
            background-image: none;
            color: #2b3f57;
            font-weight: bold;
            padding: 7px 5px;
            white-space: nowrap;
            font-size: 1.1em;
        }

        table.forum td.spacer
        {
            background-color: #aec7e4;
            height: 5px;
        }

        table.forum .quotetitle,.attachtitle
        {
            background-color: #A9B8C2;
            border-color: #A9B8C2;
            border-style: solid;
            border-width: 1px 1px 0;
            color: #333333;
            font-size: 0.85em;
            font-weight: bold;
            margin: 10px 5px 0;
            padding: 4px;
        }

        table.forum .quotecontent,.attachcontent
        {
            background-color: #FAFAFA;
            border-color: #A9B8C2;
            border-style: solid;
            border-width: 0 1px 1px;
            color: #4B5C77;
            font-family: "Lucida Grande", "Trebuchet MS", Helvetica, Arial, sans-serif;
            font-size: 1em;
            font-weight: normal;
            line-height: 1.4em;
            margin: 0 5px 10px;
            padding: 5px;
        }
        </style>
        ';
    }
}
