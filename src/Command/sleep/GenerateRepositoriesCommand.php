<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

#[AsCommand(
    name: 'app:generate:repositories',
    description: 'Generates repository classes for all entities.',
)]
class GenerateRepositoriesCommand extends Command
{
    protected function configure(): void
    {
        $this->setDescription('Generates repository classes for all entities.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filesystem = new Filesystem();
        $finder = new Finder();

        $output->writeln('Generating repositories for all entities...');

        $finder->files()->in('src/Entity')->name('*.php');

        foreach ($finder as $file) {
            $entityClass = $file->getBasename('.php');

            $repositoryCode = <<<PHP
<?php

namespace App\Repository;

use App\Entity\\{$entityClass};
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class {$entityClass}Repository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry \$registry)
    {
        parent::__construct(\$registry, {$entityClass}::class);
    }
}
PHP;

            $repositoryPath = 'src/Repository/' . $entityClass . 'Repository.php';

            if (!$filesystem->exists($repositoryPath)) {
                $filesystem->dumpFile($repositoryPath, $repositoryCode);
                $output->writeln("Generated repository: {$entityClass}Repository");
            } else {
                $output->writeln("Repository already exists for: {$entityClass}");
            }
        }

        $output->writeln('Repository generation complete!');
        return Command::SUCCESS;
    }
}