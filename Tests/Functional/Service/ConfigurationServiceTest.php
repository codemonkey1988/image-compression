<?php
declare(strict_types=1);
namespace Codemonkey1988\ImageCompression\Tests\Unit\Compressor;

/*
 * This file is part of the TYPO3 responsive images project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read
 * LICENSE file that was distributed with this source code.
 *
 */

use Codemonkey1988\ImageCompression\Service\ConfigurationService;
use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Lang\LanguageService;

class ConfigurationServiceTest extends FunctionalTestCase
{
    /**
     * @var array
     */
    protected $testExtensionsToLoad = ['typo3conf/ext/image_compression'];

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    protected function setUp()
    {
        parent::setUp();

        $GLOBALS['LANG'] = GeneralUtility::makeInstance(LanguageService::class);
        $GLOBALS['LANG']->init('en');
    }

    /**
     * Tests the default extension configuration.
     *
     * @test
     */
    public function testDefaultConfiguration()
    {
        $configurationService = GeneralUtility::makeInstance(ConfigurationService::class);

        $this->assertFalse($configurationService->isCompressOnUploadEnabled());

        // Default Tinify config
        $this->assertEmpty($configurationService->getTinifyApiKey());
        $this->assertEquals(500, $configurationService->getTinifyMaxMonthlyCompressionCount());
    }

    /**
     * Tests the default extension configuration.
     *
     * @test
     */
    public function testCustomConfigurationV8()
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['image_compression'] = serialize([
            'enableCompressOnUpload' => '1',
            'tinifyApiKey' => 'MyApiKey',
            'tinifyCompressionCount' => '600',
            'tinifyExtensions' => 'tiff,gif',
        ]);

        $configurationService = GeneralUtility::makeInstance(ConfigurationService::class);

        $this->assertTrue($configurationService->isCompressOnUploadEnabled());

        // Default Tinify config
        $this->assertEquals('MyApiKey', $configurationService->getTinifyApiKey());
        $this->assertEquals(600, $configurationService->getTinifyMaxMonthlyCompressionCount());
    }

    /**
     * Tests the default extension configuration.
     *
     * @test
     */
    public function testCustomConfigurationV9()
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['image_compression'] = [
            'enableCompressOnUpload' => '1',
            'tinifyApiKey' => 'MyApiKey',
            'tinifyCompressionCount' => '600',
            'tinifyExtensions' => 'tiff,gif',
        ];

        $configurationService = GeneralUtility::makeInstance(ConfigurationService::class);

        $this->assertTrue($configurationService->isCompressOnUploadEnabled());

        // Default Tinify config
        $this->assertEquals('MyApiKey', $configurationService->getTinifyApiKey());
        $this->assertEquals(600, $configurationService->getTinifyMaxMonthlyCompressionCount());
    }
}
