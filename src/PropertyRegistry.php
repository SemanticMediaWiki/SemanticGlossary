<?php

namespace SG;

use SMW\DIProperty;

/**
 * @ingroup SG
 *
 * @licence GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class PropertyRegistry {

	const SG_TERM = '___glt';
	const SG_DEFINITION = '___gld';
	const SG_LINK  = '___gll';
	const SG_STYLE = '___gls';

	protected static $instance = null;

	/**
	 * @since 1.0
	 *
	 * @return PropertyRegistry
	 */
	public static function getInstance() {

		if ( self::$instance === null ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * @since 1.0
	 */
	public static function clear() {
		self::$instance = null;
	}

	/**
	 * @since 1.0
	 *
	 * @return boolean
	 */
	public function registerPropertiesAndAliases() {

		$propertyDefinitions = array(
			self::SG_TERM => array(
				'label' => SG_PROP_GLT,
				'type'  => '_txt',
				'alias' => wfMessage( 'semanticglossary-prop-glt' )->text()
			),
			self::SG_DEFINITION => array(
				'label' => SG_PROP_GLD,
				'type'  => '_txt',
				'alias' => wfMessage( 'semanticglossary-prop-gld' )->text()
			),
			self::SG_LINK => array(
				'label' => SG_PROP_GLL,
				'type'  => '_txt',
				'alias' => wfMessage( 'semanticglossary-prop-gll' )->text()
			),
			self::SG_STYLE => array(
				'label' => SG_PROP_GLS,
				'type'  => '_txt',
				'alias' => wfMessage( 'semanticglossary-prop-gls' )->text()
			)
		);

		return $this->registerPropertiesFromList( $propertyDefinitions );
	}

	protected function registerPropertiesFromList( array $propertyList ) {

		foreach ( $propertyList as $propertyId => $definition ) {

			DIProperty::registerProperty(
				$propertyId,
				$definition['type'],
				$definition['label'],
				true
			);

			DIProperty::registerPropertyAlias(
				$propertyId,
				$definition['alias']
			);
		}

		return true;
	}

}
