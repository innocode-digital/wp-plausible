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
	 * @return string|null
	 */
	public function auth(): ?string {
		$token = $this->get_token();

		return $token !== null ? "Bearer $token" : $token;
	}
}
