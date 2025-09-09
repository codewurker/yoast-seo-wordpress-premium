<?php

namespace Yoast\WP\SEO\Premium\Conditionals;

use Yoast\WP\SEO\Conditionals\Conditional;

/**
 * Conditional that is only met when AI Summarize is NOT disabled.
 *
 * phpcs:disable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded
 */
class AI_Summarize_Support_Conditional implements Conditional {

	/**
	 * The AI_Summarize_Disable_Conditional instance.
	 *
	 * @var AI_Summarize_Disable_Conditional
	 */
	private $disable_conditional;

	/**
	 * Constructor.
	 *
	 * @param AI_Summarize_Disable_Conditional $disable_conditional The disable conditional.
	 */
	public function __construct( AI_Summarize_Disable_Conditional $disable_conditional ) {
		$this->disable_conditional = $disable_conditional;
	}

	/**
	 * Returns true when AI Summarize is NOT disabled.
	 *
	 * @return bool Returns true when AI Summarize is NOT disabled.
	 */
	public function is_met() {
		return ! $this->disable_conditional->is_met();
	}
}
