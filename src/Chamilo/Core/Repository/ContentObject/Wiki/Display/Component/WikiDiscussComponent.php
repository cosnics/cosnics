<?php
namespace Chamilo\Core\Repository\ContentObject\Wiki\Display\Component;

use Chamilo\Core\Repository\ContentObject\Wiki\Display\Manager;
use Chamilo\Core\Repository\Feedback\FeedbackSupport;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use MediawikiParser;
use MediawikiParserContext;

/**
 * $Id: wiki_discuss.class.php 200 2009-11-13 12:30:04Z kariboe $
 *
 * @package repository.lib.complex_display.wiki.component
 */
/*
 * This is the discuss page. Here a user can add feedback to a wiki_page. Author: Stefan Billiet Author: Nick De Feyter
 */
require_once Path :: getInstance()->getPluginPath() . 'wiki/mediawiki_parser.class.php';
require_once Path :: getInstance()->getPluginPath() . 'wiki/mediawiki_parser_context.class.php';
class WikiDiscussComponent extends Manager implements DelegateComponent, FeedbackSupport
{
    /*
     * private $wiki_page_id; private $complex_id; private $feedback_id; private $links;
     */
    const TITLE_MARKER = '<!-- /title -->';
    const DESCRIPTION_MARKER = '<!-- /description -->';

    public function run()
    {
        $this->set_parameter(
            self :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID,
            $this->get_selected_complex_content_object_item_id());

        $factory = new ApplicationFactory(
            $this->getRequest(),
            \Chamilo\Core\Repository\Feedback\Manager :: context(),
            $this->get_user(),
            $this);
        return $factory->run();
    }

    public function add_actionbar_item()
    {
    }

    public function render_header()
    {
        $complex_wiki_page_id = Request :: get(self :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID);
        $complex_wiki_page = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
            ComplexContentObjectItem :: class_name(),
            $complex_wiki_page_id);
        $wiki_page = $complex_wiki_page->get_ref_object();

        $html = array();

        $html[] = parent :: render_header($complex_wiki_page);

        $parser = new MediawikiParser(
            new MediawikiParserContext(
                $this->get_root_content_object(),
                $wiki_page->get_title(),
                $wiki_page->get_description(),
                $this->get_parameters()));

        $html[] = '<div class="wiki-pane-content-title">' . Translation :: get('Discuss') . ' ' . $wiki_page->get_title() .
             '</div>';
        $html[] = '<div class="wiki-pane-content-subtitle">' . Translation :: get(
            'From',
            null,
            Utilities :: COMMON_LIBRARIES) . ' ' . $this->get_root_content_object()->get_title() . '</div>';
        $html[] = '<div class="wiki-pane-content-discuss">';
        $html[] = $parser->parse($wiki_page->get_description());
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';

        $html[] = '<div class="wiki-pane-content-feedback">';

        return implode(PHP_EOL, $html);
    }

    public function render_footer()
    {
        $html = array();

        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        $html[] = parent :: render_footer();

        return implode(PHP_EOL, $html);
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail = $this->get_breadcrumbtrail();
    }

    /**
     *
     * @see \core\repository\feedback\FeedbackSupport::retrieve_feedbacks()
     */
    public function retrieve_feedbacks()
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * @see \core\repository\feedback\FeedbackSupport::count_feedbacks()
     */
    public function count_feedbacks()
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * @see \core\repository\feedback\FeedbackSupport::retrieve_feedback()
     */
    public function retrieve_feedback($feedback_id)
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * @see \core\repository\feedback\FeedbackSupport::get_feedback()
     */
    public function get_feedback()
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * @see \core\repository\feedback\FeedbackSupport::is_allowed_to_view_feedback()
     */
    public function is_allowed_to_view_feedback()
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * @see \core\repository\feedback\FeedbackSupport::is_allowed_to_create_feedback()
     */
    public function is_allowed_to_create_feedback()
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * @see \core\repository\feedback\FeedbackSupport::is_allowed_to_update_feedback()
     */
    public function is_allowed_to_update_feedback($feedback)
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * @see \core\repository\feedback\FeedbackSupport::is_allowed_to_delete_feedback()
     */
    public function is_allowed_to_delete_feedback($feedback)
    {
        // TODO Auto-generated method stub
    }
}
