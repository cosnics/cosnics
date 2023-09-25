<?php
namespace Chamilo\Core\Lynx\Component;

abstract class AdditionalActionComponent extends ActionComponent
{
    /**
     * @var string[] $additionalPackages
     */
    protected array $additionalPackageContexts = [];

    public function addAdditionalPackageContext(string $context): void
    {
        $this->additionalPackageContexts[] = $context;
    }

    public function addAdditionalPackageContexts(array $additional_packages): void
    {
        foreach ($additional_packages as $additional_package)
        {
            $this->addAdditionalPackageContext($additional_package);
        }
    }

    public function getNextAdditionalPackageContext(): string
    {
        return array_shift($this->additionalPackageContexts);
    }
}
