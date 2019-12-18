<?php
/**
 * RAAS CMS to Bitrix24 task data generator
 * @author Alex V. Surnin <info@volumnet.ru>
 * @copyright Volume Networks, 2018
 */
namespace RAAS\CMS\Bitrix24;

use SOME\SOME;
use RAAS\Attachment;
use RAAS\CMS\Feedback;
use RAAS\CMS\Shop\Order;
use SOME\Text;

/**
 * RAAS CMS to Bitrix24 task data generator class
 */
abstract class TaskDataGenerator extends DataGenerator
{
    /**
     * Returns the data about some item
     * @param boolean $reportAllFields true, if report all fields in comments; false if only not affected fields (not used, for compatibility)
     * @return array<mixed> Data compatible with Bitrix24 tasks.task.add method https://dev.1c-bitrix.ru/rest_help/tasks/task/tasks/tasks_task_add.php
     */
    public function getData($reportAllFields = true)
    {
        $result = array();
        $fields = array();

        if ($title = $this->getTitle()) {
            $fields['TITLE'] = $title;
        }
        if ($comments = $this->getComments([])) {
            $fields['DESCRIPTION'] = $comments;
        }

        if ($fields) {
            $result['fields'] = $fields;
        }
        return $result;
    }


    /**
     * Returns data generator for the certain type of item
     * @param SOME $item Item to generate data for
     * @param CloneChecker|null $cloneChecker Clone Checker, if necessary
     * @return DataGenerator
     */
    public static function spawn(SOME $item, CloneChecker $cloneChecker = null)
    {
        if ($item instanceof Order) {
            return new OrderTaskDataGenerator($item, $cloneChecker);
        } elseif ($item instanceof Feedback) {
            return new FeedbackTaskDataGenerator($item, $cloneChecker);
        }
        return null;
    }
}
