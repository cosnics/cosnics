<?php
namespace Chamilo\Core\Lynx\Component;

use Chamilo\Configuration\Package\Action;
use Chamilo\Configuration\Package\Service\PackageFactory;
use Chamilo\Configuration\Package\Storage\DataClass\Package;
use Chamilo\Core\Lynx\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbLessComponentInterface;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use OutOfBoundsException;

abstract class ActionComponent extends Manager implements BreadcrumbLessComponentInterface
{
    protected ?Package $currentPackage;

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function run()
    {
        set_time_limit(0);

        try
        {
            $context = $this->getCurrentContext();

            $translator = $this->getTranslator();

            $this->getBreadcrumbTrail()->add(
                new Breadcrumb(
                    null, $translator->trans(
                    $this->getBreadcrumbNameVariable(), ['PACKAGE' => $translator->trans('TypeName', [], $context)]
                )
                )
            );

            $toolbar = new Toolbar();
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('BackToPackageOVerview', [], Manager::CONTEXT), new FontAwesomeGlyph('backward'),
                    $this->get_url([self::PARAM_ACTION => self::ACTION_BROWSE])
                )
            );

            $html = [];

            $html[] = $this->renderHeader();
            $html[] = $this->runPackageAction($this->getCurrentPackageAction());
            $html[] = $toolbar->render();
            $html[] = $this->renderFooter();

            return implode(PHP_EOL, $html);
        }
        catch (OutOfBoundsException)
        {
            throw new NotAllowedException();
        }
    }

    abstract protected function getBreadcrumbNameVariable(): string;

    public function getCurrentPackage(): Package
    {
        if (!isset($this->currentPackage))
        {
            $this->currentPackage = $this->getPackageFactory()->getPackage($this->getCurrentContext());
        }

        return $this->currentPackage;
    }

    abstract protected function getCurrentPackageAction(): Action;

    public function getPackageFactory(): PackageFactory
    {
        return $this->getService(PackageFactory::class);
    }

    protected function renderPanel(string $panelType, InlineGlyph $inlineGlyph, string $title, string $message): string
    {
        $html = [];

        $html[] = '<div class="panel ' . $panelType . '">';

        $html[] = '<div class="panel-heading">';
        $html[] = '<h3 class="panel-title">';
        $html[] = $inlineGlyph->render();
        $html[] = ' ' . $title . '</h3>';
        $html[] = '</div>';

        $html[] = '<div class="panel-body">';
        $html[] = $message;
        $html[] = '</div>';

        $html[] = '</div>';

        return implode(PHP_EOL, $html[]);
    }

    protected function runPackageAction(Action $packageAction): string
    {
        $translator = $this->getTranslator();

        if (!$packageAction->run())
        {
            $title = $translator->trans('Failed', [], Manager::CONTEXT);
            $inlineGlyph = new FontAwesomeGlyph('sad-cry', ['fa-lg'], null, 'fas');
            $panelType = 'panel-danger';
        }
        else
        {
            $title = $translator->trans('Finished', [], Manager::CONTEXT);
            $inlineGlyph = new FontAwesomeGlyph('laugh-beam', ['fa-lg'], null, 'fas');
            $panelType = 'panel-default';
        }

        return $this->renderPanel($panelType, $inlineGlyph, $title, $packageAction->retrieve_message());
    }
}
