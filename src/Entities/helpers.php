<?php

/**
 * Returns the current page.
 *
 * @return string
 */
function currentPage() : string
{
    return str_after(Request::path(), '/');
}

/**
 * Returns the current page, independent of locale.
 *
 * @return string
 */
function currentPageWithoutLocale() : string
{
    return str_after(
        str_start(str_replace(Request::root(), '', LaravelLocalization::getNonLocalizedURL()), '/'),
        '/'
    );
}

/**
 * Verifies if the given url pages are the current page.
 *
 * @param array $urls
 * @param bool $startsWith
 * @return bool
 */
function isCurrentPageInArray(array $urls, bool $startsWith = false) : bool
{
    $page = currentPage();

    if ($startsWith) {
        foreach ($urls as $url) {
            if ($url === $page) {
                return true;
            }
        }
    }

    foreach ($urls as $url) {
        if (starts_with($page, $url)) {
            return true;
        }
    }

    return false;
}

/**
 * Verifies if the given page is the current page the user is seeing.
 *
 * @param string|array $url
 * @return boolean
 */
function isCurrentPage($url, bool $startsWith = false) : bool
{
    if (!$startsWith) {
        return is_array($url) ? isCurrentPageInArray($url) : $url === currentPage();
    }

    return is_array($url) ? isCurrentPageInArray($url, true) :  starts_with(currentPage(), $url);
}

/**
 * Switches the current locale.
 *
 * @param  string $locale
 * @return string
 */
function switchLocale(string $locale) : string
{
    return '/' . $locale . '/' . currentPageWithoutLocale();
}

/**
 * Provides all available locales.
 *
 * @return array
 */
function locales()
{
    return LaravelLocalization::getSupportedLocales();
}

/**
 * Returns the current locale if the locale parameter is null. Otherwise,
 * it sets the application locale.
 *
 * @param string $locale
 * @return void
 */
function locale(?string $locale = null)
{
    return LaravelLocalization::setLocale($locale);
}



/**
 *
 */
function urlRoute(?string $route = null, $parameters = [])
{
    $url = route($route, $parameters);
    return str_replace(url('/').'/', "", $url);
}

/**
 *
 */
function routeProcess($route)
{
    if(is_array($route)){
        return urlRoute($route[0], isset($route[1]) ? $route[1] : null);
    }

    return urlRoute($route);
}

/**
 * It checks if the cache exists, if it does not exist it creates the cache,
 * if it exists it rewrites the data.
 *
 * @param string $key
 * @param int $minutes
 * @param $callback
 * @param function $anonFunc
 *
 * @return Illuminate\Support\Facades\Cache
 */
function cacheKey(string $key, int $minutes = 10080, $callback = null, $anonFunc = null)
{
    if (is_callable($anonFunc)) {
        $callback = $anonFunc();
    }

    if (Cache::has($key)) {
        return Cache::get($key);
    }else{
        return Cache::remember($key, $minutes, function() use ($callback) {
            return $callback;
        });
    }

    return null;
}

/**
 * Remove cache by key
 */
function cacheKeyRemove(string $key)
{
    return Cache::forget($key);
}

