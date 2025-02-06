<?php

namespace SG;

use SMW\DIProperty;
use SMW\SemanticData;
use SMW\Store;

/**
 * @ingroup SG
 * @ingroup SemanticGlossary
 *
 * @license GPL-2.0-or-later
 * @since 1.0
 *
 * @author Stephan Gambke
 */
class SemanticDataComparator {

	/**
	 * @var Store
	 */
	private $store = null;

	/**
	 * @var SemanticData
	 */
	private $semanticData = null;

	/**
	 * @since 1.0
	 *
	 * @param Store $store
	 * @param SemanticData $semanticData
	 */
	public function __construct( Store $store, SemanticData $semanticData ) {
		$this->store = $store;
		$this->semanticData = $semanticData;
	}

	/**
	 * @since 1.0
	 *
	 * @param string $propertyId
	 *
	 * @return bool
	 */
	public function compareForProperty( string $propertyId ) {
		list( $newEntries, $oldEntries ) = $this->lookupPropertyValues( $propertyId );

		if ( $this->hasNotSamePropertyValuesCount( $newEntries, $oldEntries ) ) {
			return true;
		}

		if ( $this->hasUnmatchPropertyValue( $newEntries, $oldEntries ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Returns the new and old entries for a given property
	 *
	 * @param string $propertyId
	 *
	 * @return array|array[]
	 *
	 */
	private function lookupPropertyValues( string $propertyId ) {
		$properties = $this->semanticData->getProperties();

		if ( array_key_exists( $propertyId, $properties ) ) {

			$newEntries = $this->semanticData->getPropertyValues( $properties[$propertyId] );
			$oldEntries = $this->store->getPropertyValues(
				$this->semanticData->getSubject(),
				$properties[$propertyId]
			);

			return [
				$newEntries,
				$oldEntries
			];
		}

		$newEntries = [];
		$oldEntries = [];

		try{
			$property = new DIProperty( $propertyId );
		} catch ( \Exception $e ) {
			return [
				$newEntries,
				$oldEntries
			];
		}

		$oldEntries = $this->store->getPropertyValues(
			$this->semanticData->getSubject(),
			$property
		);

		return [
			$newEntries,
			$oldEntries
		];
	}

	/**
	 * Returns true if the number of property values is different
	 *
	 * @param array $newEntries
	 * @param array $oldEntries
	 *
	 * @return bool
	 */
	private function hasNotSamePropertyValuesCount( array $newEntries, array $oldEntries ) {
		return count( $newEntries ) !== count( $oldEntries );
	}

	/**
	 * Returns true if the property values are different
	 *
	 * @param array $newEntries
	 * @param array $oldEntries
	 *
	 * @return bool
	 */
	private function hasUnmatchPropertyValue( array $newEntries, array $oldEntries ) {
		foreach ( $newEntries as $newDi ) {
			$found = false;
			foreach ( $oldEntries as $oldKey => $oldDi ) {
				if ( $newDi->getHash() === $oldDi->getHash() ) {
					$found = true;
					unset( $oldEntries[$oldKey] );
					break;
				}
			}

			// If no match was possible...
			if ( !$found ) {
				return true;
			}
		}

		// Are there unmatched old entries left?
		if ( count( $oldEntries ) > 0 ) {
			return true;
		}

		return false;
	}

}
