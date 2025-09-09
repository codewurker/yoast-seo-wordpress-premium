<?php

namespace Yoast\WP\SEO\Premium\Conditionals;

use Yoast\WP\SEO\Conditionals\Feature_Flag_Conditional;

/**
 * Checks if the AI_SUMMARIZE_DISABLE constant is set.
 *
 * phpcs:disable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded
 */
class AI_Summarize_Disable_Conditional extends Feature_Flag_Conditional {

	/**
	 * Returns the name of the feature flag.
	 *
	 * @return string The name of the feature flag.
	 */
	public function get_feature_flag() {
		return 'AI_SUMMARIZE_DISABLE';
	}
}
