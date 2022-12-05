<?php

namespace Innocode\Statistics\Abstracts;

abstract class AbstractEntity {

	/**
	 * @var array
	 */
	protected $props = [];

	/**
	 * @param string $name
	 * @param mixed  $value
	 *
	 * @return void
	 */
	public function set_prop( string $name, $value ): void {
		$this->props[ $name ] = $value;
	}

	/**
	 * @return array
	 */
	public function get_props(): array {
		return $this->props;
	}

	/**
	 * @param array $data
	 */
	public function __construct( array $data = [] ) {
		foreach ( $data as $property => $value ) {
            if ( null === $value ) {
                continue;
            }

			if ( property_exists( $this, $property ) ) {
				$this->{"set_$property"}( $value );
			} else {
				$this->set_prop( $property, $value );
			}
		}
	}

	/**
	 * @return array
	 */
	public function to_array(): array {
		$data = [];

		foreach ( array_keys( get_object_vars( $this ) ) as $property ) {
            if ( ! isset( $this->$property ) ) {
                continue;
            }

			$value = $this->{"get_$property"}();

			if ( ! ( $property === 'props' && empty( $value ) ) ) {
				$data[ $property ] = $value;
			}
		}

		return $data;
	}
}
