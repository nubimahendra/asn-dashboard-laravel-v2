<?php

namespace App\Services;

class CsvSanitizerService
{
    /**
     * Sanitize a CSV file by forcing UTF-8 encoding and normalizing line endings.
     * This modifies the file in place.
     * 
     * @param string $path Absolute path to the file
     * @return void
     */
    public function sanitize(string $path): void
    {
        if (!file_exists($path)) {
            throw new \Exception("File not found for sanitization: {$path}");
        }

        $content = file_get_contents($path);

        // Force UTF-8 if it's not already
        $encoding = mb_detect_encoding($content, mb_detect_order(), true);
        if ($encoding !== 'UTF-8') {
             // 'auto' will try common encodings, but we can specify an order if needed
            $content = mb_convert_encoding($content, 'UTF-8', 'auto');
        }

        // Normalize line endings to \n
        $content = str_replace(["\r\n", "\r"], "\n", $content);

        file_put_contents($path, $content);
    }
}
