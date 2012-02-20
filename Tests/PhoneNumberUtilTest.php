<?php

namespace com\google\i18n\phonenumbers;

require_once dirname(__FILE__) . '/../PhoneNumberUtil.php';
require_once dirname(__FILE__) . '/../RegionCode.php';
require_once dirname(__FILE__) . '/../PhoneNumber.php';
require_once dirname(__FILE__) . '/../CountryCodeToRegionCodeMapForTesting.php';

/**
 * Test class for PhoneNumberUtil.
 * Generated by PHPUnit on 2012-02-12 at 00:30:35.
 */
class PhoneNumberUtilTest extends \PHPUnit_Framework_TestCase {

	private static $bsNumber = NULL;
	private static $internationalTollFree = NULL;
	const TEST_META_DATA_FILE_PREFIX = "PhoneNumberMetadataForTesting";
	/**
	 * @var PhoneNumberUtil
	 */
	protected $phoneUtil;

	public function __construct() {
		$this->phoneUtil = self::initializePhoneUtilForTesting();
	}

	private static function initializePhoneUtilForTesting() {
		self::$bsNumber = new PhoneNumber();
		self::$bsNumber->setCountryCode(1)->setNationalNumber(2423651234);
		self::$internationalTollFree = new PhoneNumber();
		self::$internationalTollFree->setCountryCode(800)->setNationalNumber(12345678);
		PhoneNumberUtil::resetInstance();
		return PhoneNumberUtil::getInstance(self::TEST_META_DATA_FILE_PREFIX, CountryCodeToRegionCodeMapForTesting::$countryCodeToRegionCodeMap);
	}

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown() {
		
	}

	public function testGetSupportedRegions() {
		$this->assertGreaterThan(0, count($this->phoneUtil->getSupportedRegions()));
	}

	public function testGetInstanceLoadUSMetadata() {
		$metadata = $this->phoneUtil->getMetadataForRegion(RegionCode::US);
		$this->assertEquals("US", $metadata->getId());
		$this->assertEquals(1, $metadata->getCountryCode());
		$this->assertEquals("011", $metadata->getInternationalPrefix());
		$this->assertTrue($metadata->hasNationalPrefix());
		$this->assertEquals(2, $metadata->numberFormatSize());
		$this->assertEquals("(\\d{3})(\\d{3})(\\d{4})", $metadata->getNumberFormat(1)->getPattern());
		$this->assertEquals("$1 $2 $3", $metadata->getNumberFormat(1)->getFormat());
		$this->assertEquals("[13-689]\\d{9}|2[0-35-9]\\d{8}", $metadata->getGeneralDesc()->getNationalNumberPattern());
		$this->assertEquals("\\d{7}(?:\\d{3})?", $metadata->getGeneralDesc()->getPossibleNumberPattern());
		$this->assertTrue($metadata->getGeneralDesc()->exactlySameAs($metadata->getFixedLine()));
		$this->assertEquals("\\d{10}", $metadata->getTollFree()->getPossibleNumberPattern());
		$this->assertEquals("900\\d{7}", $metadata->getPremiumRate()->getNationalNumberPattern());
		// No shared-cost data is available, so it should be initialised to "NA".
		$this->assertEquals("NA", $metadata->getSharedCost()->getNationalNumberPattern());
		$this->assertEquals("NA", $metadata->getSharedCost()->getPossibleNumberPattern());
	}

	/**
	 * @covers com\google\i18n\phonenumbers\PhoneNumberUtil::isViablePhoneNumber
	 * @todo Implement testIsViablePhoneNumber().
	 */
	public function testIsViablePhoneNumber() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers com\google\i18n\phonenumbers\PhoneNumberUtil::normalize
	 * @todo Implement testNormalize().
	 */
	public function testNormalize() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers com\google\i18n\phonenumbers\PhoneNumberUtil::normalizeDigitsOnly
	 * @todo Implement testNormalizeDigitsOnly().
	 */
	public function testNormalizeDigitsOnly() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers com\google\i18n\phonenumbers\PhoneNumberUtil::isValidNumberForRegion
	 * @todo Implement testIsValidNumberForRegion().
	 */
	public function testIsValidNumberForRegion() {
		// This number is valid for the Bahamas, but is not a valid US number.
		$this->assertTrue($this->phoneUtil->isValidNumber(self::$bsNumber));
		$this->assertTrue($this->phoneUtil->isValidNumberForRegion(self::$bsNumber, RegionCode::BS));
		$this->assertFalse($this->phoneUtil->isValidNumberForRegion(self::$bsNumber, RegionCode::US));
		$bsInvalidNumber = new PhoneNumber();
		$bsInvalidNumber->setCountryCode(1)->setNationalNumber(2421232345);
		// This number is no longer valid.
		$this->assertFalse($this->phoneUtil->isValidNumber($bsInvalidNumber));

		// La Mayotte and Reunion use 'leadingDigits' to differentiate them.
		$reNumber = new PhoneNumber();
		$reNumber->setCountryCode(262)->setNationalNumber(262123456);
		$this->assertTrue($this->phoneUtil->isValidNumber($reNumber));
		$this->assertTrue($this->phoneUtil->isValidNumberForRegion($reNumber, RegionCode::RE));
		$this->assertFalse($this->phoneUtil->isValidNumberForRegion($reNumber, RegionCode::YT));
		// Now change the number to be a number for La Mayotte.
		$reNumber->setNationalNumber(269601234);
		$this->assertTrue($this->phoneUtil->isValidNumberForRegion($reNumber, RegionCode::YT));
		$this->assertFalse($this->phoneUtil->isValidNumberForRegion($reNumber, RegionCode::RE));
		// This number is no longer valid for La Reunion.
		$reNumber->setNationalNumber(269123456);
		$this->assertFalse($this->phoneUtil->isValidNumberForRegion($reNumber, RegionCode::YT));
		$this->assertFalse($this->phoneUtil->isValidNumberForRegion($reNumber, RegionCode::RE));
		$this->assertFalse($this->phoneUtil->isValidNumber($reNumber));
		// However, it should be recognised as from La Mayotte, since it is valid for this region.
		$this->assertEquals(RegionCode::YT, $this->phoneUtil->getRegionCodeForNumber($reNumber));
		// This number is valid in both places.
		$reNumber->setNationalNumber(800123456);
		$this->assertTrue($this->phoneUtil->isValidNumberForRegion($reNumber, RegionCode::YT));
		$this->assertTrue($this->phoneUtil->isValidNumberForRegion($reNumber, RegionCode::RE));
		$this->assertTrue($this->phoneUtil->isValidNumberForRegion(self::$internationalTollFree, RegionCode::UN001));
		$this->assertFalse($this->phoneUtil->isValidNumberForRegion(self::$internationalTollFree, RegionCode::US));
		$this->assertFalse($this->phoneUtil->isValidNumberForRegion(self::$internationalTollFree, RegionCode::ZZ));

		$invalidNumber = new PhoneNumber();
		// Invalid country calling codes.
		$invalidNumber->setCountryCode(3923)->setNationalNumber(2366);
		$this->assertFalse($this->phoneUtil->isValidNumberForRegion($invalidNumber, RegionCode::ZZ));
		$invalidNumber->setCountryCode(3923)->setNationalNumber(2366);
		$this->assertFalse($this->phoneUtil->isValidNumberForRegion($invalidNumber, RegionCode::UN001));
		$invalidNumber->setCountryCode(0)->setNationalNumber(2366);
		$this->assertFalse($this->phoneUtil->isValidNumberForRegion($invalidNumber, RegionCode::UN001));
		$invalidNumber->setCountryCode(0);
		$this->assertFalse($this->phoneUtil->isValidNumberForRegion($invalidNumber, RegionCode::ZZ));
	}

}