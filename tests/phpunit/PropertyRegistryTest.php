<?php

namespace SG\Tests;

use SG\PropertyRegistry;
use SMW\DIProperty;

/**
 * @covers \SG\PropertyRegistry
 *
 * @ingroup Test
 *
 * @group SG
 * @group SGExtension
 *
 * @licence GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class PropertyRegistryTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {
		$this->assertInstanceOf(
			'\SG\PropertyRegistry',
			PropertyRegistry::getInstance()
		);
	}

	public function testRegisterPropertiesAndAliases() {
		PropertyRegistry::clear();
		$this->assertTrue( PropertyRegistry::getInstance()->registerPropertiesAndAliases() );
	}

	/**
	 * @dataProvider propertyDefinitionDataProvider
	 */
	public function testRegisteredPropertyById( $id, $label ) {

		$property = new DIProperty( $id );

		$this->assertInstanceOf( '\SMW\DIProperty', $property );
		$this->assertEquals( $label, $property->getLabel() );
		$this->assertTrue( $property->isShown() );
	}

	public function propertyDefinitionDataProvider() {

		$provider = array();

		$provider[] = array( PropertyRegistry::SG_TERM, SG_PROP_GLT );
		$provider[] = array( PropertyRegistry::SG_DEFINITION, SG_PROP_GLD );
		$provider[] = array( PropertyRegistry::SG_LINK, SG_PROP_GLL );
		$provider[] = array( PropertyRegistry::SG_STYLE, SG_PROP_GLS );

		return $provider;
	}

}
