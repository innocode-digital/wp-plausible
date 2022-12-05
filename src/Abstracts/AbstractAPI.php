<?php

namespace Innocode\Statistics\Abstracts;

abstract class AbstractAPI {

	/**
	 * @var string
	 */
	protected $root;

	/**
	 * @return AbstractEndpoint[]
	 */
	abstract public function get_endpoints(): array;

	/**
	 * @param string $root
	 *
	 * @return void
	 */
	public function set_root( string $root ): void {
		$this->root = $root;

		foreach ( $this->get_endpoints() as $endpoint ) {
			$endpoint->set_api_root( $root );
		}
	}

	/**
	 * @return string
	 */
	public function get_root(): string {
		return $this->root;
	}
}
