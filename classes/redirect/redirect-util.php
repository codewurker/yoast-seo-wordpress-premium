<?php
/**
 * WPSEO Premium plugin file.
 *
 * @package WPSEO\Premium\Classes
 */

use Yoast\WP\SEO\Helpers\Home_Url_Helper;

/**
 * Helpers for redirects.
 */
class WPSEO_Redirect_Util {

	/**
	 * Whether or not the permalink contains a trailing slash.
	 *
	 * @var bool
	 */
	public static $has_permalink_trailing_slash = null;

	/**
	 * The home URL helper.
	 *
	 * @var Home_Url_Helper
	 */
	private static $home_url;

	/**
	 * Returns whether or not a URL is a relative URL.
	 *
	 * @param string $url The URL to determine the relativity for.
	 * @return bool
	 */
	public static function is_relative_url( $url ) {
		$url_scheme = wp_parse_url( $url, PHP_URL_SCHEME );

		return ! $url_scheme;
	}

	/**
	 * Returns whether a URL is an internal URL (i.e. starts with home_url).
	 *
	 * @param string $url The URL to determine whether it's internal.
	 *
	 * @return bool
	 */
	public static function is_internal_url( $url ) {
		// Relative URLs are always internal.
		if ( self::is_relative_url( $url ) ) {
			return true;
		}

		// This part mimics the behavior in the WPSEO_Redirect class.
		if ( static::$home_url === null ) {
			static::$home_url = new Home_Url_Helper();
		}
		$home_url_pieces = static::$home_url->get_parsed();
		$url_pieces      = ( wp_parse_url( $url ) ?? [] );

		// We skip the scheme check for existence, if scheme is missing it's considered relative and thus internal.
		if ( ! isset( $url_pieces['host'] ) || ! static::match_home_url_host( $home_url_pieces['host'], $url_pieces['host'] ) ) {
			return false;
		}

		if ( ! isset( $home_url_pieces['path'] ) ) {
			return true;
		}

		return isset( $url_pieces['path'] ) && static::match_home_url_path( $home_url_pieces['path'], $url_pieces['path'] );
	}

	/**
	 * Returns whether or not the permalink structure has a trailing slash.
	 *
	 * @return bool
	 */
	public static function has_permalink_trailing_slash() {
		if ( self::$has_permalink_trailing_slash === null ) {
			$permalink_structure = get_option( 'permalink_structure' );

			self::$has_permalink_trailing_slash = substr( $permalink_structure, -1 ) === '/';
		}

		return self::$has_permalink_trailing_slash;
	}

	/**
	 * Returns whether or not the URL has query variables.
	 *
	 * @param string $url The URL.
	 * @return bool
	 */
	public static function has_query_parameters( $url ) {
		return strpos( $url, '?' ) !== false;
	}

	/**
	 * Returns whether or not the given URL has a fragment identifier.
	 *
	 * @param string $url The URL to parse.
	 *
	 * @return bool
	 */
	public static function has_fragment_identifier( $url ) {
		// Deal with this case if the last character is a hash.
		if ( substr( $url, -1 ) === '#' ) {
			return true;
		}

		$fragment = wp_parse_url( $url, PHP_URL_FRAGMENT );

		return ! empty( $fragment );
	}

	/**
	 * Returns whether or not the given URL has an extension.
	 *
	 * @param string $url The URL to parse.
	 *
	 * @return bool Whether or not the given URL has an extension.
	 */
	public static function has_extension( $url ) {
		$parsed = wp_parse_url( $url, PHP_URL_PATH );

		return ( is_string( $parsed ) && strpos( $parsed, '.' ) !== false );
	}

	/**
	 * Returns whether or not a target URL requires a trailing slash.
	 *
	 * @param string $target_url The target URL to check.
	 *
	 * @return bool
	 */
	public static function requires_trailing_slash( $target_url ) {
		return $target_url !== '/'
			&& self::has_permalink_trailing_slash()
			&& self::is_relative_url( $target_url )
			&& ! self::has_query_parameters( $target_url )
			&& ! self::has_fragment_identifier( $target_url )
			&& ! self::has_extension( $target_url );
	}

	/**
	 * Removes the base url path from the given URL.
	 *
	 * @param string $base_url The base URL that will be stripped.
	 * @param string $url      URL to remove the path from.
	 *
	 * @return string The URL without the base url
	 */
	public static function strip_base_url_path_from_url( $base_url, $url ) {
		$base_url_path = wp_parse_url( $base_url, PHP_URL_PATH );
		if ( ! empty( $base_url_path ) ) {
			$base_url_path = ltrim( $base_url_path, '/' );
		}

		if ( empty( $base_url_path ) ) {
			return $url;
		}

		$url = ltrim( $url, '/' );

		// When the url doesn't begin with the base url path.
		if ( stripos( trailingslashit( $url ), trailingslashit( $base_url_path ) ) !== 0 ) {
			return $url;
		}

		return substr( $url, strlen( $base_url_path ) );
	}

	/**
	 * Checks if the URL matches the home URL by comparing their host.
	 *
	 * @param string $home_url_host The home URL host.
	 * @param string $url_host      The URL host.
	 *
	 * @return bool True when both hosts are equal.
	 */
	private static function match_home_url_host( $home_url_host, $url_host ) {
		return $url_host === $home_url_host;
	}

	/**
	 * Checks if the URL matches the home URL by comparing their path.
	 *
	 * @param string $home_url_path The home URL path.
	 * @param string $url_path      The URL path.
	 *
	 * @return bool True when the home URL path is empty or when the URL path begins with the home URL path.
	 */
	private static function match_home_url_path( $home_url_path, $url_path ) {
		$home_url_path = trim( $home_url_path, '/' );
		if ( empty( $home_url_path ) ) {
			return true;
		}

		return strpos( trim( $url_path, '/' ), $home_url_path ) === 0;
	}
}
