<?php
namespace Armd\UtilBundle\Command\DoctrineMigrations;


use Doctrine\Bundle\DoctrineBundle\Command\DoctrineCommand;
use PDO;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

class IgnoreAllCommand extends DoctrineCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('armdutil:migrations:ignore-all')
            ->setDescription('Add all existent migrations to migration_versions table')
            ->setHelp(<<<EOT
The <info>armdutil:migrations:ignore-all</info> adds all existent migrations to migration_versions table.

<info>php app/console armdutil:migrations:ignore-all</info>
EOT
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = $this->getDoctrineConnection(null);
        $tableName = $this->getContainer()->getParameter('doctrine_migrations.table_name');
        $dir = $this->getContainer()->getParameter('doctrine_migrations.dir_name');

        echo $tableName . "\n";
        echo $dir . "\n";

        $files = scandir($dir);
        foreach ($files as $file) {
            if (preg_match('~Version(\d+)\.php~', $file, $matches)) {
                $sql = "SELECT COUNT(*) FROM " . $tableName . " WHERE version = :version";
                $stmt = $connection->prepare($sql);
                $stmt->execute(array('version' => $matches[1]));
                $row  = $stmt->fetch(PDO::FETCH_COLUMN);
                list($count) = $row[0];
                if ($count == 0) {
                    $sql = "INSERT INTO " . $tableName . " (version) VALUES(:version)";
                    $stmt = $connection->prepare($sql);
                    $stmt->execute(array('version' => $matches[1]));

                }
            }
        }
    }
}