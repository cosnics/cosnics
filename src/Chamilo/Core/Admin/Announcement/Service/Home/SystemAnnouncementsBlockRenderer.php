<?php
namespace Chamilo\Core\Admin\Announcement\Service\Home;

use Chamilo\Core\Admin\Announcement\Manager;
use Chamilo\Core\Admin\Announcement\Service\PublicationService;
use Chamilo\Core\Admin\Announcement\Storage\DataClass\Publication;
use Chamilo\Core\Home\Architecture\ConfigurableInterface;
use Chamilo\Core\Home\Renderer\BlockRenderer;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Translation\Translation;

class SystemAnnouncementsBlockRenderer extends BlockRenderer implements ConfigurableInterface
{
    public const CONFIGURATION_SHOW_EMPTY = 'show_when_empty';

    private $publications;

    public function displayContent()
    {
        $html = [];
        $publications = $this->getPublications();

        if ($publications->count() == 0)
        {
            $html[] = '<div class="panel-body portal-block-content">';
            $html[] = htmlspecialchars(Translation::get('NoSystemAnnouncementsCurrently'));
            $html[] = '</div>';
        }

        foreach ($publications as $publication)
        {
            $content_object = DataManager::retrieve_by_id(
                ContentObject::class, (int) $publication[Publication::PROPERTY_CONTENT_OBJECT_ID]
            );

            $icon = $content_object->get_icon_image(
                IdentGlyph::SIZE_MINI, !(boolean) $publication[Publication::PROPERTY_HIDDEN]
            );

            $href = htmlspecialchars($this->getPublicationLink($publication));
            $title = htmlspecialchars($content_object->get_title());
            $link = '<a href="' . $href . '">' . $title . '</a>';

            $html[] = '<div class="list-group-item">';
            $html[] = '<span class="pull-right">' . $icon . '</span>';
            $html[] = $link;
            $html[] = '</div>';
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * @see \Chamilo\Core\Home\Architecture\ConfigurableInterface::getConfigurationVariables()
     */
    public function getConfigurationVariables()
    {
        return [self::CONFIGURATION_SHOW_EMPTY];
    }

    public function getPublicationLink($publication)
    {
        $paremeters = [];
        $parameters[Application::PARAM_CONTEXT] = \Chamilo\Core\Admin\Manager::CONTEXT;
        $parameters[\Chamilo\Core\Admin\Manager::PARAM_ACTION] =
            \Chamilo\Core\Admin\Manager::ACTION_SYSTEM_ANNOUNCEMENTS;
        $parameters[Manager::PARAM_ACTION] = Manager::ACTION_VIEW;
        $parameters[Manager::PARAM_SYSTEM_ANNOUNCEMENT_ID] = $publication[Publication::PROPERTY_ID];

        return $this->getUrlGenerator()->fromParameters($parameters);
    }

    /**
     * @return \Chamilo\Core\Admin\Announcement\Service\PublicationService
     */
    protected function getPublicationService()
    {
        $container = DependencyInjectionContainerBuilder::getInstance()->createContainer();

        return $container->get(PublicationService::class);
    }

    public function getPublications()
    {
        if (!isset($this->publications))
        {
            $this->publications =
                $this->getPublicationService()->findVisiblePublicationRecordsForUserIdentifier($this->getUserId());
        }

        return $this->publications;
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(UrlGenerator::class);
    }

    public function isEmpty()
    {
        return $this->getPublications()->count() == 0;
    }

    public function isVisible(?User $user = null): bool
    {
        if (!parent::isVisible($user) || ($this->isEmpty() && !$this->showWhenEmpty()))
        {
            return false;
        }

        return true;
    }

    /**
     * @see \Chamilo\Core\Home\Renderer\BlockRenderer::renderContentFooter()
     */
    public function renderContentFooter()
    {
        return '</div>';
    }

    /**
     * @see \Chamilo\Core\Home\Renderer\BlockRenderer::renderContentHeader()
     */
    public function renderContentHeader()
    {
        return '<div class="list-group portal-block-content' . ($this->getBlock()->isVisible() ? '' : ' hidden') . '">';
    }

    public function showWhenEmpty()
    {
        return $this->getBlock()->getSetting(self::CONFIGURATION_SHOW_EMPTY, true);
    }
}