<?php
namespace RAAS\CMS\Bitrix24;

use PHPUnit_Framework_TestCase;
use RAAS\General\Package as General;
use RAAS\Application;
use RAAS\CMS\Feedback;
use RAAS\CMS\Material;
use RAAS\CMS\Shop\Order;
use VolumNet\Bitrix24\Webhook;

/**
 * RAAS CMS to Bitrix24 data generator test
 */
class DataGeneratorTest extends PHPUnit_Framework_TestCase
{

    protected static $dg;

    protected static $dg2;

    protected static $dg3;

    protected static $dg0;

    public static function setUpBeforeClass()
    {
        ob_start();
        @General::i()->backupSQL();
        $sql = ob_get_clean();
        file_put_contents(__DIR__ . '/../../../../../backup-test.sql', $sql);
        $newSQL = file_get_contents(__DIR__ . '/../resources/test.sql');
        Application::i()->SQL->query($newSQL);
        self::$dg = new FeedbackDataGenerator(new Feedback(1));
        self::$dg2 = new FeedbackDataGenerator(new Feedback(2));
        self::$dg3 = new FeedbackDataGenerator(new Feedback(3));
        self::$dg0 = new FeedbackDataGenerator(new Feedback());
    }


    public static function tearDownAfterClass()
    {
        $sql = file_get_contents(__DIR__ . '/../../../../../backup-test.sql');
        Application::i()->SQL->query($sql);
        unlink(__DIR__ . '/../../../../../backup-test.sql');
    }


    /**
     * Tests parseFullName
     */
    public function testParseFullName()
    {
        $result = self::$dg->parseFullName('Иванов Иван Иванович', true);
        $this->assertEquals(array('Иван', 'Иванович', 'Иванов'), $result);

        $result = self::$dg->parseFullName('Иван Иванович Иванов', false);
        $this->assertEquals(array('Иван', 'Иванович', 'Иванов'), $result);

        $result = self::$dg->parseFullName('Иванов Иван', true);
        $this->assertEquals(array('Иван', '', 'Иванов'), $result);

        $result = self::$dg->parseFullName('Иван Иванов', false);
        $this->assertEquals(array('Иван', '', 'Иванов'), $result);

        $result = self::$dg->parseFullName('Иван', true);
        $this->assertEquals(array('Иван', '', ''), $result);

        $result = self::$dg->parseFullName('Иван', false);
        $this->assertEquals(array('Иван', '', ''), $result);
    }


    /**
     * Tests doRichFunction
     */
    public function testDoRichFunction()
    {
        $f = self::$dg2->doRichFunction('photos');
        $feedback = new Feedback(2);
        $val = $feedback->fields['photos']->getValue(0);
        $result = $f($val);
        $this->assertEquals('https://localhost/files/cms/common/arboretum_tree_rings_4.jpg', $result);

        $f = self::$dg2->doRichFunction('city');
        $feedback = new Feedback(2);
        $val = $feedback->fields['city']->getValue(0);
        $result = $f($val);
        $this->assertEquals('Екатеринбург', $result);

        $f = self::$dg2->doRichFunction('agree');
        $result = $f(1);
        $this->assertEquals('Да', $result);
        $result = $f(false);
        $this->assertEquals('Нет', $result);

        $f = self::$dg2->doRichFunction('wishtime');
        $result = $f('2018-01-10 22:33:44');
        $this->assertEquals('10.01.2018 22:33', $result);

        $f = self::$dg2->doRichFunction('aaa');
        $result = $f(' Иванов Иван Иванович ');
        $this->assertEquals('Иванов Иван Иванович', $result);
    }


    /**
     * Tests getMultipleValue
     */
    public function testGetMultipleValue()
    {
        $result = self::$dg2->getMultipleValue('phone');
        $this->assertEquals(array('+7 999 000-00-00', '+7 999 000-11-11; +7 999 000-22-22'), $result);
    }


    /**
     * Tests getSingleValue
     */
    public function testGetSingleValue()
    {
        $result = self::$dg2->getSingleValue('phone');
        $this->assertEquals('+7 999 000-00-00, +7 999 000-11-11; +7 999 000-22-22', $result);
    }


    /**
     * Tests getExplodedValue
     */
    public function testGetExplodedValue()
    {
        $result = self::$dg2->getExplodedValue('phone');
        $this->assertEquals(array('+7 999 000-00-00', '+7 999 000-11-11', '+7 999 000-22-22'), $result);
    }


    /**
     * Tests getFirstName
     */
    public function testGetFirstName()
    {
        $result = self::$dg2->getFirstName();
        $this->assertEquals('Пользователь', $result);

        $result = self::$dg->getFirstName();
        $this->assertEquals('Пользователь', $result);

        $result = self::$dg0->getFirstName();
        $this->assertEquals('', $result);
    }


    /**
     * Tests getSecondName
     */
    public function testGetSecondName()
    {
        $result = self::$dg2->getSecondName();
        $this->assertEquals('2006', $result);

        $result = self::$dg->getSecondName();
        $this->assertEquals('2003', $result);

        $result = self::$dg0->getSecondName();
        $this->assertEquals('', $result);
    }


    /**
     * Tests getLastName
     */
    public function testGetLastName()
    {
        $result = self::$dg2->getLastName();
        $this->assertEquals('Тестовый', $result);

        $result = self::$dg->getLastName();
        $this->assertEquals('Тестовый', $result);

        $result = self::$dg0->getLastName();
        $this->assertEquals('', $result);
    }


    /**
     * Tests getAddress
     */
    public function testGetAddress()
    {
        $result = self::$dg2->getAddress();
        $this->assertEquals('Тестовый адрес', $result);

        $result = self::$dg0->getAddress();
        $this->assertEquals('', $result);
    }


    /**
     * Tests getCity
     */
    public function testGetCity()
    {
        $result = self::$dg2->getCity();
        $this->assertEquals('Екатеринбург', $result);

        $result = self::$dg0->getCity();
        $this->assertEquals('', $result);
    }


    /**
     * Tests getCountry
     */
    public function testGetCountry()
    {
        $result = self::$dg2->getCountry();
        $this->assertEquals('Россия', $result);

        $result = self::$dg0->getCountry();
        $this->assertEquals('', $result);
    }


    /**
     * Tests getPostCode
     */
    public function testGetPostCode()
    {
        $result = self::$dg2->getPostCode();
        $this->assertEquals('123456', $result);

        $result = self::$dg0->getPostCode();
        $this->assertEquals('', $result);
    }


    /**
     * Tests getRegion
     */
    public function testGetRegion()
    {
        $result = self::$dg2->getRegion();
        $this->assertEquals('Свердловская', $result);

        $result = self::$dg0->getRegion();
        $this->assertEquals('', $result);
    }


    /**
     * Tests getBirthDate
     */
    public function testGetBirthDate()
    {
        $result = self::$dg2->getBirthDate();
        $this->assertEquals('1974-03-02', $result);

        $result = self::$dg0->getBirthDate();
        $this->assertEquals('', $result);
    }


    /**
     * Tests getCompanyTitle
     */
    public function testGetCompanyTitle()
    {
        $result = self::$dg2->getCompanyTitle();
        $this->assertEquals('Тестовая компания', $result);

        $result = self::$dg0->getCompanyTitle();
        $this->assertEquals('', $result);
    }


    /**
     * Tests getPost
     */
    public function testGetPost()
    {
        $result = self::$dg2->getPost();
        $this->assertEquals('тестировщик', $result);

        $result = self::$dg0->getPost();
        $this->assertEquals('', $result);
    }


    /**
     * Tests getStatusId
     */
    public function testGetStatusId()
    {
        $result = self::$dg2->getStatusId();
        $this->assertEquals('', $result);
    }


    /**
     * Tests getStatusDescription
     */
    public function testGetStatusDescription()
    {
        $result = self::$dg2->getStatusDescription();
        $this->assertEquals('', $result);
    }


    /**
     * Tests getStatusSemanticId
     */
    public function testGetStatusSemanticId()
    {
        $result = self::$dg2->getStatusSemanticId();
        $this->assertEquals('', $result);
    }


    /**
     * Tests getRawEmails
     */
    public function testGetRawEmails()
    {
        $result = self::$dg2->getRawEmails();
        $this->assertEquals(array('test@test.org', 'test2@test.org'), $result);

        $result = self::$dg0->getRawEmails();
        $this->assertEquals(array(), $result);
    }


    /**
     * Tests getEmails
     */
    public function testGetEmails()
    {
        $result = self::$dg2->getEmails();
        $this->assertEquals(
            array(
                array(
                    'VALUE' => 'test@test.org',
                    'VALUE_TYPE' => 'WORK'
                ),
                array(
                    'VALUE' => 'test2@test.org',
                    'VALUE_TYPE' => 'WORK'
                ),
            ),
            $result
        );

        $result = self::$dg0->getEmails();
        $this->assertEquals(array(), $result);
    }


    /**
     * Tests getRawPhones
     */
    public function testGetRawPhones()
    {
        $result = self::$dg2->getRawPhones();
        $this->assertEquals(array('+7 999 000-00-00', '+7 999 000-11-11', '+7 999 000-22-22'), $result);

        $result = self::$dg0->getRawPhones();
        $this->assertEquals(array(), $result);
    }


    /**
     * Tests getPhones
     */
    public function testGetPhones()
    {
        $result = self::$dg2->getPhones();
        $this->assertEquals(
            array(
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
            $result
        );

        $result = self::$dg3->getPhones();
        $this->assertEquals(
            array(
                array(
                    'VALUE' => '+7 999 000-00-00',
                    'VALUE_TYPE' => 'WORK'
                ),
            ),
            $result
        );

        $result = self::$dg0->getPhones();
        $this->assertEquals(array(), $result);
    }


    /**
     * Tests getIMs
     */
    public function testGetIMs()
    {
        $result = self::$dg2->getIMs();
        $this->assertEquals(array(), $result);
    }


    /**
     * Tests getWebs
     */
    public function testGetWebs()
    {
        $result = self::$dg2->getWebs();
        $this->assertEquals(array(), $result);
    }


    /**
     * Tests getSimilarContactsText
     */
    public function testGetSimilarContactsText()
    {
        $src = array(
            'phone' => array(
                '+7 999 000-00-00' => array(1, 2, 3),
                '+7 999 000-11-22' => array(2, 3, 4),
            ),
            'email' => array(
                'test@test.org' => array(3, 4, 5),
                'test2@test.org' => array(4, 5, 6),
            )
        );
        $result = self::$dg2->getSimilarContactsText($src);
        $this->assertEquals(
            "НАЙДЕНЫ СОВПАДАЮЩИЕ КОНТАКТЫ:\n" .
            "Телефон +7 999 000-00-00, e-mail test@test.org: #3\n" .
            "Телефон +7 999 000-11-22, e-mail test@test.org: #3, #4\n" .
            "Телефон +7 999 000-11-22, e-mail test2@test.org: #4\n" .
            "Телефон +7 999 000-00-00: #1, #2, #3\n" .
            "Телефон +7 999 000-11-22: #2, #3, #4\n" .
            "E-mail test@test.org: #3, #4, #5\n" .
            "E-mail test2@test.org: #4, #5, #6",
            $result
        );

        $result = self::$dg2->getSimilarContactsText(array());
        $this->assertEquals('', $result);
    }


    /**
     * Tests spawn
     */
    public function testSpawn()
    {
        $result = DataGenerator::spawn(new Feedback());
        $this->assertTrue($result instanceof FeedbackDataGenerator);

        $result = DataGenerator::spawn(new Order());
        $this->assertTrue($result instanceof OrderDataGenerator);

        $result = DataGenerator::spawn(new Material());
        $this->assertNull($result);
    }


    /**
     * Tests getData
     */
    public function testGetData()
    {
        require_once __DIR__ . '/../mocks/DataGeneratorMock.php';
        $dg = new DataGeneratorMock(new Feedback());
        $result = $dg->getData();
        $this->assertEquals('123456789', $result['fields']['IM'][0]['VALUE']);
        $this->assertEquals('http://test.org', $result['fields']['WEB'][0]['VALUE']);
    }
}
