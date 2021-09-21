<?php

namespace Fjarfs\SrcService\Helpers;

class Locale
{
    /**
     * Get app locale
     *
     * @return mixed
     */
    public static function getLocale()
    {
        return static::localization()->getLocale();
    }

    /**
     * App localization
     *
     * @return mixed
     */
    private static function localization()
    {
        if (app() instanceof \Illuminate\Foundation\Application) {
            // if current framework is Laravel
            return app();
        } else {
            // if current framework is Lumen
            return app('translator');
        }
    }
}
