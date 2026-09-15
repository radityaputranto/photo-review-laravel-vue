<?php

namespace App\Services;

use Google\Client;
use Google\Service\Drive;
use Illuminate\Support\Facades\Cache;

class GoogleDriveService
{
    protected ?Client $client = null;
    protected ?Drive $drive = null;

    public function __construct()
    {
        // Credentials are loaded lazily so pages can render even if Google Drive API is not yet configured
    }

    /**
     * Cek apakah file kredensial Google Drive tersedia
     */
    public function isConfigured(): bool
    {
        $path = $this->getCredentialsPath();
        return file_exists($path) && is_readable($path);
    }

    /**
     * Dapatkan path file kredensial Google
     */
    protected function getCredentialsPath(): string
    {
        $customPath = env('GOOGLE_APPLICATION_CREDENTIALS');
        if ($customPath && file_exists($customPath)) {
            return $customPath;
        }

        return storage_path('app/google-credentials.json');
    }

    /**
     * Dapatkan instance Google Drive Service (Lazy loaded)
     */
    protected function getDrive(): ?Drive
    {
        if ($this->drive !== null) {
            return $this->drive;
        }

        if (!$this->isConfigured()) {
            return null;
        }

        try {
            $this->client = new Client();
            $this->client->setAuthConfig($this->getCredentialsPath());
            $this->client->addScope(Drive::DRIVE_READONLY);
            $this->drive = new Drive($this->client);
            return $this->drive;
        } catch (\Throwable $e) {
            \Log::warning('Failed to initialize Google Drive client: ' . $e->getMessage());
            return null;
        }
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
        $drive = $this->getDrive();
        if (!$drive) {
            return [];
        }

        $cacheKey = 'drive_folder_' . $folderId;
        $minutes  = config('app.google_drive_cache_minutes', 10);

        return Cache::remember($cacheKey, now()->addMinutes($minutes), function () use ($drive, $folderId) {
            try {
                $results = $drive->files->listFiles([
                    'q'      => "'{$folderId}' in parents and mimeType contains 'image/' and trashed = false",
                    'fields' => 'files(id, name, mimeType, size, createdTime, thumbnailLink)',
                    'orderBy' => 'name',
                ]);

                return array_map(function ($file) {
                    $thumbUrl = $file->getThumbnailLink();
                    // Replace =s220 with =s400 for a sharper thumbnail
                    $thumbnail = $thumbUrl ? preg_replace('/=s\d+$/', '=s600', $thumbUrl) : $this->getThumbnailUrl($file->getId());
                    // Use =s0 for original resolution full image preview
                    $viewUrl = $thumbUrl ? preg_replace('/=s\d+$/', '=s0', $thumbUrl) : "https://drive.google.com/file/d/{$file->getId()}/view";

                    return [
                        'id'           => $file->getId(),
                        'name'         => $file->getName(),
                        'thumbnail'    => $thumbnail,
                        'view_url'     => $viewUrl,
                    ];
                }, $results->getFiles());
            } catch (\Throwable $e) {
                \Log::error('Error fetching photos from Google Drive: ' . $e->getMessage());
                return [];
            }
        });
    }

    /**
     * Ambil info folder (nama) berdasarkan Folder ID
     */
    public function getFolderInfo(string $folderId): ?array
    {
        $drive = $this->getDrive();
        if (!$drive) {
            return null;
        }

        try {
            $folder = $drive->files->get($folderId, [
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
