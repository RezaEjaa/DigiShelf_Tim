<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class BackupController extends Controller
{
    public function index()
    {
        $backupPath = 'laravel-backup';
        $disk = Storage::disk('local');

        $backups = collect();

        if ($disk->exists($backupPath)) {
            $files = $disk->files($backupPath);

            foreach ($files as $file) {
                $backups->push([
                    'path' => $file,
                    'name' => basename($file),
                    'size' => $disk->size($file),
                    'date' => $disk->lastModified($file),
                ]);
            }
        }

        $backups = $backups->sortByDesc('date');

        return view('admin.backup', compact('backups'));
    }

    public function create()
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
                '"%s" --user=%s --password=%s --host=%s %s > "%s"',
                $mysqldumpPath,
                $username,
                $password,
                $host,
                $database,
                $filepath
            );

            // Execute backup
            exec($command, $output, $returnCode);

            if ($returnCode !== 0 || !file_exists($filepath)) {
                throw new \Exception('Backup gagal dibuat');
            }

            return back()->with('success', 'Backup database berhasil dibuat: ' . $filename);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat backup: ' . $e->getMessage());
        }
    }

    public function download(string $filename)
    {
        $backupPath = 'laravel-backup';
        $disk = Storage::disk('local');
        $filePath = $backupPath . '/' . basename($filename);

        if ($disk->exists($filePath)) {
            return response()->download($disk->path($filePath));
        }

        abort(404);
    }
}
