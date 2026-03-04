<?php

namespace Yoast\WP\SEO\Premium\Integrations\Third_Party;

use WPSEO_Schema_Context;
use Yoast\WP\SEO\Conditionals\Front_End_Conditional;
use Yoast\WP\SEO\Helpers\Schema\ID_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Premium\Conditionals\EDD_Conditional;
use Yoast\WP\SEO\Surfaces\Meta_Surface;

/**
 * EDD integration.
 */
class EDD implements Integration_Interface {

	/**
	 * The meta surface.
	 *
	 * @var Meta_Surface
	 */
	private $meta;

	/**
	 * The schema id helpers surface.
	 *
	 * @var ID_Helper
	 */
	private $schema_id;

	/**
	 * Returns the conditionals based on which this loadable should be active.
	 *
	 * @return list<class-string>
	 */
	public static function get_conditionals() {
		return [ Front_End_Conditional::class, EDD_Conditional::class ];
	}

	/**
	 * EDD constructor.
	 *
	 * @codeCoverageIgnore It only sets dependencies.
	 *
	 * @param Meta_Surface $meta      The meta surface.
	 * @param ID_Helper    $schema_id The schema id helpers surface.
	 */
	public function __construct( Meta_Surface $meta, ID_Helper $schema_id ) {
		$this->meta      = $meta;
		$this->schema_id = $schema_id;
	}

	/**
	 * Initializes the integration.
	 *
	 * This is the place to register hooks and filters.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_filter( 'edd_generate_download_structured_data', [ $this, 'filter_download_schema' ] );
		\add_filter( 'wpseo_schema_organization', [ $this, 'filter_organization_schema' ] );
		\add_filter( 'wpseo_schema_webpage', [ $this, 'filter_webpage_schema' ], 10, 2 );
	}

	/**
	 * Make sure the Organization is classified as a Brand too.
	 *
	 * @param array<string, string|array> $data The organization schema.
	 *
	 * @return array<string, string|array>
	 */
	public function filter_organization_schema( $data ) {
		if ( ! \is_singular( 'download' ) ) {
			return $data;
		}

		// This will always become an array. Cast early to allow easier comparison.
		$data['@type'] = (array) $data['@type'];
		$missing_types = \array_diff( [ 'Organization', 'Brand' ], $data['@type'] );
		if ( ! empty( $missing_types ) ) {
			\array_push( $data['@type'], ...$missing_types );
		}

		return $data;
	}

	/**
	 * Make sure the WebPage schema contains reference to the product.
	 *
	 * @param array<string, string|array> $data    The schema Webpage data.
	 * @param WPSEO_Schema_Context        $context Context object.
	 *
	 * @return array<string, string|array>
	 */
	public function filter_webpage_schema( $data, $context ) {
		if ( \is_singular( [ 'download' ] ) ) {
			$data['about']      = [ '@id' => $context->canonical . '#/schema/edd-product/' . \get_the_ID() ];
			$data['mainEntity'] = [ '@id' => $context->canonical . '#/schema/edd-product/' . \get_the_ID() ];
		}

		return $data;
	}

	/**
	 * Filter the structured data output for a download to tie into Yoast SEO's output.
	 *
	 * @param array<string, string|array> $data Structured data for a download.
	 *
	 * @return array<string, string|array>
	 */
	public function filter_download_schema( $data ) {

		$data['@id']    = $this->meta->for_current_page()->canonical . '#/schema/edd-product/' . \get_the_ID();
		$data['sku']    = (string) $data['sku'];
		$data['brand']  = $this->return_organization_node();
		$data['offers'] = $this->clean_up_offer( $data['offers'] );

		if ( ! isset( $data['description'] ) ) {
			$data['description'] = $this->meta->for_current_page()->open_graph_description;
		}

		return $data;
	}

	/**
	 * Cleans up EDD generated Offers.
	 *
	 * @param array<array-key, string|array> $offer The schema array.
	 *
	 * @return array<array-key, string|array>
	 */
	private function clean_up_offer( $offer ) {
		// Checking for not isset @type makes sure there are multiple offers in the offer list. It is always an array.
		if ( ! isset( $offer['@type'] ) ) {
			foreach ( $offer as $key => $o ) {
				if ( \array_key_exists( 'priceValidUntil', $o ) && $o['priceValidUntil'] === null ) {
					unset( $offer[ $key ]['priceValidUntil'] );
				}
				$offer[ $key ]['seller'] = $this->return_organization_node();
			}
		}
		else {
			if ( \array_key_exists( 'priceValidUntil', $offer ) && $offer['priceValidUntil'] === null ) {
				unset( $offer['priceValidUntil'] );
			}
			$offer['seller'] = $this->return_organization_node();
		}

		return $offer;
	}

	/**
	 * Returns a Schema node for the current site's Organization.
	 *
	 * @return array{"@type": string[], "@id": string}
	 */
	private function return_organization_node() {
		$home_page_meta = $this->meta->for_home_page();

		$id = $home_page_meta->canonical . '#organization';
		if ( $home_page_meta->site_represents === 'person' ) {
			$current_page_meta = $this->meta->for_current_page();

			$id = $this->schema_id->get_user_schema_id(
				$current_page_meta->site_user_id,
				$current_page_meta,
			);
		}

		return [
			'@type' => [ 'Organization', 'Brand' ],
			'@id'   => $id,
		];
	}
}
