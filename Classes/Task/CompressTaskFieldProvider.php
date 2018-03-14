<?php
declare(strict_types=1);
namespace Codemonkey1988\ImageCompression\Task;

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

use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Scheduler\AdditionalFieldProviderInterface;
use TYPO3\CMS\Scheduler\Controller\SchedulerModuleController;
use TYPO3\CMS\Scheduler\Task\AbstractTask;

/**
 * Class CompressTaskFieldProvider
 *
 * @author  Tim Schreiner <schreiner.tim@gmail.com>
 */
class CompressTaskFieldProvider implements AdditionalFieldProviderInterface
{
    /**
     * @var array
     */
    protected $taskInfo;

    /**
     * @var CompressTask
     */
    protected $task;

    /**
     * @var \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController
     */
    protected $schedulerModule;

    /**
     * @param array $taskInfo
     * @param AbstractTask $task
     * @param SchedulerModuleController $schedulerModule
     * @return array
     */
    public function getAdditionalFields(array &$taskInfo, $task, SchedulerModuleController $schedulerModule): array
    {
        $this->taskInfo = &$taskInfo;
        $this->task = $task;
        $this->schedulerModule = $schedulerModule;

        $filesPerRunId = 'files_per_run';
        $compressOriginal = 'compress_original';
        $compressProcessed = 'compress_processed';
        $supportedExtension = 'supported_extensions';

        return [
            $filesPerRunId => $this->generateFieldPerRunField($filesPerRunId),
            $compressOriginal => $this->generateCompressOriginalField($compressOriginal),
            $compressProcessed => $this->generateCompressProcessedField($compressProcessed),
            $supportedExtension => $this->generateSupportedExtensionsField($supportedExtension),
        ];
    }

    /**
     * @param array $submittedData
     * @param SchedulerModuleController $schedulerModule
     * @return bool
     */
    public function validateAdditionalFields(array &$submittedData, SchedulerModuleController $schedulerModule): bool
    {
        if (empty($submittedData['files_per_run'])) {
            $schedulerModule->addMessage($GLOBALS['LANG']->sL('Please enter a numeric value'), FlashMessage::ERROR);

            return false;
        } elseif (!is_numeric($submittedData['files_per_run'])) {
            $schedulerModule->addMessage($GLOBALS['LANG']->sL('Value has to be numeric'), FlashMessage::ERROR);

            return false;
        } elseif (intval($submittedData['files_per_run']) <= 0) {
            $schedulerModule->addMessage($GLOBALS['LANG']->sL('Value has to be greater 0'), FlashMessage::ERROR);

            return false;
        }

        if (empty($submittedData['supported_extensions'])) {
            $schedulerModule->addMessage($GLOBALS['LANG']->sL('Please enter file extensions to process'), FlashMessage::ERROR);

            return false;
        }

        return true;
    }

    /**
     * @param array $submittedData
     * @param AbstractTask $task
     */
    public function saveAdditionalFields(array $submittedData, AbstractTask $task)
    {
        if ($task instanceof CompressTask) {
            $task->filesPerRun = (int)$submittedData['files_per_run'];
            $task->compressOriginal = !empty($submittedData['compress_original']);
            $task->compressProcessed = !empty($submittedData['compress_processed']);
            $task->supportedExtensions = $submittedData['supported_extensions'];
        }
    }

    /**
     * @param string $fieldId
     * @return array
     */
    protected function generateFieldPerRunField(string $fieldId): array
    {
        if (!isset($taskInfo['files_per_run'])) {
            $taskInfo['files_per_run'] = '25';

            if ($this->schedulerModule->CMD === 'edit') {
                $taskInfo['files_per_run'] = $this->task->filesPerRun;
            }
        }

        $fieldHtml = '<input type="text" name="tx_scheduler[files_per_run]" id="' . $fieldId . '" value="' . (int)$taskInfo['files_per_run'] . '" />';

        return [
            'code' => $fieldHtml,
            'label' => 'LLL:EXT:image_compression/Resources/Private/Language/locallang_be.xlf:task.compress.field.files_per_run',
            'cshKey' => '_MOD_tools_txschedulerM1',
            'cshLabel' => $fieldId,
        ];
    }

    /**
     * @param string $fieldId
     * @return array
     */
    protected function generateCompressOriginalField(string $fieldId): array
    {
        if (!isset($taskInfo['compress_original'])) {
            $taskInfo['compress_original'] = '1';

            if ($this->schedulerModule->CMD === 'edit') {
                $taskInfo['compress_original'] = $this->task->compressOriginal;
            }
        }

        $fieldHtml = '<input type="checkbox" name="tx_scheduler[compress_original]" id="' . $fieldId . '" value="1"';

        if ($taskInfo['compress_original']) {
            $fieldHtml .= ' checked="checked"';
        }

        $fieldHtml .= ' />';

        return [
            'code' => $fieldHtml,
            'label' => 'LLL:EXT:image_compression/Resources/Private/Language/locallang_be.xlf:task.compress.field.compress_original',
            'cshKey' => '_MOD_tools_txschedulerM1',
            'cshLabel' => $fieldId,
        ];
    }

    /**
     * @param string $fieldId
     * @return array
     */
    protected function generateCompressProcessedField(string $fieldId): array
    {
        if (!isset($taskInfo['compress_processed'])) {
            $taskInfo['compress_processed'] = '1';

            if ($this->schedulerModule->CMD === 'edit') {
                $taskInfo['compress_processed'] = $this->task->compressProcessed;
            }
        }

        $fieldHtml = '<input type="checkbox" name="tx_scheduler[compress_processed]" id="' . $fieldId . '" value="1"';

        if ($taskInfo['compress_processed']) {
            $fieldHtml .= ' checked="checked"';
        }

        $fieldHtml .= ' />';

        return [
            'code' => $fieldHtml,
            'label' => 'LLL:EXT:image_compression/Resources/Private/Language/locallang_be.xlf:task.compress.field.compress_processed',
            'cshKey' => '_MOD_tools_txschedulerM1',
            'cshLabel' => $fieldId,
        ];
    }

    /**
     * @param string $fieldId
     * @return array
     */
    protected function generateSupportedExtensionsField(string $fieldId): array
    {
        if (!isset($taskInfo['supported_extensions'])) {
            $taskInfo['supported_extensions'] = 'jpg,jpeg,png';

            if ($this->schedulerModule->CMD === 'edit') {
                $taskInfo['supported_extensions'] = $this->task->supportedExtensions;
            }
        }

        $fieldHtml = '<input type="text" name="tx_scheduler[supported_extensions]" id="' . $fieldId . '" value="' . htmlspecialchars($taskInfo['supported_extensions']) . '" />';

        return [
            'code' => $fieldHtml,
            'label' => 'LLL:EXT:image_compression/Resources/Private/Language/locallang_be.xlf:task.compress.field.supported_extensions',
            'cshKey' => '_MOD_tools_txschedulerM1',
            'cshLabel' => $fieldId,
        ];
    }
}
