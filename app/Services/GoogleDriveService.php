<?php

namespace App\Services;

use Google\Client;
use Google\Service\Drive;
use Illuminate\Support\Facades\Cache;

class GoogleDriveService
{
    protected Client $client;
    protected Drive $drive;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setAuthConfig(storage_path('app/google-credentials.json'));
        $this->client->addScope(Drive::DRIVE_READONLY);
        $this->drive = new Drive($this->client);
    }

    /**
     * Ekstrak Folder ID dari URL Google Drive atau ID murni
     */
    public function extractFolderId(string $input): string
    {
        $input = trim($input);

        if (!str_contains($input, 'drive.google.com')) {
            return $input;
        }

        preg_match('/\/folders\/([a-zA-Z0-9_-]+)/', $input, $matches);

        return $matches[1] ?? $input;
    }

    /**
     * Ambil daftar foto dari folder Google Drive (dengan cache)
     */
    public function getPhotosFromFolder(string $folderId): array
    {
        $cacheKey = 'drive_folder_' . $folderId;
        $minutes  = config('app.google_drive_cache_minutes', 10);

        return Cache::remember($cacheKey, now()->addMinutes($minutes), function () use ($folderId) {
            $results = $this->drive->files->listFiles([
                'q'      => "'{$folderId}' in parents and mimeType contains 'image/' and trashed = false",
                'fields' => 'files(id, name, mimeType, size, createdTime)',
                'orderBy' => 'name',
            ]);

            return array_map(function ($file) {
                return [
                    'id'           => $file->getId(),
                    'name'         => $file->getName(),
                    'thumbnail'    => $this->getThumbnailUrl($file->getId()),
                    'view_url'     => "https://drive.google.com/file/d/{$file->getId()}/view",
                ];
            }, $results->getFiles());
        });
    }

    /**
     * Ambil info folder (nama) berdasarkan Folder ID
     */
    public function getFolderInfo(string $folderId): ?array
    {
        try {
            $folder = $this->drive->files->get($folderId, [
                'fields' => 'id, name',
            ]);

            return [
                'id'   => $folder->getId(),
                'name' => $folder->getName(),
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Generate URL thumbnail Google Drive
     */
    public function getThumbnailUrl(string $fileId, int $size = 400): string
    {
        return "https://drive.google.com/thumbnail?id={$fileId}&sz=w{$size}";
    }

    /**
     * Generate URL embed PDF untuk preview di browser
     */
    public function getPdfPreviewUrl(string $fileId): string
    {
        return "https://drive.google.com/file/d/{$fileId}/preview";
    }

    /**
     * Hapus cache folder tertentu (untuk refresh manual)
     */
    public function clearFolderCache(string $folderId): void
    {
        Cache::forget('drive_folder_' . $folderId);
    }
}
