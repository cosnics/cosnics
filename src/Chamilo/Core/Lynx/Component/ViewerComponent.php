<?php
namespace Chamilo\Core\Lynx\Component;

use Chamilo\Configuration\Package\Action\Activator;
use Chamilo\Configuration\Package\Action\Deactivator;
use Chamilo\Configuration\Package\Action\Installer;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Lynx\Manager;
use Chamilo\Core\Lynx\Service\PackageInformationRenderer;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbLessComponentInterface;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Utilities\StringUtilities;
use OutOfBoundsException;

class ViewerComponent extends Manager implements BreadcrumbLessComponentInterface
{

    protected ButtonToolBarRenderer $buttonToolbarRenderer;

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException
     * @throws \QuickformException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function run()
    {
        $translator = $this->getTranslator();
        $currentContext = $this->getCurrentContext();

        if (!$currentContext)
        {
            throw new NoObjectSelectedException($translator->trans('Package', [], Manager::CONTEXT));
        }

        $this->getBreadcrumbTrail()->add(
            new Breadcrumb(
                null, $translator->trans(
                'ViewingPackage', ['{PACKAGE}' => $translator->trans('TypeName', [], $currentContext)], Manager::CONTEXT
            )
            )
        );

        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $this->getButtonToolbarRenderer()->render();
        $html[] = $this->getPackageInformationRenderer()->render($currentContext);
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function getButtonToolbarRenderer(): ButtonToolBarRenderer
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar();

            try
            {
                $commonActions = new ButtonGroup();

                $translator = $this->getTranslator();
                $context = $this->getCurrentContext();
                $registration = $this->getRegistrationConsulter()->getRegistrationForContext($context);

                if (!empty($registration))
                {
                    if ($registration[Registration::PROPERTY_STATUS] &&
                        $this->getPackageActionFactory()->getPackageDeactivator($context) instanceof Deactivator)
                    {
                        $commonActions->addButton(
                            new Button(
                                $translator->trans('Deactivate', [], StringUtilities::LIBRARIES),
                                new FontAwesomeGlyph('pause-circle', [], null, 'fas'), $this->get_url(
                                [
                                    self::PARAM_ACTION => self::ACTION_DEACTIVATE,
                                    self::PARAM_CONTEXT => $context
                                ]
                            )
                            )
                        );
                    }
                    elseif ($this->getPackageActionFactory()->getPackageActivator($context) instanceof Activator)
                    {
                        $commonActions->addButton(
                            new Button(
                                $translator->trans('Activate', [], StringUtilities::LIBRARIES),
                                new FontAwesomeGlyph('play-circle', [], null, 'fas'), $this->get_url(
                                [
                                    self::PARAM_ACTION => self::ACTION_ACTIVATE,
                                    self::PARAM_CONTEXT => $context
                                ]
                            )
                            )
                        );
                    }
                }
                elseif ($this->getPackageActionFactory()->getPackageInstaller($context) instanceof Installer)
                {

                    $commonActions->addButton(
                        new Button(
                            $translator->trans('Install', [], StringUtilities::LIBRARIES),
                            new FontAwesomeGlyph('box', [], null, 'fas'), $this->get_url(
                            [self::PARAM_ACTION => self::ACTION_INSTALL, self::PARAM_CONTEXT => $context]
                        ), ToolbarItem::DISPLAY_ICON_AND_LABEL,
                            $translator->trans('ConfirmChosenAction', [], StringUtilities::LIBRARIES)
                        )
                    );
                }

                $buttonToolbar->addButtonGroup($commonActions);
            }
            catch (OutOfBoundsException)
            {
            }

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    public function getPackageInformationRenderer(): PackageInformationRenderer
    {
        return $this->getService(PackageInformationRenderer::class);
    }
}
