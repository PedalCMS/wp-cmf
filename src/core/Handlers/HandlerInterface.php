<?php
/**
 * Handler Interface
 *
 * Defines the contract for all registration handlers.
 *
 * @package Pedalcms\WpCmf
 * @since 1.0.0
 */

namespace Pedalcms\WpCmf\Core\Handlers;

use Pedalcms\WpCmf\Field\FieldInterface;

/**
 * Interface HandlerInterface
 *
 * All handlers must implement this interface.
 */
interface HandlerInterface {

	/**
	 * Initialize WordPress hooks
	 *
	 * @return void
	 */
	public function init_hooks(): void;

	/**
	 * Add fields for a context
	 *
	 * @param string $context Context identifier.
	 * @param array  $fields  Field configurations.
	 * @return void
	 */
	public function add_fields( string $context, array $fields ): void;

	/**
	 * Get fields for a context
	 *
	 * @param string $context Context identifier.
	 * @return array<string, FieldInterface>
	 */
	public function get_fields( string $context ): array;

	/**
	 * Check if context has fields
	 *
	 * @param string $context Context identifier.
	 * @return bool
	 */
	public function has_fields( string $context ): bool;
}
