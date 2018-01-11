<?php
namespace RAAS\CMS\Bitrix24;

use PHPUnit_Framework_TestCase;
use VolumNet\Bitrix24\Webhook;

/**
 * RAAS CMS to Bitrix24 clones checker test
 */
class CloneCheckerTest extends PHPUnit_Framework_TestCase
{
    protected static $wh;

    protected static $testContactId;

    public static function setUpBeforeClass()
    {
        self::$wh = new Webhook($GLOBALS['bitrix24']['domain'], $GLOBALS['bitrix24']['webhook']);
        $data = array(
            'fields' => array(
                'NAME' => 'User',
                'LAST_NAME' => 'Test',
                'ADDRESS' => 'Test address',
                'SOURCE_ID' => 'WEB',
                'PHONE' => array(
                    array(
                        'VALUE' => '+7 999 000-00-00',
                        'VALUE_TYPE' => 'WORK'
                    ),
                    array(
                        'VALUE' => '+7 999 000-11-11',
                        'VALUE_TYPE' => 'WORK'
                    ),
                    array(
                        'VALUE' => '+7 999 000-22-22',
                        'VALUE_TYPE' => 'WORK'
                    ),
                ),
                'EMAIL' => array(
                    array(
                        'VALUE' => 'test@test.org',
                        'VALUE_TYPE' => 'WORK'
                    ),
                    array(
                        'VALUE' => 'test2@test.org',
                        'VALUE_TYPE' => 'WORK'
                    ),
                    array(
                        'VALUE' => 'test3@test.org',
                        'VALUE_TYPE' => 'WORK'
                    )
                )
            )
        );
        $result = self::$wh->method('crm.contact.add', $data);
        self::$testContactId = (int)$result->result;
    }


    public static function tearDownAfterClass()
    {
        $result = self::$wh->method('crm.contact.delete', array('id' => self::$testContactId));
    }

    /**
     * Test search similars by phones
     */
    public function testSearchByPhone()
    {
        $cc = new CloneChecker(self::$wh);
        $result = $cc->searchByPhone(array('+7 999 000-00-00', '+7 999 000-22-22'));
        $this->assertContains(self::$testContactId, $result['+7 999 000-00-00']);
        $this->assertContains(self::$testContactId, $result['+7 999 000-22-22']);
    }


    /**
     * Test search similars by emails
     */
    public function testSearchByEmail()
    {
        $cc = new CloneChecker(self::$wh);
        $result = $cc->searchByEmail(array('test@test.org', 'test3@test.org'));
        $this->assertContains(self::$testContactId, $result['test@test.org']);
        $this->assertContains(self::$testContactId, $result['test3@test.org']);
    }


    /**
     * Test search similars by phones and emails
     */
    public function testSearch()
    {
        $cc = new CloneChecker(self::$wh);
        $result = $cc->search(array('+7 999 000-00-00', '+7 999 000-22-22'), array('test@test.org', 'test3@test.org'));
        $this->assertContains(self::$testContactId, $result['phone']['+7 999 000-00-00']);
        $this->assertContains(self::$testContactId, $result['phone']['+7 999 000-22-22']);
        $this->assertContains(self::$testContactId, $result['email']['test@test.org']);
        $this->assertContains(self::$testContactId, $result['email']['test3@test.org']);
    }
}
