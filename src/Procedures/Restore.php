<?php

declare(strict_types=1);

namespace Fezfez\BackupManager\Procedures;

use Fezfez\BackupManager\Compressors\Compressor;
use Fezfez\BackupManager\Databases\Database;
use Fezfez\BackupManager\Filesystems\BackupManagerFilesystemAdapter;
use Fezfez\BackupManager\Filesystems\LocalFilesystemAdapter;
use Fezfez\BackupManager\ShellProcessing\ShellProcessor;
use Symfony\Component\Process\Process;

use function sprintf;
use function uniqid;

final class Restore implements RestoreProcedure
{
    private ShellProcessor $shellProcessor;

    public function __construct(ShellProcessor|null $shellProcessor = null)
    {
        $this->shellProcessor = $shellProcessor ?? new ShellProcessor();
    }

    public function __invoke(
        LocalFilesystemAdapter $localFileSystem,
        BackupManagerFilesystemAdapter $to,
        string $sourcePath,
        Database $databaseName,
        Compressor ...$compressorList,
    ): void {
        // begin the life of a new working file
        $workingFile = sprintf('%s/%s', $localFileSystem->getRootPath(), uniqid()) . '.gz';

        // download or retrieve the archived backup file

        $localFileSystem->writeStream($workingFile, $to->readStream($sourcePath));

        // decompress the archived backup
        foreach ($compressorList as $compressor) {
            $workingFile = $compressor->decompress($workingFile);
        }

        $this->shellProcessor->__invoke(Process::fromShellCommandline($databaseName->getRestoreCommandLine($workingFile)));

        $localFileSystem->delete($workingFile);
    }
}
