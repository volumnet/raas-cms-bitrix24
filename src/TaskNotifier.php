<?php
/**
 * RAAS CMS to Bitrix24 adapter for tasks
 * @author Alex V. Surnin <info@volumnet.ru>
 * @copyright Volume Networks, 2019
 */
namespace RAAS\CMS\Bitrix24;

use SOME\SOME;
use VolumNet\Bitrix24\Webhook;
use Exception;

/**
 * RAAS CMS to Bitrix24 adapter (notifier) class for tasks
 */
class TaskNotifier extends Notifier
{
    /**
     * Auditors IDs#
     * @var array
     */
    protected $auditorsIds = [];

    /**
     * Class constructor
     * @param string $domain Bitrix24 domain
     * @param string $webhook Webhook ID#
     * @param int $assignedById Bitrix24 user ID# to assign task to
     * @param array $auditorsIds Bitrix24 auditors IDs#
     */
    public function __construct($domain, $webhook, $assignedById = null, array $auditorsIds = [])
    {
        $this->webhook = new Webhook($domain, $webhook);
        $this->assignedById = (int)$assignedById ?: null;
        if ((array)$auditorsIds) {
            $this->auditorsIds = (array)$auditorsIds;
        }
    }

    /**
     * Notify about some item
     * @param SOME $item Item to notify about
     * @param boolean $reportAllFields true, if report all fields in comments; false if only not affected fields (not used, for compatibility)
     * @return array<mixed>|false Response from the server or false in the case of error (also will output in syslog)
     */
    public function notify(SOME $item, $reportAllFields = true)
    {
        try {
            $result = false;
            if ($generator = TaskDataGenerator::spawn($item)) {
                if ($data = $generator->getData()) {
                    if ((int)$this->assignedById) {
                        $data['fields']['RESPONSIBLE_ID'] = (int)$this->assignedById;
                    }
                    if ($this->auditorsIds) {
                        $data['fields']['AUDITORS'] = $this->auditorsIds;
                    }
                    $result = $this->webhook->method('tasks.task.add', $data);
                }
            }
            return $result;
        } catch (Exception $e) {
            syslog(LOG_ERR, $e->getMessage());
        }
        return false;
    }
}
