<?php

namespace SG\Tests;

use SG\PropertyRegistrationHelper;
use SMW\DIProperty;

/**
 * @covers \SG\PropertyRegistrationHelper
 * @group semantic-glossary
 *
 * @license GPL-2.0-or-later
 * @since 1.0
 *
 * @author mwjames
 */
class PropertyRegistrationHelperTest extends \PHPUnit\Framework\TestCase {

	public function testCanConstruct() {
		$propertyRegistry =
			$this->getMockBuilder( '\SMW\PropertyRegistry' )
			->disableOriginalConstructor()
			->getMock();

		$propertyRegistrationHelper = new PropertyRegistrationHelper( $propertyRegistry );

		$this->assertInstanceOf(
			'\SG\PropertyRegistrationHelper',
			$propertyRegistrationHelper
		);
	}

	public function testRegisterProperties() {
		$propertyRegistry =
			$this->getMockBuilder( '\SMW\PropertyRegistry' )
			->disableOriginalConstructor()
			->getMock();

		$propertyRegistry
			->expects( $this->exactly( 4 ) )
			->method( 'registerProperty' );

		$propertyRegistry
			->expects( $this->exactly( 4 ) )
			->method( 'registerPropertyAlias' );


		$propertyRegistrationHelper = new PropertyRegistrationHelper( $propertyRegistry );

		$this->assertTrue(
			$propertyRegistrationHelper->registerProperties()
		);
	}

	/**
	 * @dataProvider propertyDefinitionDataProvider
	 *
	 * @param string $id
	 * @param string $label
	 */
	public function testRegisteredPropertyById( $id, $label ) {
		$property = new DIProperty( $id );

		$this->assertInstanceOf( '\SMW\DIProperty', $property );
		$this->assertEquals( $label, $property->getLabel() );
		$this->assertTrue( $property->isShown() );
	}

	/**
	 * @return string[][]
	 */
	public function propertyDefinitionDataProvider() {
		$provider = array();

		$provider[] = array( PropertyRegistrationHelper::SG_TERM, SG_PROP_GLT );
		$provider[] = array( PropertyRegistrationHelper::SG_DEFINITION, SG_PROP_GLD );
		$provider[] = array( PropertyRegistrationHelper::SG_LINK, SG_PROP_GLL );
		$provider[] = array( PropertyRegistrationHelper::SG_STYLE, SG_PROP_GLS );

		return $provider;
	}

}
