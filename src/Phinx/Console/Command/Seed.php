<?php

namespace Phinx\Console\Command;


use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Seed extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('seed')
            ->setDescription('Seeds the database')
            ->addOption('--environment', '-e', InputOption::VALUE_REQUIRED, 'The target environment')
            ->addOption('--target', '-t', InputOption::VALUE_REQUIRED, 'Name of a seeder class to use')
            ->setHelp(
                <<<EOT
                The <info>seed</info> command seeds the database with data specified in seeder classes

<info>phinx seed -e development</info>
<info>phinx seed -e development -c MySpecificSeeder</info>

EOT
            );
    }

    /**
     * Seeds the database.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->bootstrap($input, $output);

        $environment = $input->getOption('environment');
        $class = $input->getOption('target');

        if (null === $environment) {
            $environment = $this->getConfig()->getDefaultEnvironment();
            $output->writeln('<comment>warning</comment> no environment specified, defaulting to: ' . $environment);
        } else {
            $output->writeln('<info>using environment</info> ' . $environment);
        }

        $envOptions = $this->getConfig()->getEnvironment($environment);
        if (isset($envOptions['adapter'])) {
            $output->writeln('<info>using adapter</info> ' . $envOptions['adapter']);
        }

        if (isset($envOptions['name'])) {
            $output->writeln('<info>using database</info> ' . $envOptions['name']);
        }

        // start seeding
        $start = microtime(true);
        $this->getManager()->seed($environment, $class);
        $end = microtime(true);

        $output->writeln('');
        $output->writeln('<comment>All Done. Took ' . sprintf('%.4fs', $end - $start) . '</comment>');
    }
} 