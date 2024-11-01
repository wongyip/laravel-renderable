<?php

namespace Wongyip\Laravel\Renderable\Utils;

use Exception;
use HTMLPurifier;
use HTMLPurifier_Config;
use Illuminate\Support\Facades\Log;

class HTML
{
    // Same as HTMLPurifier_Config::createDefault().
    const PURIFY_MODE_STRICT = 12;

    // With CSS.trusted and CSS.AllowTricky directives enabled.
    const PURIFY_MODE_TRUSTED = 16;

    /**
     * Create and return HTMLPurifier_Config based on the input mode. If input
     * mode is not recognized, return the strict mode config, which is the
     * default output of HTMLPurifier_Config::createDefault().
     *
     * @param int $mode
     * @return HTMLPurifier_Config
     * @use HTML::PURIFY_MODE_TRUSTED | HTML::PURIFY_MODE_STRICT
     */
    public static function purifierConfig(int $mode): HTMLPurifier_Config
    {
        $config = HTMLPurifier_Config::createDefault();
        if ($mode === static::PURIFY_MODE_TRUSTED) {
            /**
             * @todo To be elaborated.
             */
            $config->set('CSS.Trusted', true);
            $config->set('CSS.AllowTricky', true);
        }
        return $config;
    }

    /**
     * Sanitize input with HTML Purifier, use a strict config by default.
     *
     * @param string $input
     * @param HTMLPurifier_Config|int|null $configOrMode Skip for a strict config.
     * @return string
     * @use HTML::PURIFY_MODE_TRUSTED | HTML::PURIFY_MODE_STRICT
     */
    static function purify(string $input, HTMLPurifier_Config|int $configOrMode = null): string
    {
        try {
            $config = $configOrMode instanceof HTMLPurifier_Config
                ? $configOrMode
                : static::purifierConfig($configOrMode ?? static::PURIFY_MODE_STRICT) ;
            /**
             * Cache Dir: DO NOT get the config directive for verification since
             * a get() call will also trigger autoFinalize() and make the config
             * read only.
             */
            if (self::purifierCacheDirReady($path)) {
                $config->set('Cache.SerializerPath', $path);
            }
            else {
                Log::notice('HTMLPurifier cache directory is not ready, running without caching.');
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
     * Get the cache directory ready.
     *
     * @param string|null $path
     * @return bool
     * @see config/renderable.php
     */
    static function purifierCacheDirReady(string &$path = null): bool
    {
        $path = $path ?? config('renderable.htmlPurifier.cacheDir');
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