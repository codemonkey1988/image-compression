<?php

declare(strict_types=1);

/*
 * This file is part of the "image_compression" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Codemonkey1988\ImageCompression\Tests\Unit\Compressor;

use Codemonkey1988\ImageCompression\Service\ConfigurationService;
use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

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

        if (class_exists('\\TYPO3\\CMS\\Lang\\LanguageService')) {
            $GLOBALS['LANG'] = GeneralUtility::makeInstance('TYPO3\\CMS\\Lang\\LanguageService');
            $GLOBALS['LANG']->init('en');
        }
    }

    /**
     * Tests the default extension configuration.
     *
     * @test
     */
    public function testDefaultConfiguration()
    {
        $configurationService = GeneralUtility::makeInstance(ConfigurationService::class);

        self::assertFalse($configurationService->isCompressOnUploadEnabled());

        // Default Tinify config
        self::assertEmpty($configurationService->getTinifyApiKey());
        self::assertEquals(500, $configurationService->getTinifyMaxMonthlyCompressionCount());
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

        self::assertTrue($configurationService->isCompressOnUploadEnabled());

        // Default Tinify config
        self::assertEquals('MyApiKey', $configurationService->getTinifyApiKey());
        self::assertEquals(600, $configurationService->getTinifyMaxMonthlyCompressionCount());
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

        self::assertTrue($configurationService->isCompressOnUploadEnabled());

        // Default Tinify config
        self::assertEquals('MyApiKey', $configurationService->getTinifyApiKey());
        self::assertEquals(600, $configurationService->getTinifyMaxMonthlyCompressionCount());
    }
}
