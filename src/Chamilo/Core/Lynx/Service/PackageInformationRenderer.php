<?php
namespace Chamilo\Core\Lynx\Service;

use Chamilo\Configuration\Package\Properties\Dependencies\DependencyRenderer;
use Chamilo\Configuration\Package\Properties\Dependencies\DependencyVerifier;
use Chamilo\Configuration\Package\Properties\Dependencies\DependencyVerifierRenderer;
use Chamilo\Configuration\Package\Service\PackageFactory;
use Chamilo\Configuration\Package\Storage\DataClass\Package;
use Chamilo\Configuration\Service\Consulter\RegistrationConsulter;
use Chamilo\Core\Lynx\Manager;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

class PackageInformationRenderer
{
    protected DependencyRenderer $dependencyRenderer;

    protected DependencyVerifier $dependencyVerifier;

    protected DependencyVerifierRenderer $dependencyVerifierRenderer;

    protected PackageFactory $packageFactory;

    protected RegistrationConsulter $registrationConsulter;

    protected StringUtilities $stringUtilities;

    protected Translator $translator;

    public function __construct(
        PackageFactory $packageFactory, RegistrationConsulter $registrationConsulter, Translator $translator,
        StringUtilities $stringUtilities, DependencyVerifier $dependencyVerifier,
        DependencyVerifierRenderer $dependencyVerifierRenderer, DependencyRenderer $dependencyRenderer
    )
    {
        $this->packageFactory = $packageFactory;
        $this->registrationConsulter = $registrationConsulter;
        $this->translator = $translator;
        $this->stringUtilities = $stringUtilities;
        $this->dependencyVerifier = $dependencyVerifier;
        $this->dependencyVerifierRenderer = $dependencyVerifierRenderer;
        $this->dependencyRenderer = $dependencyRenderer;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \Exception
     */
    public function render(string $context): string
    {
        $package = $this->getPackageFactory()->getPackage($context);

        $html = [];

        $html[] = $this->getPropertiesTable($package);
        $html[] = $this->getDependenciesTable($package);

        $registration = $this->getRegistrationConsulter()->getRegistrationForContext($context);

        if (!empty($registration))
        {
            $html[] = $this->verifyDependencies($package);
        }

        return implode(PHP_EOL, $html);
    }

    public function getDependenciesTable(Package $package): string
    {
        $translator = $this->getTranslator();
        $html = [];

        if ($package->has_dependencies())
        {
            $html[] = '<div class="panel panel-default">';
            $html[] = '<div class="panel-heading">';
            $html[] = '<h4 class="panel-title">' . $translator->trans('Dependencies', [], Manager::CONTEXT) . '</h4>';
            $html[] = '</div>';
            $html[] = '<div class="panel-body">';
            $html[] = $this->getDependencyRenderer()->renderDependencies($package);
            $html[] = '</div>';
            $html[] = '</div>';
        }

        return implode(PHP_EOL, $html);
    }

    public function getDependencyRenderer(): DependencyRenderer
    {
        return $this->dependencyRenderer;
    }

    public function getDependencyVerifier(): DependencyVerifier
    {
        return $this->dependencyVerifier;
    }

    public function getDependencyVerifierRenderer(): DependencyVerifierRenderer
    {
        return $this->dependencyVerifierRenderer;
    }

    public function getPackageFactory(): PackageFactory
    {
        return $this->packageFactory;
    }

    public function getPropertiesTable(Package $package): string
    {
        $translator = $this->getTranslator();
        $stringUtilities = $this->getStringUtilities();

        $html = [];

        $html[] = '<div class="panel panel-default">';
        $html[] = '<div class="panel-heading">';
        $html[] = '<h4 class="panel-title">' . $translator->trans('Properties', [], Manager::CONTEXT) . '</h4>';
        $html[] = '</div>';
        //        $html[] = '<div class="panel-body">';
        //        $html[] = $this->getDependencyVerifierRenderer()->renderVerifiedDependencies($package);
        //        $html[] = '</div>';

        $html[] = '<table class="table table-bordered table-hover table-data data_table_no_header">';

        $properties = Package::getDefaultPropertyNames();

        $hiddenProperties = [
            Package::PROPERTY_AUTHORS,
            Package::PROPERTY_VERSION,
            Package::PROPERTY_DEPENDENCIES,
            Package::PROPERTY_EXTRA,
            Package::PROPERTY_RESOURCES
        ];

        foreach ($properties as $property)
        {
            $value = $package->getDefaultProperty($property);

            if (!empty($value) && !in_array($property, $hiddenProperties))
            {
                $html[] = '<tr><th class="header cell-stat-2x">' . $translator->trans(
                        $stringUtilities->createString($property)->upperCamelize()->toString(), [],
                        'Chamilo\Configuration'
                    ) . '</th><td>' . $value . '</td></tr>';
            }
        }

        $authors = $package->get_authors();

        foreach ($authors as $key => $author)
        {

            $html[] = '<tr><th class="header cell-stat-2x">';

            if ($key == 0)
            {
                $html[] = $translator->trans('Authors', [], Manager::CONTEXT);
            }

            $html[] = '</th><td>' . $stringUtilities->encryptMailLink($author->get_email(), $author->get_name()) .
                '</td></tr>';
        }

        $html[] = '</table>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function getRegistrationConsulter(): RegistrationConsulter
    {
        return $this->registrationConsulter;
    }

    public function getStringUtilities(): StringUtilities
    {
        return $this->stringUtilities;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function verifyDependencies(Package $package): string
    {
        $translator = $this->getTranslator();
        $success = $this->getDependencyVerifier()->isInstallable($package);

        $html = [];

        $html[] = '<div class="panel panel-' . ($success ? 'success' : 'danger') . '">';
        $html[] = '<div class="panel-heading">';
        $html[] =
            '<h4 class="panel-title">' . $translator->trans('InstallationDependencies', [], Manager::CONTEXT) . '</h4>';
        $html[] = '</div>';
        $html[] = '<div class="panel-body">';
        $html[] = $this->getDependencyVerifierRenderer()->renderVerifiedDependencies($package);
        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}
