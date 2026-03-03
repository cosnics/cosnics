<?php
namespace Chamilo\Core\User\Service;

use Chamilo\Core\Admin\Service\PackageBundlesCacheService;
use Chamilo\Core\Admin\Storage\DataClass\Package;
use Chamilo\Libraries\Filesystem\Service\SystemPathBuilder;
use DOMDocument;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\User\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserSettingsParser
{
    protected PackageBundlesCacheService $packageBundlesCacheService;

    protected SystemPathBuilder $systemPathBuilder;

    protected Translator $translator;

    public function __construct(
        SystemPathBuilder $systemPathBuilder, Translator $translator,
        PackageBundlesCacheService $packageBundlesCacheService
    )
    {
        $this->systemPathBuilder = $systemPathBuilder;
        $this->translator = $translator;
        $this->packageBundlesCacheService = $packageBundlesCacheService;
    }

    public function determineConfigurablePackageContextSettings(string $packageContext): array
    {
        $settings = [];

        if ($this->isConfigurablePackageContext($packageContext)) {
            $doc = new DOMDocument();
            $doc->load($this->getConfigurablePackageContextPath($packageContext));

            // Get categories
            $categories = $doc->getElementsByTagName('category');

            foreach ($categories as $category) {
                $categoryName = $category->getAttribute('name');
                $categoryProperties = [];

                // Get settings in category
                $properties = $category->getElementsByTagname('setting');
                $attributes = ['field', 'default', 'locked', 'user_setting', 'hidden'];

                foreach ($properties as $property) {
                    $propertyInfo = [];

                    foreach ($attributes as $attribute) {
                        if ($property->hasAttribute($attribute)) {
                            $propertyInfo[$attribute] = $property->getAttribute($attribute);
                        }
                    }

                    if ($property->hasChildNodes()) {
                        $propertyOptions = $property->getElementsByTagname('options')->item(0);

                        if ($propertyOptions) {
                            $propertyOptionsAttributes = ['type', 'source'];

                            foreach ($propertyOptionsAttributes as $optionsAttribute) {
                                if ($propertyOptions->hasAttribute($optionsAttribute)) {
                                    $propertyInfo['options'][$optionsAttribute] = $propertyOptions->getAttribute(
                                        $optionsAttribute
                                    );
                                }
                            }

                            if ($propertyOptions->getAttribute('type') == 'static' &&
                                $propertyOptions->hasChildNodes()) {
                                $options = $propertyOptions->getElementsByTagname('option');
                                $optionsInfo = [];
                                foreach ($options as $option) {
                                    $optionsInfo[$option->getAttribute('value')] = $option->getAttribute('name');
                                }
                                $propertyInfo['options']['values'] = $optionsInfo;
                            }
                        }

                        $propertyAvailability = $property->getElementsByTagname('availability')->item(0);

                        if ($propertyAvailability) {
                            $propertyAvailabilityAttributes = ['source'];

                            foreach ($propertyAvailabilityAttributes as $availabilityAttribute) {
                                if ($propertyAvailability->hasAttribute($availabilityAttribute)) {
                                    $propertyInfo['availability'][$availabilityAttribute] =
                                        $propertyAvailability->getAttribute(
                                            $availabilityAttribute
                                        );
                                }
                            }
                        }
                    }
                    $categoryProperties[$property->getAttribute('name')] = $propertyInfo;
                }

                $settings[$categoryName] = $categoryProperties;
            }
        }

        return $settings;
    }

    /**
     * @return \Chamilo\Core\Admin\Storage\DataClass\Package[]
     */
    public function determineConfigurablePackages(): array
    {
        $packages = $this->getPackageBundlesCacheService()->getPackages();
        $configurablePackages = [];

        foreach ($packages as $package) {
            $packageContext = $package->getContext();
            $settingsFilePath =
                $this->getSystemPathBuilder()->namespaceToFullPath($packageContext) . 'Resources/Settings/settings.xml';

            if (file_exists($settingsFilePath)) {
                $configurablePackages[] = $package;
            }
        }

        usort($configurablePackages, [$this, 'orderConfigurablePackages']);

        return $configurablePackages;
    }

    public function determineConfigurableSettings(): array
    {
        $configurablePackages = $this->determineConfigurablePackages();
        $configurableSettings = [];

        foreach ($configurablePackages as $configurablePackage) {
            $configurableSettings[$configurablePackage->getContext()] =
                $this->determineConfigurablePackageContextSettings(
                    $configurablePackage->getContext()
                );
        }

        return $configurableSettings;
    }

    protected function getConfigurablePackageContextPath(string $packageContext): string
    {
        return $this->getSystemPathBuilder()->namespaceToFullPath($packageContext) . 'Resources/Settings/settings.xml';
    }

    protected function getConfigurablePackagePath(Package $package): string
    {
        return $this->getConfigurablePackageContextPath($package->getContext());
    }

    protected function getPackageBundlesCacheService(): PackageBundlesCacheService
    {
        return $this->packageBundlesCacheService;
    }

    protected function getSystemPathBuilder(): SystemPathBuilder
    {
        return $this->systemPathBuilder;
    }

    protected function getTranslator(): Translator
    {
        return $this->translator;
    }

    protected function isConfigurablePackage(Package $package): bool
    {
        return $this->isConfigurablePackageContext($package->getContext());
    }

    protected function isConfigurablePackageContext(string $packageContext): bool
    {
        return file_exists($this->getConfigurablePackageContextPath($packageContext));
    }

    protected function orderConfigurablePackages(Package $packageLeft, Package $packageRight): int
    {
        $translator = $this->getTranslator();

        return strcasecmp($translator->trans('TypeName', [], $packageLeft->getContext()),
            $translator->trans('TypeName', [], $packageRight->getContext()));
    }
}