<?php

namespace App\Http\Controllers;

use App\Services\DatabaseBackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class BackupController extends Controller
{
    protected DatabaseBackupService $backupService;

    public function __construct(DatabaseBackupService $backupService)
    {
        $this->backupService = $backupService;
    }

    /**
     * Display the Admin Backup & Restore Management Dashboard page.
     */
    public function index()
    {
        $user = Auth::user();

        // Security check: Only Admin / Full Access accounts can access Backup Management
        if (!$user || !$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak. Fitur Backup & Restore hanya dapat diakses oleh akun Admin.');
        }

        $backups = $this->backupService->getBackupList();
        $totalBackups = count($backups);
        $totalSizeBytes = array_sum(array_column($backups, 'sizeBytes'));
        $totalSizeFormatted = $totalSizeBytes > 1048576 
            ? number_format($totalSizeBytes / 1048576, 2, ',', '.') . ' MB'
            : number_format($totalSizeBytes / 1024, 1, ',', '.') . ' KB';
        $latestBackupDate = !empty($backups) ? $backups[0]['createdAt'] : 'Belum Ada Backup';

        return view('admin.backups.index', compact(
            'user',
            'backups',
            'totalBackups',
            'totalSizeFormatted',
            'latestBackupDate'
        ));
    }

    /**
     * Create a new instant backup snapshot.
     */
    public function create()
    {
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            return redirect()->back()->with('error', 'Akses ditolak. Hanya Admin yang dapat membuat backup.');
        }

        $res = $this->backupService->createBackup();

        if ($res['success']) {
            return redirect()->back()->with('success', $res['message']);
        }

        return redirect()->back()->with('error', $res['message']);
    }

    /**
     * Download a specific backup file.
     */
    public function download(string $fileName)
    {
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            return redirect()->back()->with('error', 'Akses ditolak. Hanya Admin yang dapat mengunduh backup.');
        }

        $cleanName = basename($fileName);
        $filePath = storage_path('app/backups/' . $cleanName);

        if (!File::exists($filePath)) {
            return redirect()->back()->with('error', 'File backup tidak ditemukan.');
        }

        return response()->download($filePath, $cleanName, [
            'Content-Type' => 'application/sql',
        ]);
    }

    /**
     * Restore database from an existing file or uploaded backup file.
     */
    public function restore(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            return redirect()->back()->with('error', 'Akses ditolak. Hanya Admin yang dapat memulihkan (restore) data.');
        }

        $request->validate([
            'file_name' => 'nullable|string',
            'backup_file' => 'nullable|file|mimes:sql,txt,sqlite|max:102400',
        ]);

        $filePath = null;

        // Case 1: Restoring from an uploaded file from user's computer
        if ($request->hasFile('backup_file')) {
            $uploaded = $request->file('backup_file');
            $tempName = 'temp_restore_' . date('Y-m-d_H-i-s') . '.' . $uploaded->getClientOriginalExtension();
            $targetDir = storage_path('app/backups');
            $uploaded->move($targetDir, $tempName);
            $filePath = $targetDir . '/' . $tempName;
        } 
        // Case 2: Restoring from existing backup history file on server
        elseif ($request->has('file_name') && !empty($request->file_name)) {
            $cleanName = basename($request->file_name);
            $filePath = storage_path('app/backups/' . $cleanName);
        }

        if (!$filePath || !File::exists($filePath)) {
            return redirect()->back()->with('error', 'Silakan pilih atau unggah file backup `.sql` yang valid untuk di-restore.');
        }

        $res = $this->backupService->restoreBackup($filePath);

        if ($res['success']) {
            return redirect()->back()->with('success', $res['message']);
        }

        return redirect()->back()->with('error', $res['message']);
    }

    /**
     * Delete a specific backup file.
     */
    public function destroy(string $fileName)
    {
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $success = $this->backupService->deleteBackup($fileName);

        if ($success) {
            return redirect()->back()->with('success', "File backup {$fileName} berhasil dihapus.");
        }

        return redirect()->back()->with('error', "Gagal menghapus file backup.");
    }
}
