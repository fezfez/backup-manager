<?php

declare(strict_types=1);

namespace Fezfez\BackupManager;

use Fezfez\BackupManager\Compressors\Compressor;
use Fezfez\BackupManager\Databases\Database;
use Fezfez\BackupManager\Filesystems\BackupManagerFilesystemAdapter;
use Fezfez\BackupManager\Filesystems\Destination;
use Fezfez\BackupManager\Filesystems\LocalFilesystemAdapter;
use Fezfez\BackupManager\Procedures\Backup;
use Fezfez\BackupManager\Procedures\BackupProcedure;
use Fezfez\BackupManager\Procedures\Restore;
use Fezfez\BackupManager\Procedures\RestoreProcedure;

/**
 * This is a facade class that gives consumers access to the simple backup and restore procedures.
 * This class can be copied and namespaced into your project, renamed, added to, modified, etc.
 * Once you've done that, your application can interact with the backup manager in one place only
 * and the rest of the system will interact with the new Manager-like construct that you created.
 */
class BackupManager implements BackupManagerContract
{
    private BackupProcedure $backupProcedure;
    private RestoreProcedure $restoreProcedure;

    public function __construct(
        BackupProcedure|null $backupProcedure = null,
        RestoreProcedure|null $restoreProcedure = null,
    ) {
        $this->backupProcedure  = $backupProcedure ?? new Backup();
        $this->restoreProcedure = $restoreProcedure ?? new Restore();
    }

    /** @param Destination[] $destinations */
    public function backup(
        LocalFilesystemAdapter $localFileSystem,
        Database $database,
        array $destinations,
        Compressor ...$compression,
    ): void {
        $this->backupProcedure->__invoke($localFileSystem, $database, $destinations, ...$compression);
    }

    public function restore(
        LocalFilesystemAdapter $localFileSystem,
        BackupManagerFilesystemAdapter $to,
        string $sourcePath,
        Database $databaseName,
        Compressor ...$compression,
    ): void {
        $this->restoreProcedure->__invoke($localFileSystem, $to, $sourcePath, $databaseName, ...$compression);
    }
}
