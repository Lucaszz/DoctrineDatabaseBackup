<?php

namespace Lucaszz\DoctrineDatabaseBackup\Backup;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Lucaszz\DoctrineDatabaseBackup\Backup\Executor\MySqlExecutor;
use Lucaszz\DoctrineDatabaseBackup\Backup\Executor\SqliteExecutor;

class ExecutorFactory
{
    /** @var Connection */
    private $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function create()
    {
        $params = $this->connection->getParams();

        if ($this->connection->getDatabasePlatform() instanceof SqlitePlatform) {
            if (false === isset($params['path']) || $params['path'] == ':memory:') {
                throw new \RuntimeException('Backup for Sqlite "in_memory" is not supported.');
            }

            return new SqliteExecutor($params['path'], new Filesystem(), new BackupFile(__DIR__.'/../../tests/Integration/database'));
        }

        if ($this->connection->getDatabasePlatform() instanceof MySqlPlatform) {
            return new MySqlExecutor($params['dbname'], new Filesystem(), new BackupFile(__DIR__.'/../../tests/Integration/database'));
        }

        throw new \RuntimeException('Unsupported database platform. Currently "SqlitePlatform" is supported.');
    }
}
