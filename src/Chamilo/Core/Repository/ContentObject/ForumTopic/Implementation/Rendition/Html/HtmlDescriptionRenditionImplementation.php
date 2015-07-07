<?php
namespace Chamilo\Core\Repository\ContentObject\ForumTopic\Implementation\Rendition\Html;

use Chamilo\Core\Repository\Common\ContentObjectResourceRenderer;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\ContentObject\ForumTopic\Implementation\Rendition\HtmlRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass\ForumTopic;
use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataManager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use HTML_Table;
use Pager;

/**
 * This class is used for displaying a ForumTopic ContentObject and all its posts.
 *
 * @author Maarten Volckaert - Hogeschool Gent
 */
class HtmlDescriptionRenditionImplementation extends HtmlRenditionImplementation
{

    /**
     * Variable that represents the content object that will be displayed.
     *
     * @var Forum Topic
     */
    private $object;

    /**
     * The number of the page that will be displayed
     */
    private $page_nr;

    /**
     * Number of items to display per page
     */
    private $per_page;

    /**
     * The pager object to split the data in several pages
     */
    private $pager;

    /**
     * A prefix for the URL-parameters, can be used on pages with multiple Pagers
     */
    private $param_prefix;

    /**
     * The total number of items in the list
     */
    private $total_number_of_items;

    /**
     * The default number of objects per page.
     */
    const DEFAULT_PER_PAGE = 5;

    /**
     * This function launches the rendition of the Content Object.
     *
     * @return ContentObjectRendition
     */
    public function render()
    {
        return ContentObjectRendition :: launch($this);
    }

    /**
     * This method gets all the content for the view and returns it to the rendition.
     *
     * @return Full HTML page.
     */
    public function get_description()
    {
        $this->object = $this->get_content_object();
        // Prepare the pager
        $this->prepare_pager();
        $pager = $this->get_pager();

        // Set the starting position for the data retrievement
        $offset = $pager->getOffsetByPageId();
        $from = $offset[0] - 1;

        $table = new HTML_Table(array('class' => 'forum', 'cellspacing' => 2));
        $html = array();

        if (DataManager :: count_forum_topic_posts($this->object->get_id()) > 0)
        {
            $html[] = '<div class="clear"></div><br />';
            $row = 0;
            $this->make_table($this->get_table_data($from, $this->total_number_of_items), $table, $row);
            $html[] = '<div>' . $table->toHtml() . '</div>';
            $html[] = $this->get_navigation_html();
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * This function generates the posts table of a topic.
     *
     * @param ResultSet $posts A Resultset of posts.
     * @param HTML_Table $table A HTML_Table object in which the posts will be added.
     * @param int $row The row number where this function will start adding content.
     */
    public function make_table($posts, $table, &$row)
    {
        $post_counter = 0;
        foreach ($posts as $post)
        {
            if (! $this->object->is_first_post($post))
            {
                $display = new ContentObjectResourceRenderer($this, $post->get_content());
                $html = $display->run();
                $class = ($post_counter % 2 == 0 ? 'row1' : 'row2');
                $table->setCellContents(
                    $row,
                    0,
                    '<div style="float:right;"><b>' . Translation :: get('Subject') . ':</b> ' . $post->get_title() .
                         '</div>');
                $table->setCellAttributes(
                    $row,
                    0,
                    array('class' => $class, 'height' => 25, 'style' => 'padding-left: 10px;'));

                $row ++;

                $info = DatetimeUtilities :: format_locale_date(null, $post->get_creation_date());

                $message = $this->format_message($html);
                $attachments = $post->get_attached_content_objects();
                if (count($attachments) > 0)
                {
                    $message .= '<div class="quotetitle">' . Translation :: get('Attachments') .
                         ':</div><div class="quotecontent"><ul>';

                    foreach ($attachments as $attachment)
                    {
                        $url = $this->get_context()->get_content_object_display_attachment_url($attachment);
                        $url = 'javascript:openPopup(\'' . $url . '\'); return false;';
                        $message .= '<li><a href="#" onClick="' . $url . '">' . ContentObject :: icon_image(
                            $attachment->context(),
                            Theme :: ICON_MINI) . $attachment->get_title() . '</a></li>';
                    }

                    $message .= '</ul></div>';
                }

                $table->setCellContents($row, 0, $message);
                $table->setCellAttributes(
                    $row,
                    0,
                    array('class' => $class, 'valign' => 'top', 'style' => 'padding: 10px; padding-top: 10px;'));

                $row ++;

                $bottom_bar = '<div style="float: right;"><a name="post_' . $post->get_id() . '"></a>' .
                     Translation :: get('Created by') . '<b> ' . $post->get_user()->get_fullname() . '</b> ' .
                     Translation :: get('On') . ' <b> ' . $info . '</b></div>';
                $table->setCellContents($row, 0, $bottom_bar);
                $table->setCellAttributes(
                    $row,
                    0,
                    array('class' => $class, 'style' => 'padding: 10px;', 'width' => 500));

                $row ++;

                $table->setCellContents($row, 0, ' ');
                $table->setCellAttributes($row, 0, array('colspan' => '1', 'class' => 'spacer'));

                $row ++;
            }
            $post_counter ++;
        }
    }

    /**
     * Function that converts content to the right form.
     *
     * @param string $message String content that needs to be converted.
     * @return string $message Converted message.
     */
    private function format_message($message)
    {
        $message = preg_replace(
            '/\[quote=("|&quot;)(.*)("|&quot;)\]/',
            "<div class=\"quotetitle\">$2 " . Translation :: get('Wrote') . ":</div><div class=\"quotecontent\">",
            $message);
        $message = str_replace('[/quote]', '</div>', $message);
        return $message;
    }

    /**
     * Prepares the pager (counts objects, sets page, etc)
     */
    public function prepare_pager()
    {
        // set the prefix
        $this->param_prefix = ForumTopic :: get_table_name() . '_';

        // count the total number of objects
        $this->total_number_of_items = DataManager :: count_forum_topic_posts($this->object->get_id());

        // set the page number
        $this->page_nr = isset($_SESSION[$this->param_prefix . 'page_nr']) ? $_SESSION[$this->param_prefix . 'page_nr'] : 1;
        $this->page_nr = Request :: get($this->param_prefix . 'page_nr') ? Request :: get(
            $this->param_prefix . 'page_nr') : $this->page_nr;
        $_SESSION[$this->param_prefix . 'page_nr'] = $this->page_nr;

        // set the number of objects per page
        $this->per_page = self :: DEFAULT_PER_PAGE;
    }

    /**
     * Get the Pager object to split the showed data in several pages
     */
    public function get_pager()
    {
        if (is_null($this->pager))
        {
            $total_number_of_items = $this->total_number_of_items;
            $params['mode'] = 'Sliding';
            $params['perPage'] = $this->per_page;
            $params['totalItems'] = $total_number_of_items;
            $params['urlVar'] = $this->param_prefix . 'page_nr';
            $params['prevImg'] = '<img src="' . Theme :: getInstance()->getCommonImagePath('Action/Prev') .
                 '"  style="vertical-align: middle;"/>';
            $params['nextImg'] = '<img src="' . Theme :: getInstance()->getCommonImagePath('Action/Next') .
                 '"  style="vertical-align: middle;"/>';
            $params['firstPageText'] = '<img src="' . Theme :: getInstance()->getCommonImagePath('Action/First') .
                 '"  style="vertical-align: middle;"/>';
            $params['lastPageText'] = '<img src="' . Theme :: getInstance()->getCommonImagePath('Action/Last') .
                 '"  style="vertical-align: middle;"/>';
            $params['firstPagePre'] = '';
            $params['lastPagePre'] = '';
            $params['firstPagePost'] = '';
            $params['lastPagePost'] = '';
            $params['spacesBeforeSeparator'] = '';
            $params['spacesAfterSeparator'] = '';
            $params['currentPage'] = $this->page_nr;
            $params['excludeVars'] = array('message');

            $this->pager = Pager :: factory($params);
        }

        return $this->pager;
    }

    /**
     * Get the HTML-code with the navigational buttons to browse through the data-pages.
     */
    public function get_navigation_html()
    {
        $pager = $this->get_pager();
        $pager_links = $pager->getLinks();
        $showed_items = $pager->getOffsetByPageId();
        return $pager_links['first'] . ' ' . $pager_links['back'] . ' ' . $pager->getCurrentPageId() . ' / ' .
             $pager->numPages() . ' ' . $pager_links['next'] . ' ' . $pager_links['last'];
    }

    /**
     * Get table data to show on current page
     *
     * @see SortableTable#get_table_data
     */
    public function get_table_data($from = 1, $total = 1)
    {
        $data = DataManager :: retrieve_forum_posts($this->object->get_id())->as_array();
        return array_slice($data, $from, self :: DEFAULT_PER_PAGE);
    }

    /**
     * Gets the number of posts per page.
     *
     * @return int
     */
    public function get_per_page()
    {
        return $this->per_page;
    }
}
