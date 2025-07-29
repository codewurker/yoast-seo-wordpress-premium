<?php

namespace Yoast\WP\SEO\Premium\AI_Suggestions_Postprocessor\Application;

use DOMDocument;
use DOMNode;
use DOMNodeList;
use Yoast\WP\SEO\Premium\DOM_Manager\Application\DOM_Parser;
use Yoast\WP\SEO\Premium\DOM_Manager\Application\Node_Processor;

/**
 * Class implementing the processing elements used on the AI suggestions.
 *
 * @deprecated 25.6
 * @codeCoverageIgnore
 */
class Suggestion_Processor {
	// Class name for the diff elements.
	public const YST_DIFF_CLASS = 'yst-diff';

	/**
	 * The DOM Parser.
	 *
	 * @var DOM_Parser
	 */
	private $parser;

	/**
	 * The suggestion processor.
	 *
	 * @var Node_Processor
	 */
	protected $node_processor;

	/**
	 * The suggestion serializer.
	 *
	 * @var AI_Suggestions_Serializer
	 */
	protected $serializer;

	/**
	 * Constructor
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @param DOM_Parser                $parser         The DOM parser.
	 * @param Node_Processor            $node_processor The node processor.
	 * @param AI_Suggestions_Serializer $serializer     The suggestion serializer.
	 */
	public function __construct(
		DOM_Parser $parser,
		Node_Processor $node_processor,
		AI_Suggestions_Serializer $serializer
	) {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\Premium\AI\Optimize\Optimizer\Application\Suggestion_Processor::__construct' );
		$this->parser         = $parser;
		$this->node_processor = $node_processor;
		$this->serializer     = $serializer;
	}

	/**
	 * Parses the AI response and returns the suggestion
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @param string $ai_response The AI response to parse.
	 *
	 * @return string The suggestion from the AI response.
	 */
	public function get_suggestion_from_ai_response( string $ai_response ) {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\Premium\AI\Optimize\Optimizer\Application\Suggestion_Processor::get_suggestion_from_ai_response' );
	}

	/**
	 * Calculates the diff between the original text and the fixed text.
	 * Differences are marked with `<ins>` and `<del>` tags.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @param string $original  The original text.
	 * @param string $raw_fixes The suggested fixes.
	 *
	 * @return string The difference between the two strings.
	 */
	public function calculate_diff( string $original, string $raw_fixes ) {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\Premium\AI\Optimize\Optimizer\Application\Suggestion_Processor::calculate_diff' );
	}

	/**
	 * Removes the HTML tags from the suggestion.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @param string $diff The suggestion to remove the HTML tags from.
	 *
	 * @return string The suggestion without the HTML tags.
	 */
	public function remove_html_from_suggestion( string $diff ) {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\Premium\AI\Optimize\Optimizer\Application\Suggestion_Processor::remove_html_from_suggestion' );
	}

	/**
	 * In the paragraph length assessment, we introduce new paragraphs. We mark these with a special class.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @param string $diff The suggestion that potentially includes newly introduced paragraphs.
	 *
	 * @return string The suggestion with the newly introduced paragraphs marked by the class `yst-paragraph`.
	 */
	public function mark_new_paragraphs_in_suggestions( string $diff ) {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\Premium\AI\Optimize\Optimizer\Application\Suggestion_Processor::mark_new_paragraphs_in_suggestions' );
	}

	/**
	 * Retains any replacements of non-breaking spaces in suggestions.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @param string $diff The diff to keep its non-breaking spaces.
	 *
	 * @return string The suggestion with non-breaking spaces intact.
	 */
	public function keep_nbsp_in_suggestions( string $diff ) {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\Premium\AI\Optimize\Optimizer\Application\Suggestion_Processor::keep_nbsp_in_suggestions' );
	}

	/**
	 * Get the Yoast diff nodes from the DOM
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @param DOMDocument $dom       The DOM to get the diff nodes from.
	 * @param string|null $node_type The type of node to get. If null the method will get both ins and del nodes.
	 *
	 * @return DOMNodeList The diff nodes
	 */
	public function get_diff_nodes( DOMDocument $dom, ?string $node_type = null ) {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\Premium\AI\Optimize\Optimizer\Application\Suggestion_Processor::get_diff_nodes' );
	}

	/**
	 * Check if a node is a Yoast diff node
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @param DOMNode $node The node to check.
	 *
	 * @return bool Whether the node is a Yoast diff node.
	 */
	public function is_yoast_diff_node( DOMNode $node ) {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\Premium\AI\Optimize\Optimizer\Application\Suggestion_Processor::is_yoast_diff_node' );
	}

	/**
	 * Convert diff nodes to string
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @param DOMDocument $dom The DOM to convert.
	 *
	 * @return DOMDocument The converted DOM
	 */
	public function convert_diff_nodes_to_string_nodes( DOMDocument $dom ) {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\Premium\AI\Optimize\Optimizer\Application\Suggestion_Processor::convert_diff_nodes_to_string_nodes' );
	}

	/**
	 * Replace placeholders with diff tags
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @param string $suggestion The suggestion to process.
	 *
	 * @return string The suggestion with the tags replaced
	 */
	public function replace_placeholders_with_diff_tags( string $suggestion ) {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\Premium\AI\Optimize\Optimizer\Application\Suggestion_Processor::replace_placeholders_with_diff_tags' );
	}

	/**
	 * Additional unification step to join contiguous diff nodes with the same tag.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @param DOMDocument $dom The DOM to unify.
	 *
	 * @return void
	 */
	public function unify_suggestion( DOMDocument $dom ) {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\Premium\AI\Optimize\Optimizer\Application\Suggestion_Processor::unify_suggestion' );
	}

	/**
	 * Takes care of the cases when an ins node starts with a full stop.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @param DOMDocument $dom The DOM to fix the leading full stop in.
	 *
	 * @return void
	 */
	public function fix_leading_full_stop( DOMDocument $dom ) {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\Premium\AI\Optimize\Optimizer\Application\Suggestion_Processor::fix_leading_full_stop' );
	}
}
