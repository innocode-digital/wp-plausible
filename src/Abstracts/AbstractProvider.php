<?php

namespace Innocode\Statistics\Abstracts;

use Innocode\Statistics\Plugin;
use Innocode\Statistics\Traits\SiteIdTrait;

abstract class AbstractProvider {

	use SiteIdTrait;

	/**
	 * @var AbstractAPI
	 */
	protected $api;

	/**
	 * @return AbstractAPI
	 */
	public function get_api(): AbstractAPI {
		return $this->api;
	}

	/**
	 * @param Plugin $plugin
	 *
	 * @return void
	 */
	public function activate( Plugin $plugin ): void {

	}

	/**
	 * @param Plugin $plugin
	 *
	 * @return void
	 */
	public function deactivate( Plugin $plugin ): void {

	}

	/**
	 * @param Plugin $plugin
	 *
	 * @return void
	 */
	public function run( Plugin $plugin ): void {

	}

	/**
	 * @param string $name
	 * @param string $url
	 * @param array  $props
	 *
	 * @return void
	 */
	abstract public function push_event( string $name, string $url, array $props ): void;

	/**
	 * @param array $query
	 *
	 * @return array
	 */
	abstract public function popular_urls( array $query = [] ): array;

	/**
	 * @param array $query
	 *
	 * @return array
	 */
	abstract public function not_found_pages( array $query = [] ): array;

	/**
	 * @param string $type
	 * @param array  $query
	 * @return array
	 */
	abstract public function popular_comments( string $type = 'comment', array $query = [] ): array;

	/**
	 * @param string $post_type
	 * @param array  $query
	 * @return array
	 */
	abstract public function popular_posts( string $post_type = 'post', array $query = [] ): array;

	/**
	 * @param array $query
	 * @return array
	 */
	abstract public function popular_categories( array $query = [] ): array;

	/**
	 * @param array $query
	 * @return array
	 */
	abstract public function popular_tags( array $query = [] ): array;

	/**
	 * @param string $taxonomy
	 * @param array  $query
	 * @return array
	 */
	abstract public function popular_terms( string $taxonomy, array $query = [] ): array;

	/**
	 * @param array $query
	 *
	 * @return array
	 */
	abstract public function popular_authors( array $query = [] ): array;
}
