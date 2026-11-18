<?php

// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Premium\Abilities\Infrastructure;

/**
 * Describes the bots that can be blocked through the robots.txt file, keyed by the bot.
 *
 * The map is the single source of truth for both the ability schemas and the bot blocking manager.
 */
class Blockable_Bot_Map {

	/**
	 * Returns the bots, each with its robots.txt user agent, the option that blocks it and its description, starting with its name as shown in the settings.
	 *
	 * @return array<string, array<string, string>> The user agent, option name and description of each bot, keyed by the bot.
	 */
	public function get_bots(): array {
		return [
			'google_extended' => [
				'user_agent'  => 'Google-Extended',
				'option_name' => 'deny_google_extended_crawling',
				'description' => \__( 'Google Gemini and Vertex AI bots (the Google-Extended bot). Blocking it won’t prevent Google from indexing the website.', 'wordpress-seo-premium' ),
			],
			'gptbot'          => [
				'user_agent'  => 'GPTBot',
				'option_name' => 'deny_gptbot_crawling',
				'description' => \__( 'OpenAI GPTBot. Other OpenAI bots, such as OAI-SearchBot and ChatGPT-User, aren’t affected.', 'wordpress-seo-premium' ),
			],
			'ccbot'           => [
				'user_agent'  => 'CCBot',
				'option_name' => 'deny_ccbot_crawling',
				'description' => \__( 'Common Crawl CCBot.', 'wordpress-seo-premium' ),
			],
		];
	}
}
