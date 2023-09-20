<?php
namespace Chamilo\Core\Lynx\Service;

use Chamilo\Configuration\Package\Properties\Dependencies\DependencyVerifier;
use Chamilo\Configuration\Package\Service\PackageFactory;
use Chamilo\Configuration\Package\Storage\DataClass\Package;
use Chamilo\Configuration\Service\Consulter\RegistrationConsulter;
use Chamilo\Core\Lynx\Manager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

class PackageInformationRenderer
{
    protected PackageFactory $packageFactory;

    protected RegistrationConsulter $registrationConsulter;

    protected StringUtilities $stringUtilities;

    protected Translator $translator;

    public function __construct(
        PackageFactory $packageFactory, RegistrationConsulter $registrationConsulter, Translator $translator,
        StringUtilities $stringUtilities
    )
    {
        $this->packageFactory = $packageFactory;
        $this->registrationConsulter = $registrationConsulter;
        $this->translator = $translator;
        $this->stringUtilities = $stringUtilities;
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
            $html[] = $this->verifyDependencies();
        }

        return implode(PHP_EOL, $html);
    }

    public function getDependenciesTable()
    {
        $package_info = $this->get_package_info();

        $html = [];

        if ($package_info->has_dependencies())
        {
            $html[] = '<h3>' . Translation::get('Dependencies') . '</h3>';

            $html[] = '<table class="table table-striped table-bordered table-hover table-data">';

            if (!is_null($package_info->get_dependencies()))
            {
                $html[] = '<tr>';
                $html[] = '<td class="header">' . Translation::get('PreDepends') . '</td>';
                $html[] = '<td>' . $package_info->get_dependencies()->as_html() . '</td>';
                $html[] = '</tr>';
            }

            $html[] = '</table><br/>';
        }

        return implode(PHP_EOL, $html);
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

        $html[] = '<table class="table table-striped table-bordered table-hover table-data data_table_no_header">';

        $properties = $package::getDefaultPropertyNames();

        $hidden_properties = [
            Package::PROPERTY_AUTHORS,
            Package::PROPERTY_VERSION,
            Package::PROPERTY_DEPENDENCIES,
            Package::PROPERTY_EXTRA
        ];

        foreach ($properties as $property)
        {
            $value = $package->getDefaultProperty($property);

            if (!empty($value) && !in_array($property, $hidden_properties))
            {
                $html[] = '<tr><td class="header">' . $translator->trans(
                        $stringUtilities->createString($property)->upperCamelize()->toString()
                    ) . '</td><td>' . $value . '</td></tr>';
            }
        }

        $authors = $package->get_authors();

        foreach ($authors as $key => $author)
        {

            $html[] = '<tr><td class="header">';

            if ($key == 0)
            {
                $html[] = $translator->trans('Authors', [], Manager::CONTEXT);
            }

            $html[] = '</td><td>' . $stringUtilities->encryptMailLink($author->get_email(), $author->get_name()) .
                '</td></tr>';
        }

        $html[] = '</table><br/>';

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

    public function verifyDependencies()
    {
        $package_dependency = new DependencyVerifier($this->get_package_info());
        $success = $package_dependency->is_installable();

        $html = [];

        $html[] = '<h3>' . Translation::get(
                'InstallationDependencies', ['VERSION' => $this->get_package_info()->get_version()]
            ) . '</h3>';

        $html[] = '<div class="panel panel-' . ($success ? 'success' : 'danger') . '">';
        $html[] = '<div class="panel-heading">';
        $html[] = '<h3 class="panel-title">' . Translation::get('DependenciesResultVerification') . '</h3>';
        $html[] = '</div>';
        $html[] = '<div class="panel-body">';
        $html[] = $package_dependency->get_logger()->render();
        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}
