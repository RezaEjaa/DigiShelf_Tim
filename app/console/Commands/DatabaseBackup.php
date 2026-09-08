<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup database otomatis';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            $host = config('database.connections.mysql.host');

            $backupPath = storage_path('app/laravel-backup');

            if (!file_exists($backupPath)) {
                mkdir($backupPath, 0755, true);
            }

            $filename = 'backup-' . date('Y-m-d-His') . '.sql';
            $filepath = $backupPath . '/' . $filename;

            // Path to mysqldump
            $mysqldumpPath = 'C:\\laragon\\bin\\mysql\\mysql-8.4.3-winx64\\bin\\mysqldump.exe';

            // Build command
            $command = sprintf(
                '"%s" --user=%s %s --host=%s %s > "%s"',
                $mysqldumpPath,
                $username,
                $password ? '--password=' . $password : '',
                $host,
                $database,
                $filepath
            );

            // Execute backup
            exec($command, $output, $returnCode);

            if ($returnCode !== 0 || !file_exists($filepath)) {
                $this->error('Backup gagal dibuat');
                return 1;
            }

            // Delete old backups (keep last 7 days)
            $this->cleanOldBackups($backupPath);

            $this->info('Backup berhasil dibuat: ' . $filename);
            return 0;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }

    private function cleanOldBackups(string $backupPath)
    {
        $files = glob($backupPath . '/backup-*.sql');

        if (count($files) > 7) {
            // Sort by modification time
            usort($files, function($a, $b) {
                return filemtime($a) - filemtime($b);
            });

            // Delete oldest files, keep last 7
            $filesToDelete = array_slice($files, 0, count($files) - 7);

            foreach ($filesToDelete as $file) {
                @unlink($file);
            }
        }
    }
}
