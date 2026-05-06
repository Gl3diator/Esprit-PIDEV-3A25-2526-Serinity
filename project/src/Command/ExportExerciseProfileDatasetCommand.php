<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\UserRepository;
use App\Service\User\ExerciseProfileDatasetBuilder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:export-exercise-profile-dataset',
    description: 'Export the exercise profile training dataset as one row per user.',
)]
final class ExportExerciseProfileDatasetCommand extends Command
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly ExerciseProfileDatasetBuilder $datasetBuilder,
        private readonly string $projectDir,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Export exercise profile dataset');

        $rows = $this->datasetBuilder->buildDatasetRows($this->userRepository->findAllNonAdmin());
        $directory = $this->projectDir . DIRECTORY_SEPARATOR . 'ml' . DIRECTORY_SEPARATOR . 'exercise_profile';
        $path = $directory . DIRECTORY_SEPARATOR . 'dataset.csv';

        if (!is_dir($directory) && !mkdir($directory, 0777, true) && !is_dir($directory)) {
            $io->error('Unable to create the dataset directory.');

            return Command::FAILURE;
        }

        $handle = fopen($path, 'wb');
        if ($handle === false) {
            $io->error('Unable to open the dataset file for writing.');

            return Command::FAILURE;
        }

        $header = ['user_id', ...$this->datasetBuilder->featureColumns(), 'target_label'];
        fputcsv($handle, $header);

        foreach ($rows as $row) {
            $csvRow = [];
            foreach ($header as $column) {
                $csvRow[] = $row[$column] ?? '';
            }

            fputcsv($handle, $csvRow);
        }

        fclose($handle);

        $io->success(sprintf('Dataset exported to %s', $path));
        $io->text(sprintf('Exported %d user rows.', count($rows)));

        return Command::SUCCESS;
    }
}
