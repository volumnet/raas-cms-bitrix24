<?php
/**
 * RAAS CMS to Bitrix24 task data generator for feedbacks
 * @author Alex V. Surnin <info@volumnet.ru>
 * @copyright Volume Networks, 2019
 */
namespace RAAS\CMS\Bitrix24;

use RAAS\CMS\Feedback;
use Exception;

/**
 * RAAS CMS to Bitrix24 task data generator for feedbacks class
 */
class FeedbackTaskDataGenerator extends TaskDataGenerator
{
    use FeedbackDataTrait;
}
