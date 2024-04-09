<?php

namespace Wongyip\Laravel\Renderable\Utils;

use Exception;
use HTMLPurifier;
use HTMLPurifier_Config;
use Illuminate\Support\Facades\Log;

class HTML
{
    /**
     * Sanitize input with HTML Purifier.
     *
     * @param string $input
     * @param HTMLPurifier_Config|null $config
     * @return string
     */
    static function purify(string $input, HTMLPurifier_Config $config = null): string
    {
        try {
            // Use default if config is not given.
            if (!$config) {
                $config = HTMLPurifier_Config::createDefault();
            }
            // Cache Dir.
            if (self::purifierCacheDirReady($cache)) {
                $config->set('Cache.SerializerPath', $cache);
            }
            // Work on it.
            $purifier = new HTMLPurifier($config);
            return $purifier->purify($input);
        }
        catch (Exception $e) {
            Log::error(sprintf('HTML.purify() Exception: %s', $e->getMessage()));
        }
        return 'Error sanitizing HTML content.';
    }

    /**
     * @param string|null $path
     * @return bool
     */
    static function purifierCacheDirReady(string &$path = null): bool
    {
        $path = $path ?: storage_path('cache/html-purifier');

        if (file_exists($path) && is_dir($path)) {
            return is_writable($path);
        }
        else {
            if (mkdir($path, 0770, true)) {
                return is_writable($path);
            }
        }
        return false;
    }
}