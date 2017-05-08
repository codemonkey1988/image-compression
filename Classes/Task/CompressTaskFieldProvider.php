<?php
namespace Codemonkey1988\ImageCompression\Task;

/***************************************************************
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Scheduler\AdditionalFieldProviderInterface;

/**
 * Class CompressTaskFieldProvider
 *
 * @package Codemonkey1988\ImageCompression\Task
 * @author  Tim Schreiner <schreiner.tim@gmail.com>
 */
class CompressTaskFieldProvider implements AdditionalFieldProviderInterface
{
    /**
     * @param array                                                     $taskInfo
     * @param \TYPO3\CMS\Scheduler\Task\AbstractTask                    $task
     * @param \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $schedulerModule
     * @return array
     */
    public function getAdditionalFields(array &$taskInfo, $task, \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $schedulerModule)
    {
        if (!isset($taskInfo['files_per_run'])) {
            $taskInfo['files_per_run'] = '25';

            if ($schedulerModule->CMD === 'edit') {
                $taskInfo['files_per_run'] = $task->files_per_run;
            }
        }

        $fieldId   = 'files_per_run';
        $fieldHtml = '<input type="text" name="tx_scheduler[files_per_run]" id="' . $fieldId . '" value="' . htmlspecialchars(
                $taskInfo['files_per_run']
            ) . '" />';

        return [
            $fieldId => [
                'code'     => $fieldHtml,
                'label'    => 'Files per run',
                'cshKey'   => '_MOD_tools_txschedulerM1',
                'cshLabel' => $fieldId
            ]
        ];
    }

    /**
     * @param array                                                     $submittedData
     * @param \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $schedulerModule
     * @return bool
     */
    public function validateAdditionalFields(
        array &$submittedData,
        \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $schedulerModule
    ) {
        if (!is_numeric($submittedData['files_per_run'])) {
            $schedulerModule->addMessage($GLOBALS['LANG']->sL('Value has to be numeric'), FlashMessage::ERROR);

            return false;
        } elseif (intval($submittedData['files_per_run']) <= 0) {
            $schedulerModule->addMessage($GLOBALS['LANG']->sL('Value has to be greater 0'), FlashMessage::ERROR);

            return false;
        }

        return true;
    }

    /**
     * @param array                                  $submittedData
     * @param \TYPO3\CMS\Scheduler\Task\AbstractTask $task
     */
    public function saveAdditionalFields(array $submittedData, \TYPO3\CMS\Scheduler\Task\AbstractTask $task)
    {
        $task->files_per_run = $submittedData['files_per_run'];
    }
}