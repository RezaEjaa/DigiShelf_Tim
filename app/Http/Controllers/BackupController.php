<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Spatie\Backup\BackupDestination\BackupDestination;

class BackupController extends Controller
{
    public function index()
    {
        $destination = BackupDestination::create('local', 'Laravel');

        $backups = $destination->backups();

        return view('admin.backup', compact('backups'));
    }

    public function create()
    {
        Artisan::call('backup:run', [
            '--only-db' => true,
        ]);

        return back()->with('success', 'Backup database berhasil dibuat.');
    }

    public function download($filename)
    {
        $destination = BackupDestination::create('local', 'Laravel');

        foreach ($destination->backups() as $backup) {
            if (basename($backup->path()) === basename($filename)) {
                return response()->download($backup->path());
            }
        }

        abort(404);
    }
}
