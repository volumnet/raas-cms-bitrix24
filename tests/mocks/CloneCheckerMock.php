<?php
namespace RAAS\CMS\Bitrix24;

class CloneCheckerMock extends CloneChecker
{
    /**
     * Class constructor
     */
    public function __construct()
    {
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
            $similars[$phone] = array(1, 2, 3);
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
            $similars[$email] = array(1, 2, 3);
        }
        return $similars;
    }
}
