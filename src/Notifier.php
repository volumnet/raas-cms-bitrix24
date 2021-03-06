<?php
/**
 * RAAS CMS to Bitrix24 adapter
 * @author Alex V. Surnin <info@volumnet.ru>
 * @copyright Volume Networks, 2018
 */
namespace RAAS\CMS\Bitrix24;

use SOME\SOME;
use VolumNet\Bitrix24\Webhook;
use Exception;

/**
 * RAAS CMS to Bitrix24 adapter (notifier) class
 */
class Notifier
{
    /**
     * Webhook object to use
     * @var Webhook
     */
    protected $webhook;

    /**
     * Bitrix24 user ID# to assign lead to
     * @var int
     */
    protected $assignedById;

    /**
     * Bitrix24 source ID#
     * @var string
     */
    protected $sourceId;

    /**
     * Class constructor
     * @param string $domain Bitrix24 domain
     * @param string $webhook Webhook ID#
     * @param int $assignedById Bitrix24 user ID# to assign lead to
     * @param string $sourceId Bitrix24 source ID#
     */
    public function __construct($domain, $webhook, $assignedById = null, $sourceId = null)
    {
        $this->webhook = new Webhook($domain, $webhook);
        $this->assignedById = (int)$assignedById ?: null;
        $this->sourceId = trim($sourceId);
    }


    /**
     * Notify about some item
     * @param SOME $item Item to notify about
     * @param boolean $reportAllFields true, if report all fields in comments; false if only not affected fields
     * @return array<mixed>|false Response from the server or false in the case of error (also will output in syslog)
     */
    public function notify(SOME $item, $reportAllFields = true)
    {
        try {
            $cloneChecker = new CloneChecker($this->webhook);
            $result = false;
            if ($generator = DataGenerator::spawn($item, $cloneChecker)) {
                if ($data = $generator->getData($reportAllFields)) {
                    if ((int)$this->assignedById) {
                        $data['fields']['ASSIGNED_BY_ID'] = (int)$this->assignedById;
                    }
                    if ($this->sourceId) {
                        $data['fields']['SOURCE_ID'] = $this->sourceId;
                    }
                    $result = $this->webhook->method('crm.lead.add', $data);
                }
            }
            return $result;
        } catch (Exception $e) {
            syslog(LOG_ERR, $e->getMessage());
        }
        return false;
    }
}
