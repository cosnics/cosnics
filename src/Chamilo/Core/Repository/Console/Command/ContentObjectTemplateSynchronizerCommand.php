<?php

namespace Chamilo\Core\Repository\Console\Command;

use Chamilo\Core\Repository\Service\ContentObjectTemplate\ContentObjectTemplateSynchronizer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Translation\Translator;

/**
 * Executes the content object template synchronizer service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ContentObjectTemplateSynchronizerCommand extends Command
{
    const ARG_CONTENT_OBJECT_TYPE = 'content_object_type';

    /**
     * @var ContentObjectTemplateSynchronizer
     */
    protected $contentObjectTemplateSynchronizer;

    /**
     * The translator
     *
     * @var \Symfony\Component\Translation\Translator
     */
    protected $translator;

    /**
     * ContentObjectTemplateSynchronizerCommand constructor.
     *
     * @param ContentObjectTemplateSynchronizer $contentObjectTemplateSynchronizerDirector
     * @param Translator $translator
     */
    public function __construct(
        ContentObjectTemplateSynchronizer $contentObjectTemplateSynchronizerDirector, Translator $translator
    )
    {
        $this->contentObjectTemplateSynchronizer = $contentObjectTemplateSynchronizerDirector;
        $this->translator = $translator;

        parent::__construct();
    }

    /**
     * Configures the current command
     */
    protected function configure()
    {
        $this->setName('chamilo:repository:content_object:template_synchronizer')
            ->setDescription(
                $this->translator->trans(
                    'ContentObjectTemplateSynchronizerDescription', array(), 'Chamilo\Core\Repository'
                )
            )
            ->addArgument(
                self::ARG_CONTENT_OBJECT_TYPE, InputArgument::REQUIRED,
                $this->translator->trans('ContentObjectType', array(), 'Chamilo\Core\Repository')
            );
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->contentObjectTemplateSynchronizer->synchronize($input->getArgument(self::ARG_CONTENT_OBJECT_TYPE));
    }

}