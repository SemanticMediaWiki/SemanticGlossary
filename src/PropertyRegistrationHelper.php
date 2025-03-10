<?php

namespace SG;

use SMW\PropertyRegistry;

define( 'SG_PROP_GLT', 'Glossary-Term' );
define( 'SG_PROP_GLD', 'Glossary-Definition' );
define( 'SG_PROP_GLL', 'Glossary-Link' );
define( 'SG_PROP_GLS', 'Glossary-Style' );

/**
 * @ingroup SemanticGlossary
 *
 * @license GPL-2.0-or-later
 * @since 1.0
 *
 * @author mwjames
 */
class PropertyRegistrationHelper {

	public const SG_TERM = '___glt';
	public const SG_DEFINITION = '___gld';
	public const SG_LINK  = '___gll';
	public const SG_STYLE = '___gls';

	/**
	 * @var PropertyRegistry
	 */
	private $propertyRegistry;

	/**
	 * PropertyRegistry constructor.
	 *
	 * @param PropertyRegistry $propertyRegistry
	 */
	public function __construct( PropertyRegistry $propertyRegistry ) {
		$this->propertyRegistry = $propertyRegistry;
	}

	/**
	 * @since 1.0
	 *
	 * @return bool
	 */
	public function registerProperties() {
		$propertyDefinitions = [
			self::SG_TERM => [
				'label' => SG_PROP_GLT,
				'type'  => '_txt',
				'alias' => wfMessage( 'semanticglossary-prop-glt' )->text()
			],
			self::SG_DEFINITION => [
				'label' => SG_PROP_GLD,
				'type'  => '_txt',
				'alias' => wfMessage( 'semanticglossary-prop-gld' )->text()
			],
			self::SG_LINK => [
				'label' => SG_PROP_GLL,
				'type'  => '_txt',
				'alias' => wfMessage( 'semanticglossary-prop-gll' )->text()
			],
			self::SG_STYLE => [
				'label' => SG_PROP_GLS,
				'type'  => '_txt',
				'alias' => wfMessage( 'semanticglossary-prop-gls' )->text()
			]
		];

		return $this->registerPropertiesFromList( $propertyDefinitions );
	}

	/**
	 * @param string[][] $propertyList
	 * @return bool
	 */
	protected function registerPropertiesFromList( array $propertyList ) {
		foreach ( $propertyList as $propertyId => $definition ) {

			$this->propertyRegistry->registerProperty(
				$propertyId,
				$definition['type'],
				$definition['label'],
				true
			);

			$this->propertyRegistry->registerPropertyAlias(
				$propertyId,
				$definition['alias']
			);
		}

		return true;
	}

}
