<?php

declare(strict_types=1);

namespace Fezfez\BackupManager\Filesystems;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemAdapter as LeagueV3;
use League\Flysystem\FilesystemException;

use function sprintf;

class LeagueFilesystemAdapater implements FilesystemAdapter
{
    private Filesystem $fileSysteme;

    public function __construct(LeagueV3 $adapter)
    {
        $this->fileSysteme = new Filesystem($adapter);
    }

    public function readStream(string $path): BackupManagerRessource
    {
        try {
            return new BackupManagerRessource($this->fileSysteme->readStream($path));
        } catch (FilesystemException $e) {
            throw new CandReadFile(sprintf('cant read file %s', $path));
        }
    }

    public function writeStream(string $path, BackupManagerRessource $resource): void
    {
        try {
            $this->fileSysteme->writeStream($path, $resource->getResource());
        } catch (FilesystemException) {
            throw new CantWriteFile(sprintf('cant write file %s', $path));
        }
    }

    public function delete(string $path): void
    {
        try {
            $this->fileSysteme->delete($path);
        } catch (FilesystemException) {
            throw new CantDeleteFile(sprintf('cant delete file %s', $path));
        }
    }
}
