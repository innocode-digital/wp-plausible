<?php

namespace Innocode\Statistics\Traits;

trait BearerTokenTrait {

	/**
	 * @var string
	 */
	protected $token;

	/**
	 * @param string $token
	 *
	 * @return void
	 */
	public function set_token( string $token ): void {
		$this->token = $token;
	}

	/**
	 * @return string|null
	 */
	public function get_token(): ?string {
		return $this->token;
	}

	/**
	 * @return string
	 */
	public function auth(): string {
		$token = $this->get_token();

		if ( null === $token ) {
			return '';
		}

		return "Bearer $token";
	}
}
