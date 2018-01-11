<?php
/**
 * RAAS CMS to Bitrix24 clones checker
 * @author Alex V. Surnin <info@volumnet.ru>
 * @copyright Volume Networks, 2018
 */
namespace RAAS\CMS\Bitrix24;

use SOME\Text;
use VolumNet\Bitrix24\Webhook;

/**
 * RAAS CMS to Bitrix24 clones checker class
 */
class CloneChecker
{
    /**
     * Class constructor
     * @param Webhook $webhook Bitrix24 webhook to use
     */
    public function __construct(Webhook $webhook)
    {
        $this->webhook = $webhook;
    }


    /**
     * Searches similars by phones
     * @param array<string> $phones Array of phones to check
     * @return array<[string $phone] => array<int $id>> found IDs by phones
     */
    public function searchByPhone(array $phones)
    {
        $similars = array();
        foreach ($phones as $phone) {
            $bphone = Text::beautifyPhone($phone);
            $result = $this->webhook->method(
                'crm.duplicate.findbycomm',
                array(
                    'entity_type' => 'CONTACT',
                    'type' => 'PHONE',
                    'values' => array(
                        $bphone,
                        '+7' . $bphone,
                        '8' . $bphone
                    )
                )
            );
            if ($result->result->CONTACT) {
                $similars[$phone] = (array)$result->result->CONTACT;
            }
        }
        return $similars;
    }


    /**
     * Searches similars by emails
     * @param array<string> $emails Array of emails to check
     * @return array<[string $email] => array<int $id>> found IDs by emails
     */
    public function searchByEmail(array $emails)
    {
        $similars = array();
        foreach ($emails as $email) {
            $result = $this->webhook->method(
                'crm.duplicate.findbycomm',
                array(
                    'entity_type' => 'CONTACT',
                    'type' => 'EMAIL',
                    'values' => array($email)
                )
            );
            if ($result->result->CONTACT) {
                $similars[$email] = (array)$result->result->CONTACT;
            }
        }
        return $similars;
    }


    /**
     * Searches similars by phones and emails
     * @param array<string> $phones Array of phones to check
     * @param array<string> $emails Array of emails to check
     * @return array('phone' => array<[string $phone] => array<int $id>>, 'email' => array<[string $email] => array<int $id>>) found IDs
     */
    public function search(array $phones, array $emails)
    {
        $similars = array();
        if ($similarsByPhone = $this->searchByPhone($phones)) {
            $similars['phone'] = $similarsByPhone;
        }
        if ($similarsByEmail = $this->searchByEmail($emails)) {
            $similars['email'] = $similarsByEmail;
        }
        return $similars;
    }
}
