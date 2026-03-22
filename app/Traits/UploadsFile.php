<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait UploadsFile
{
    /**
     * Store an uploaded file and return path plus whatever the client sent (MIME, name, size, extension).
     *
     * @return array{
     *     path: string,
     *     mime_type: string|null,
     *     extension: string,
     *     original_name: string,
     *     size: int
     * }
     */
    protected function storeUploadedFile(
        UploadedFile $file,
        string $directory = 'uploads',
        string $disk = 'public'
    ): array {
        $directory = trim($directory, '/');
        $extension = strtolower($file->guessExtension() ?: $file->getClientOriginalExtension() ?: 'bin');
        $filename = Str::uuid()->toString() . '.' . $extension;
        $path = $file->storeAs($directory, $filename, $disk);

        if ($path === false) {
            throw new \RuntimeException('Unable to store uploaded file.');
        }

        return array_merge(
            ['path' => $path],
            $this->uploadedFileMetadata($file)
        );
    }

    /**
     * Store a new file, then delete the previous path (if any and not the same path).
     *
     * @return array{
     *     path: string,
     *     mime_type: string|null,
     *     extension: string,
     *     original_name: string,
     *     size: int
     * }
     */
    protected function replaceUploadedFile(
        UploadedFile $file,
        ?string $previousPath,
        string $directory = 'uploads',
        string $disk = 'public'
    ): array {
        $stored = $this->storeUploadedFile($file, $directory, $disk);

        if ($previousPath !== null && $previousPath !== '' && $previousPath !== $stored['path']) {
            $this->deleteStoredFile($previousPath, $disk);
        }

        return $stored;
    }

    /**
     * Metadata for any upload (no fixed image/video/audio buckets).
     *
     * @return array{mime_type: string|null, extension: string, original_name: string, size: int}
     */
    protected function uploadedFileMetadata(UploadedFile $file): array
    {
        $extension = strtolower($file->guessExtension() ?: $file->getClientOriginalExtension() ?: '');

        return [
            'mime_type' => $file->getMimeType() ?: null,
            'extension' => $extension !== '' ? $extension : 'bin',
            'original_name' => $file->getClientOriginalName() ?: $file->hashName(),
            'size' => (int) $file->getSize(),
        ];
    }

    protected function deleteStoredFile(?string $path, string $disk = 'public'): void
    {
        if ($path === null || $path === '') {
            return;
        }

        if (Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }
    }
}
