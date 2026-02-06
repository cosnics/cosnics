<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Core\Home\UserInterface\HomeRenderer\TabRenderer;
use Chamilo\Libraries\Filesystem\Service\SystemPathBuilder;
use Chamilo\Libraries\Filesystem\Service\WebPathBuilder;
use Chamilo\Libraries\Service\Resource\ResourceManager;
use Chamilo\Libraries\UserInterface\Breadcrumb\Architecture\Domain\BreadcrumbTrail;
use Chamilo\Libraries\UserInterface\Breadcrumb\Service\BreadcrumbGenerator;
use Chamilo\Libraries\UserInterface\Breadcrumb\Service\BreadcrumbTrailRenderer;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\ButtonRendererCollection;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonRendererInterface;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\ButtonGroupRenderer;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\ButtonRenderer;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\ButtonToolBarRenderer;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\DropDownButtonRenderer;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\MiniButtonToolBarRenderer;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\SplitDropdownButtonRenderer;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\SubButtonDividerRenderer;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\SubButtonHeaderRenderer;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\SubButtonRenderer;
use Chamilo\Libraries\UserInterface\Form\Factory\FormValidatorHtmlEditorOptionsFactory;
use Chamilo\Libraries\UserInterface\Form\Service\FormValidatorHtmlEditorRenderer;
use Chamilo\Libraries\UserInterface\Layout\Architecture\Domain\PageConfiguration;
use Chamilo\Libraries\UserInterface\Layout\Service\BannerRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\FooterRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\HeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\PanelRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\ProgressBarRenderer;
use Chamilo\Libraries\UserInterface\NotificationMessage\Architecture\Interface\NotificationMessageStorageInterface;
use Chamilo\Libraries\UserInterface\NotificationMessage\Service\NotificationMessageManager;
use Chamilo\Libraries\UserInterface\NotificationMessage\Service\NotificationMessageRenderer;
use Chamilo\Libraries\UserInterface\NotificationMessage\Storage\Repository\NotificationMessageSessionStorage;
use Chamilo\Libraries\UserInterface\Tab\Service\ActionRenderer;
use Chamilo\Libraries\UserInterface\Tab\Service\ActionsTabRenderer;
use Chamilo\Libraries\UserInterface\Tab\Service\ContentTabRenderer;
use Chamilo\Libraries\UserInterface\Tab\Service\FormTabGenerator;
use Chamilo\Libraries\UserInterface\Tab\Service\FormTabsGenerator;
use Chamilo\Libraries\UserInterface\Tab\Service\GenericTabRenderer;
use Chamilo\Libraries\UserInterface\Tab\Service\GenericTabsRenderer;
use Chamilo\Libraries\UserInterface\Tab\Service\LinkTabRenderer;
use Chamilo\Libraries\UserInterface\Tab\Service\LinkTabsRenderer;
use Chamilo\Libraries\UserInterface\Tab\Service\TabsRenderer;
use Chamilo\Libraries\UserInterface\Table\Factory\DataClassPropertyTableColumnFactory;
use Chamilo\Libraries\UserInterface\Table\Service\ArrayCollectionTableRenderer;
use Chamilo\Libraries\UserInterface\Table\Service\ListHtmlTableRenderer;
use Chamilo\Libraries\UserInterface\Table\Service\ListTableRenderer;
use Chamilo\Libraries\UserInterface\Table\Service\Pager;
use Chamilo\Libraries\UserInterface\Table\Service\PagerRenderer;
use Chamilo\Libraries\UserInterface\Table\Service\PropertiesTableRenderer;
use Chamilo\Libraries\UserInterface\Table\Service\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\UserInterface\Theme\Service\ThemePathBuilder;
use Chamilo\Libraries\UserInterface\Tree\Service\JsTreeRenderer;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set('Chamilo\Libraries\UserInterface\Theme\Service\ThemeSystemPathBuilder', ThemePathBuilder::class)
        ->args([
            '$pathBuilder' => service(SystemPathBuilder::class),
            '$theme' => '%cosnics.libraries.userInterface.theme%',
        ]);

    $services->set('Chamilo\Libraries\UserInterface\Theme\Service\ThemeWebPathBuilder', ThemePathBuilder::class)->args([
        '$pathBuilder' => service(WebPathBuilder::class),
        '$theme' => '%cosnics.libraries.userInterface.theme%',
    ]);

    $services->set(ResourceManager::class);

    $services->set(NotificationMessageManager::class);
    $services->set(NotificationMessageRenderer::class);
    $services->set(NotificationMessageSessionStorage::class);
    $services->alias(NotificationMessageStorageInterface::class, NotificationMessageSessionStorage::class);

    $services->set(ArrayCollectionTableRenderer::class);
    $services->set(PropertiesTableRenderer::class);
    $services->set(ListTableRenderer::class);
    $services->set(ListHtmlTableRenderer::class);

    $services->set(Pager::class);
    $services->set(PagerRenderer::class);

    $services->set(RequestTableParameterValuesCompiler::class);
    $services->set(DataClassPropertyTableColumnFactory::class);

    $services->set(FormTabGenerator::class);
    $services->set(FormTabsGenerator::class);
    $services->set(LinkTabRenderer::class);
    $services->set(LinkTabsRenderer::class);
    $services->set(ActionRenderer::class);
    $services->set(ActionsTabRenderer::class);
    $services->set(ContentTabRenderer::class);
    $services->set(GenericTabRenderer::class);
    $services->set(GenericTabsRenderer::class);
    $services->set(TabRenderer::class);
    $services->set(TabsRenderer::class);

    $services->set(BreadcrumbTrail::class);
    $services->set(BreadcrumbGenerator::class);
    $services->set(BreadcrumbTrailRenderer::class);

    $services->set(BannerRenderer::class);
    $services->set(PageConfiguration::class);
    $services->set(FooterRenderer::class)->args(
        [
            '$administratorData' => '%cosnics.libraries.userInterface.layout.administrator%',
            '$institutionData' => '%cosnics.libraries.userInterface.layout.institution%'
        ]
    );
    $services->set(HeaderRenderer::class)->args(
        [
            '$themeWebPathBuilder' => service('Chamilo\Libraries\UserInterface\Theme\Service\ThemeWebPathBuilder'),
            '$siteName' => '%cosnics.libraries.userInterface.layout.site.name%',
            '$institutionName' => '%cosnics.libraries.userInterface.layout.institution.name%'
        ]
    );

    $services->set(PanelRenderer::class);
    $services->set(ProgressBarRenderer::class);

    $services->set(FormValidatorHtmlEditorRenderer::class);
    $services->set(FormValidatorHtmlEditorOptionsFactory::class);

    $services->set(JsTreeRenderer::class);

    $services->set(ButtonRendererCollection::class);
    $services->set(ButtonToolBarRenderer::class);
    $services->set(MiniButtonToolBarRenderer::class);
    $services->set(ButtonGroupRenderer::class)->tag(ButtonRendererInterface::class);
    $services->set(ButtonRenderer::class)->tag(ButtonRendererInterface::class);
    $services->set(DropDownButtonRenderer::class)->tag(ButtonRendererInterface::class);
    $services->set(SplitDropdownButtonRenderer::class)->tag(ButtonRendererInterface::class);
    $services->set(SubButtonDividerRenderer::class)->tag(ButtonRendererInterface::class);
    $services->set(SubButtonHeaderRenderer::class)->tag(ButtonRendererInterface::class);
    $services->set(SubButtonRenderer::class)->tag(ButtonRendererInterface::class);
};
