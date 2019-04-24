<?php

declare(strict_types=1);

/*
 * This file is part of the behat/helpers project.
 *
 * (c) Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ekino\BehatHelpers;

use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Cocur\Slugify\Slugify;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Process\Process;

/**
 * @author Rémi Marseille <remi.marseille@ekino.com>
 */
trait ReloadDatabaseTrait
{
    /**
     * @var string
     */
    private $databaseDump;

    /**
     * @BeforeScenario
     *
     * @param BeforeScenarioScope $scope
     *
     * @throws \RuntimeException
     */
    public function dumpDatabaseOnBeforeScenario(BeforeScenarioScope $scope): void
    {
        $tags = array_merge($scope->getFeature()->getTags(), $scope->getScenario()->getTags());

        if (!\in_array('behat_helpers_restore_db', $tags)) {
            return;
        }

        if (!\in_array(KernelDictionary::class, class_uses($this))) {
            throw new \RuntimeException(sprintf('Please use the trait %s in the class %s', KernelDictionary::class, __CLASS__));
        }

        $this->databaseDump = sprintf('%s/%s.sql',
            $this->getContainer()->getParameter('kernel.cache_dir'),
            (new Slugify())->slugify($scope->getScenario()->getTitle())
        );

        if (file_exists($this->databaseDump)) {
            throw new \RuntimeException(sprintf('File %s already exists', $this->databaseDump));
        }

        $connection = $this->getContainer()->get('doctrine')->getConnection();

        switch ($platformName = $connection->getDatabasePlatform()->getName()) {
            case 'mysql':
                $command = sprintf('mysqldump -h %s -P %s -u %s %s %s > %s',
                    $connection->getHost(),
                    $connection->getPort(),
                    $connection->getUsername(),
                    $connection->getPassword() ? sprintf('-p %s', $connection->getPassword()) : '',
                    $connection->getParams()['dbname'],
                    $this->databaseDump
                );
                break;
            default:
                throw new \RuntimeException(sprintf('Platform %s is not supported yet. Feel free to contribute ;).', $platformName));
        }

        $process = new Process($command);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException(sprintf('Error during database dump: %s', $process->getErrorOutput()));
        }

        echo 'Database has been saved';
    }

    /**
     * @AfterScenario
     *
     * @throws \RuntimeException
     */
    public function restoreDatabaseAfterScenario(AfterScenarioScope $scope): void
    {
        if (!$this->databaseDump) {
            return;
        }

        $output = new BufferedOutput();

        $application = new Application($this->getKernel());
        $application->setAutoExit(false);

        $status = $application->run(new ArrayInput([
            'command' => 'doctrine:database:import',
            'file'    => $this->databaseDump,
        ]), $output);

        if ($status !== 0) {
            throw new \RuntimeException(sprintf('Error during database restore: %s', $output->fetch()));
        }

        unlink($this->databaseDump);

        echo 'Database has been restored';
    }
}
