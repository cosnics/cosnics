<?php
namespace Chamilo\Libraries\Protocol\Console\Architecture\Domain;

use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Doctrine\ORM\EntityManager;
use RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Protocol\Console\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DoctrineProxyCacheCommand extends ChamiloCommand
{
    public function __construct(
        protected EntityManager $entityManager, protected Filesystem $filesystem, Translator $translator
    )
    {
        parent::__construct($translator);
    }

    protected function configure(): void
    {
        $this->setName('chamilo:cache:doctrineProxy')->setDescription(
            $this->translator->trans('GenerateDoctrineProxyCacheCommandDescription', [], StringUtilities::LIBRARIES)
        );
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!is_dir($proxyCacheDir = $this->entityManager->getConfiguration()->getProxyDir())) {
            $this->filesystem->mkdir($proxyCacheDir);
        }
        elseif (!is_writable($proxyCacheDir)) {
            throw new RuntimeException(
                sprintf(
                    'The Doctrine Proxy directory "%s" is not writeable for the current system user.', $proxyCacheDir
                )
            );
        }

        if ($this->entityManager->getConfiguration()->getAutoGenerateProxyClasses()) {
            return 0;
        }

        $classes = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $this->entityManager->getProxyFactory()->generateProxyClasses($classes);

        return 0;
    }
}