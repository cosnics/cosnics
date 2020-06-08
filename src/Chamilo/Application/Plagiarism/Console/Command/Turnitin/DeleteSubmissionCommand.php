<?php

namespace Chamilo\Application\Plagiarism\Console\Command\Turnitin;

use Chamilo\Application\Plagiarism\Service\Turnitin\SubmissionService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @package Chamilo\Application\Plagiarism\Console\Command
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class DeleteSubmissionCommand extends Command
{
    const ARG_SUBMISSION_ID = 'submission_id';

    /**
     * @var SubmissionService
     */
    protected $submissionService;

    /**
     * DeleteSubmissionCommand constructor.
     *
     * @param SubmissionService $submissionService
     */
    public function __construct(SubmissionService $submissionService)
    {
        $this->submissionService = $submissionService;

        parent::__construct();
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('chamilo:plagiarism:delete_submission')
            ->setDescription('Deletes a submission from turnitin by a given identifier')
            ->addArgument(self::ARG_SUBMISSION_ID, InputArgument::REQUIRED, 'Submission ID');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int|void
     * @throws \Exception
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $submissionId = $input->getArgument(self::ARG_SUBMISSION_ID);

        $this->submissionService->deleteSubmission($submissionId);
    }

}
