<?php
namespace RAAS\CMS\Bitrix24;

use PHPUnit_Framework_TestCase;
use RAAS\General\Package as General;
use RAAS\Application;
use RAAS\CMS\Feedback;

/**
 * RAAS CMS to Bitrix24 data generator for feedbacks test
 */
class FeedbackDataGeneratorTest extends PHPUnit_Framework_TestCase
{
    protected static $dg;

    protected static $dg2;

    protected static $dg3;

    protected static $dg0;

    public static function setUpBeforeClass()
    {
        require_once __DIR__ . '/../mocks/CloneCheckerMock.php';
        $cloneChecker = new CloneCheckerMock();
        self::$dg = new FeedbackDataGenerator(new Feedback(2), $cloneChecker);
        self::$dg2 = new FeedbackDataGenerator(new Feedback(3));
        self::$dg3 = new FeedbackDataGenerator(new Feedback(4));
        self::$dg0 = new FeedbackDataGenerator(new Feedback());
    }


    /**
     * Tests getTitle
     */
    public function testGetTitle()
    {
        $result = self::$dg->getTitle();
        $this->assertEquals('Заявка #2 с формы «Обратная связь 2» (localhost)', $result);

        $result = self::$dg0->getTitle();
        $this->assertEquals('Заявка #0 с формы «» (localhost)', $result);
    }


    /**
     * Tests getPostDate
     */
    public function testGetPostDate()
    {
        $result = self::$dg->getPostDate();
        $this->assertEquals('09.01.2018 18:07', $result);
    }


    /**
     * Tests getFormName
     */
    public function testGetFormName()
    {
        $result = self::$dg->getFormName();
        $this->assertEquals('Обратная связь 2', $result);
    }


    /**
     * Tests getBreadcrumbsRaw
     */
    public function testGetBreadcrumbsRaw()
    {
        $result = self::$dg->getBreadcrumbsRaw();
        $this->assertEquals(array('Главная', 'О компании'), $result);

        $result = self::$dg2->getBreadcrumbsRaw();
        $this->assertEquals(array('Главная', 'Каталог продукции', 'Категория 1', 'Категория 11', 'Категория 111', 'Товар 2'), $result);
    }


    /**
     * Tests getBreadcrumbs
     */
    public function testGetBreadcrumbs()
    {
        $result = self::$dg->getBreadcrumbs();
        $this->assertEquals('Главная / О компании', $result);
    }


    /**
     * Tests getIp
     */
    public function testGetIp()
    {
        $result = self::$dg->getIp();
        $this->assertEquals('127.0.0.1', $result);
    }


    /**
     * Tests getUserAgent
     */
    public function testGetUserAgent()
    {
        $result = self::$dg->getUserAgent();
        $this->assertEquals('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36', $result);
    }


    /**
     * Tests getLink
     */
    public function testsGetLink()
    {
        $result = self::$dg->getLink();
        $this->assertEquals('https://localhost/admin/?p=cms&sub=feedback&action=view&id=2', $result);
    }


    /**
     * Tests getSuffixText
     */
    public function testGetSuffixText()
    {
        $result = self::$dg->getSuffixText();
        $this->assertEquals(
            "Дата отправки: 09.01.2018 18:07\n" .
            "Форма: Обратная связь 2\n" .
            "Страница: Главная / О компании\n" .
            "IP-адрес: 127.0.0.1\n" .
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36\n" .
            "Просмотреть: https://localhost/admin/?p=cms&sub=feedback&action=view&id=2",
            $result
        );
    }


    /**
     * Tests getData
     */
    public function testGetData()
    {

        $suffixText = "\n"
                    . "Дата отправки: 09.01.2018 18:07\n"
                    . "Форма: Обратная связь 2\n"
                    . "Страница: Главная / О компании\n"
                    . "IP-адрес: 127.0.0.1\n"
                    . "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36\n"
                    . "Просмотреть: https://localhost/admin/?p=cms&sub=feedback&action=view&id=2\n"
                    . "\n"
                    . "НАЙДЕНЫ СОВПАДАЮЩИЕ КОНТАКТЫ:\n"
                    . "Телефон +7 999 000-00-00, e-mail test@test.org: #1, #2, #3\n"
                    . "Телефон +7 999 000-00-00, e-mail test2@test.org: #1, #2, #3\n"
                    . "Телефон +7 999 000-11-11, e-mail test@test.org: #1, #2, #3\n"
                    . "Телефон +7 999 000-11-11, e-mail test2@test.org: #1, #2, #3\n"
                    . "Телефон +7 999 000-22-22, e-mail test@test.org: #1, #2, #3\n"
                    . "Телефон +7 999 000-22-22, e-mail test2@test.org: #1, #2, #3\n"
                    . "Телефон +7 999 000-00-00: #1, #2, #3\n"
                    . "Телефон +7 999 000-11-11: #1, #2, #3\n"
                    . "Телефон +7 999 000-22-22: #1, #2, #3\n"
                    . "E-mail test@test.org: #1, #2, #3\n"
                    . "E-mail test2@test.org: #1, #2, #3";

        $result = self::$dg->getData(false);
        $this->assertEquals('WEB', $result['fields']['SOURCE_ID']);
        $this->assertEquals('Заявка #2 с формы «Обратная связь 2» (localhost)', $result['fields']['TITLE']);
        $this->assertEquals('Пользователь', $result['fields']['NAME']);
        $this->assertEquals('2006', $result['fields']['SECOND_NAME']);
        $this->assertEquals('Тестовый', $result['fields']['LAST_NAME']);
        $this->assertEquals('Тестовый адрес', $result['fields']['ADDRESS']);
        $this->assertEquals('Екатеринбург', $result['fields']['ADDRESS_CITY']);
        $this->assertEquals('Россия', $result['fields']['ADDRESS_COUNTRY']);
        $this->assertEquals('123456', $result['fields']['ADDRESS_POSTAL_CODE']);
        $this->assertEquals('Свердловская', $result['fields']['ADDRESS_REGION']);
        $this->assertEquals('1974-03-02', $result['fields']['BIRTHDAY']);
        $this->assertEquals('Тестовая компания', $result['fields']['COMPANY_TITLE']);
        $this->assertEquals('тестировщик', $result['fields']['POST']);
        $this->assertCount(2, $result['fields']['EMAIL']);
        $this->assertEquals('test@test.org', $result['fields']['EMAIL'][0]['VALUE']);
        $this->assertCount(3, $result['fields']['PHONE']);
        $this->assertEquals('+7 999 000-00-00', $result['fields']['PHONE'][0]['VALUE']);
        $this->assertNull($result['fields']['IM']);
        $this->assertNull($result['fields']['WEB']);

        $this->assertEquals(
            "Изображение: https://localhost/files/cms/common/arboretum_tree_rings_4.jpg\n" .
            "https://localhost/files/cms/common/brownleaves02899_4.jpg\n" .
            "Согласен(на) на обработку персональных данных: Да\n" .
            "Желаемое время заказа: 11.01.2018 10:00\n" .
            $suffixText,
            $result['fields']['COMMENTS']
        );

        $result = self::$dg->getData(true);
        $this->assertEquals(
            "Фамилия: Тестовый\n" .
            "Имя: Пользователь\n" .
            "Отчество: 2006\n" .
            "Страна: Россия\n" .
            "Индекс: 123456\n" .
            "Область: Свердловская\n" .
            "Дата рождения: 02.03.1974\n" .
            "Компания: Тестовая компания\n" .
            "Должность: тестировщик\n" .
            "E-mail: test@test.org, test2@test.org\n" .
            "Телефон: +7 999 000-00-00, +7 999 000-11-11; +7 999 000-22-22\n" .
            "Изображение: https://localhost/files/cms/common/arboretum_tree_rings_4.jpg\n" .
            "https://localhost/files/cms/common/brownleaves02899_4.jpg\n" .
            "Согласен(на) на обработку персональных данных: Да\n" .
            "Адрес: Тестовый адрес\n" .
            "Город: Екатеринбург\n" .
            "Желаемое время заказа: 11.01.2018 10:00\n" .
            $suffixText,
            $result['fields']['COMMENTS']
        );

        $result = self::$dg3->getData(true);
        $this->assertNotRegExp('/Фамилия/umis', $result['fields']['COMMENTS']);
        $this->assertNotRegExp('/Изображение/umis', $result['fields']['COMMENTS']);
    }
}
