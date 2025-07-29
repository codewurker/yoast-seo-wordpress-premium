<?php

namespace Yoast\WP\SEO\Premium\AI_Suggestions_Postprocessor\Application;

use DOMDocument;

/**
 * Class used to serialize the output dom to a string.
 *
 * @deprecated 25.6
 * @codeCoverageIgnore
 */
class AI_Suggestions_Serializer {

	/**
	 * Serializes the output DOM to a string.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @param DOMDocument $dom The output dom.
	 *
	 * @return string The serialized output dom.
	 */
	public function serialize( DOMDocument $dom ) {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\Premium\AI\Optimize\Suggestions_Postprocessor\Application\Suggestions_Serializer::serialize' );
	}
}
