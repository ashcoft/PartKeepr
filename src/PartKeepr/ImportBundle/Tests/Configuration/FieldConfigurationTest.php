<?php

namespace PartKeepr\ImportBundle\Tests\Configuration;

use PartKeepr\ImportBundle\Configuration\FieldConfiguration;

/**
 * Unit tests for the field configuration of the importer.
 *
 * The configuration parser doesn't use any of the dependencies injected into
 * BaseConfiguration, so it can be instantiated without booting the Symfony
 * container.
 */
class FieldConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        FieldConfiguration::$logs = [];
    }

    public function testParseConfigurationWithoutModeReturnsFalse()
    {
        $this->assertFalse($this->getFieldConfiguration()->parseConfiguration(new \stdClass()));
    }

    public function testParseConfigurationRejectsAnUnknownMode()
    {
        $configuration = new \stdClass();
        $configuration->fieldConfiguration = 'unknownMode';

        $this->setExpectedException('\Exception', 'The key fieldConfiguration contains an invalid value!');

        $this->getFieldConfiguration()->parseConfiguration($configuration);
    }

    public function testFixedValueConfiguration()
    {
        $configuration = new \stdClass();
        $configuration->fieldConfiguration = FieldConfiguration::FIELDCONFIGURATION_FIXEDVALUE;
        $configuration->setToValue = 'foobar';

        $fieldConfiguration = $this->getFieldConfiguration();
        $this->assertTrue($fieldConfiguration->parseConfiguration($configuration));

        $this->assertEquals('foobar', $fieldConfiguration->import(['someColumn' => 'someValue']));
    }

    public function testFixedValueConfigurationRequiresTheValue()
    {
        $configuration = new \stdClass();
        $configuration->fieldConfiguration = FieldConfiguration::FIELDCONFIGURATION_FIXEDVALUE;

        $this->setExpectedException('\Exception', 'The key setToValue does not exist for mode fixedValue!');

        $this->getFieldConfiguration()->parseConfiguration($configuration);
    }

    public function testCopyFromConfiguration()
    {
        $configuration = new \stdClass();
        $configuration->fieldConfiguration = FieldConfiguration::FIELDCONFIGURATION_COPYFROM;
        $configuration->copyFromField = 'sourceColumn';

        $fieldConfiguration = $this->getFieldConfiguration();
        $this->assertTrue($fieldConfiguration->parseConfiguration($configuration));

        $this->assertEquals('someValue', $fieldConfiguration->import(['sourceColumn' => 'someValue']));
    }

    public function testCopyFromConfigurationRequiresTheSourceColumn()
    {
        $configuration = new \stdClass();
        $configuration->fieldConfiguration = FieldConfiguration::FIELDCONFIGURATION_COPYFROM;

        $this->setExpectedException('\Exception', 'The key copyFromField does not exist for mode copyFrom!');

        $this->getFieldConfiguration()->parseConfiguration($configuration);
    }

    public function testCopyFromConfigurationIgnoresMissingColumns()
    {
        $configuration = new \stdClass();
        $configuration->fieldConfiguration = FieldConfiguration::FIELDCONFIGURATION_COPYFROM;
        $configuration->copyFromField = 'sourceColumn';

        $fieldConfiguration = $this->getFieldConfiguration();
        $fieldConfiguration->parseConfiguration($configuration);

        $this->assertNull($fieldConfiguration->import(['otherColumn' => 'someValue']));
    }

    public function testIgnoreConfiguration()
    {
        $configuration = new \stdClass();
        $configuration->fieldConfiguration = FieldConfiguration::FIELDCONFIGURATION_IGNORE;

        $fieldConfiguration = $this->getFieldConfiguration();
        $this->assertTrue($fieldConfiguration->parseConfiguration($configuration));

        $this->assertNull($fieldConfiguration->import(['sourceColumn' => 'someValue']));
    }

    /**
     * Creates a field configuration without invoking the constructor, as the
     * parser under test doesn't need the injected dependencies.
     *
     * @return FieldConfiguration
     */
    private function getFieldConfiguration()
    {
        $reflection = new \ReflectionClass('PartKeepr\ImportBundle\Configuration\FieldConfiguration');

        /**
         * @var FieldConfiguration
         */
        $fieldConfiguration = $reflection->newInstanceWithoutConstructor();
        $fieldConfiguration->setFieldName('testField');

        return $fieldConfiguration;
    }
}
