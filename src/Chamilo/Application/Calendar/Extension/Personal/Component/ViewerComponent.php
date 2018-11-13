<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Component;

use Chamilo\Application\Calendar\Extension\Personal\Manager;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataManager;
use Chamilo\Core\Group\Storage\DataClass\Group;
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
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
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

        $html = array();

        $html[] = $this->render_header();
        $html[] = $this->getButtonToolbarRenderer()->render() . '<br />';
        $html[] = '<div id="action_bar_browser">';
        $html[] = $this->get_publication_as_html();
        $html[] = '</div>';
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
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
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ParameterNotDefinedException
     */
    public function get_publication_as_html()
    {
        $content_object = $this->getPublication()->get_publication_object();
        $content_object_properties = $content_object->get_properties();
        BreadcrumbTrail::getInstance()->add(
            new Breadcrumb(null, $content_object_properties['default_properties']['title'])
        );

        $html = array();

        $html[] = ContentObjectRenditionImplementation::launch(
            $content_object, ContentObjectRendition::FORMAT_HTML, ContentObjectRendition::VIEW_FULL, $this
        );

        $html[] = $this->render_info();

        return implode(PHP_EOL, $html);
    }

    /**
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ParameterNotDefinedException
     */
    public function render_info()
    {
        $publisher = $this->getUserService()->findUserByIdentifier($this->getPublication()->get_publisher());

        $html = array();

        $html[] = '<div class="event_publication_info">';
        $html[] = htmlentities(Translation::get('PublishedOn', null, Utilities::COMMON_LIBRARIES)) . ' ' .
            $this->render_publication_date();
        $html[] =
            htmlentities(Translation::get('By', null, Utilities::COMMON_LIBRARIES)) . ' ' . $publisher->get_fullname();
        $html[] = htmlentities(Translation::get('SharedWith', null, Utilities::COMMON_LIBRARIES)) . ' ' .
            $this->render_publication_targets();
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
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

            if (count($users) + count($groups) == 1)
            {
                if (count($users) == 1)
                {
                    $user = array_pop($users);

                    return $user->get_firstname() . ' ' . $user->get_lastname();
                }
                else
                {
                    $group = array_pop($groups);

                    return $group->get_name();
                }
            }

            $target_list = array();
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
                    Translation::get('ExportIcal'), Theme::getInstance()->getCommonImagePath('Export/Csv'), $ical_url
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
                        Translation::get('Edit', null, Utilities::COMMON_LIBRARIES),
                        Theme::getInstance()->getCommonImagePath('Action/Edit'), $editUrl
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
                        Translation::get('Delete', null, Utilities::COMMON_LIBRARIES),
                        Theme::getInstance()->getCommonImagePath('Action/Delete'), $deleteUrl
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
     * @param \Chamilo\Libraries\Format\Structure\BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('personal_calendar_viewer');
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
}
