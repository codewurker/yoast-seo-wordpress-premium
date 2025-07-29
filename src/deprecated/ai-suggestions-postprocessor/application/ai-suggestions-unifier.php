<?php

namespace Yoast\WP\SEO\Premium\AI_Suggestions_Postprocessor\Application;

use Yoast\WP\SEO\Premium\AI_Suggestions_Postprocessor\Domain\Suggestion_Interface;
use Yoast\WP\SEO\Premium\DOM_Manager\Application\DOM_Parser;
use Yoast\WP\SEO\Premium\DOM_Manager\Application\Node_Processor;

/**
 * Class that implements the main flow of the AI suggestions unifier.
 *
 * @deprecated 25.6
 * @codeCoverageIgnore
 */
class AI_Suggestions_Unifier {

	public const PUNCTUATION_SPLIT_REGEX = '/(?<=[.!?])/i';

	/**
	 * The suggestion parser.
	 *
	 * @var DOM_Parser
	 */
	protected $parser;

	/**
	 * The suggestion processor.
	 *
	 * @var Node_Processor
	 */
	protected $node_processor;

	/**
	 * The sentence processor.
	 *
	 * @var Sentence_Processor
	 */
	protected $sentence_processor;

	/**
	 * The suggestion serializer.
	 *
	 * @var Suggestion_Processor
	 */
	private $suggestion_processor;

	/**
	 * The suggestion serializer.
	 *
	 * @var AI_Suggestions_Serializer
	 */
	protected $serializer;

	/**
	 * The class constructor.
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @param DOM_Parser                $parser               The DOM parser.
	 * @param Node_Processor            $node_processor       The node processor.
	 * @param Sentence_Processor        $sentence_processor   The sentence processor.
	 * @param Suggestion_Processor      $suggestion_processor The suggestion processor.
	 * @param AI_Suggestions_Serializer $serializer           The suggestion serializer.
	 */
	public function __construct( DOM_Parser $parser, Node_Processor $node_processor, Sentence_Processor $sentence_processor, Suggestion_Processor $suggestion_processor, AI_Suggestions_Serializer $serializer ) {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\Premium\AI\Optimize\Suggestions_Postprocessor\Application\Suggestions_Unifier::__construct' );
		$this->parser               = $parser;
		$this->node_processor       = $node_processor;
		$this->suggestion_processor = $suggestion_processor;
		$this->sentence_processor   = $sentence_processor;
		$this->serializer           = $serializer;
	}

	/**
	 * Process the suggestion
	 *
	 * @deprecated 25.6
	 * @codeCoverageIgnore
	 *
	 * @param Suggestion_Interface $suggestion The suggestion to process.
	 *
	 * @return string The processed suggestion
	 */
	public function unify_diffs( Suggestion_Interface $suggestion ) {
		\_deprecated_function( __METHOD__, 'Yoast SEO 25.6', 'Yoast\WP\SEO\Premium\AI\Optimize\Suggestions_Postprocessor\Application\Suggestions_Unifier::unify_diffs' );
	}
}
