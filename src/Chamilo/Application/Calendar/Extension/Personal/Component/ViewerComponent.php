<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Component;

use Chamilo\Application\Calendar\Extension\Personal\Manager;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Architecture\Exceptions\ParameterNotDefinedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * @package Chamilo\Application\Calendar\Extension\Personal\Component
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ViewerComponent extends Manager implements DelegateComponent
{

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    /**
     * @var \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication
     */
    private $publication;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if (!$this->getRightsService()->isAllowedToViewPublication($this->getPublication(), $this->getUser()))
        {
            throw new NotAllowedException();
        }

        $html = [];

        $html[] = $this->render_header();
        $html[] = $this->getButtonToolbarRenderer()->render() . '<br />';
        $html[] = '<div id="action_bar_browser">';
        $html[] = $this->get_publication_as_html();
        $html[] = '</div>';
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * @param \Chamilo\Libraries\Format\Structure\BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('personal_calendar_viewer');
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ParameterNotDefinedException
     */
    public function getButtonToolbarRenderer()
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar();
            $commonActions = new ButtonGroup();
            $toolActions = new ButtonGroup();

            $ical_url = $this->get_url(
                array(
                    self::PARAM_ACTION => self::ACTION_EXPORT,
                    self::PARAM_PUBLICATION_ID => $this->getPublication()->get_id()
                )
            );

            $toolActions->addButton(
                new Button(
                    Translation::get('ExportIcal'), new FontAwesomeGlyph('download'), $ical_url
                )
            );

            $user = $this->get_user();

            if ($this->getRightsService()->isAllowedToEditPublication($this->getPublication(), $this->getUser()))
            {
                $editUrl = $this->get_url(
                    array(
                        self::PARAM_ACTION => self::ACTION_EDIT,
                        self::PARAM_PUBLICATION_ID => $this->getPublication()->get_id()
                    )
                );

                $commonActions->addButton(
                    new Button(
                        Translation::get('Edit', null, Utilities::COMMON_LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                        $editUrl
                    )
                );
            }

            if ($this->getRightsService()->isAllowedToDeletePublication($this->getPublication(), $this->getUser()))
            {
                $deleteUrl = $this->get_url(
                    array(
                        self::PARAM_ACTION => self::ACTION_DELETE,
                        self::PARAM_PUBLICATION_ID => $this->getPublication()->getId()
                    )
                );

                $commonActions->addButton(
                    new Button(
                        Translation::get('Delete', null, Utilities::COMMON_LIBRARIES), new FontAwesomeGlyph('times'),
                        $deleteUrl
                    )
                );
            }

            $buttonToolbar->addButtonGroup($commonActions);
            $buttonToolbar->addButtonGroup($toolActions);
            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    /**
     * @return \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ParameterNotDefinedException
     */
    public function getPublication()
    {
        if (!isset($this->publication))
        {
            $publicationIdentifier = $this->getRequest()->query->get(Manager::PARAM_PUBLICATION_ID);
            if (is_null($publicationIdentifier))
            {
                throw new ParameterNotDefinedException(Manager::PARAM_PUBLICATION_ID);
            }

            $publication = $this->getPublicationService()->findPublicationByIdentifier($publicationIdentifier);
            if (!$publication)
            {
                throw new ObjectNotExistException(Translation::get('Publication'), $publicationIdentifier);
            }

            $this->publication = $publication;
        }

        return $this->publication;
    }

    /**
     * @param $attachment
     *
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ParameterNotDefinedException
     */
    public function get_content_object_display_attachment_url($attachment)
    {
        return $this->get_url(
            array(
                Application::PARAM_ACTION => Manager::ACTION_VIEW_ATTACHMENT,
                self::PARAM_PUBLICATION_ID => $this->getPublication()->getId(),
                self::PARAM_OBJECT => $attachment->get_id()
            )
        );
    }

    /**
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ParameterNotDefinedException
     */
    public function get_publication_as_html()
    {
        $content_object = $this->getPublication()->get_publication_object();
        $publisher = $this->getUserService()->findUserByIdentifier($this->getPublication()->get_publisher());

        BreadcrumbTrail::getInstance()->add(
            new Breadcrumb(null, $content_object->get_title())
        );

        $html = [];

        $html[] = '<div class="panel panel-default panel-publication panel-publication-">';
        $html[] = '	<div class="panel-body">';
        $html[] = '		<div class="row panel-publication-header">';
        $html[] = '			<div class="col-xs-12 col-sm-10 panel-publication-header-title">';
        $html[] = '				<h3>' . $content_object->get_title() . '</h3>';
        $html[] = '				<small>' . $publisher->get_fullname() . '</small>';
        $html[] = '			</div>';
        $html[] = '			<div class="col-xs-12 col-sm-2 panel-publication-header-actions"></div>';
        $html[] = '		</div>';
        $html[] = '		<div class="row panel-publication-body">';
        $html[] = '			<div class="col-xs-12">';

        $html[] = ContentObjectRenditionImplementation::launch(
            $content_object, ContentObjectRendition::FORMAT_HTML, ContentObjectRendition::VIEW_DESCRIPTION, $this
        );

        $html[] = '			</div>';
        $html[] = '		</div>';
        $html[] = '		<div class="row panel-publication-footer">';
        $html[] = '			<div class="col-xs-12 col-sm-3 panel-publication-footer-date">';

        $glyph = new FontAwesomeGlyph('clock', [], null, 'far');

        $html[] = '				' . $glyph->render() . ' ' . $this->render_publication_date();
        $html[] = '			</div>';
        $html[] = '			<div class="col-xs-12 col-sm-6 panel-publication-footer-visibility"></div>';
        $html[] = '			<div class="col-xs-12 col-sm-3 panel-publication-footer-targets">';

        $glyph = new FontAwesomeGlyph('user', [], null, 'fas');

        $html[] = $glyph->render() . ' ' . $this->render_publication_targets();
        $html[] = '			</div>';
        $html[] = '		</div>';
        $html[] = '	</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ParameterNotDefinedException
     */
    public function render_publication_date()
    {
        $date_format = Translation::get('DateTimeFormatLong', null, Utilities::COMMON_LIBRARIES);

        return DatetimeUtilities::format_locale_date($date_format, $this->getPublication()->get_published());
    }

    /**
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ParameterNotDefinedException
     */
    public function render_publication_targets()
    {
        $publication = $this->getPublication();

        if (!$this->getRightsService()->isPublicationSharedWithAnyone($publication))
        {
            return htmlentities(Translation::get('Nobody', null, \Chamilo\Core\User\Manager::context()));
        }
        else
        {
            $users = $this->getRightsService()->getUsersForPublication($publication);
            $groups = $this->getRightsService()->getGroupsForPublication($publication);

            if (($users->count() + $groups->count()) == 1)
            {
                if ($users->count() == 1)
                {
                    $user = array_pop($users->toArray());

                    return $user->get_firstname() . ' ' . $user->get_lastname();
                }
                else
                {
                    $group = array_pop($groups->toArray());

                    return $group->get_name();
                }
            }

            $target_list = [];
            $target_list[] = '<select>';

            foreach ($users as $user)
            {
                $target_list[] = '<option>' . $user->get_firstname() . ' ' . $user->get_lastname() . '</option>';
            }

            foreach ($groups as $group)
            {
                $target_list[] = '<option>' . $group->get_name() . '</option>';
            }

            $target_list[] = '</select>';

            return implode(PHP_EOL, $target_list);
        }
    }
}
