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
     * Tests getBreadcrumbs
     */
    public function testGetBreadcrumbs()
    {
        $result = self::$dg->getBreadcrumbs();
        $this->assertEquals('<a href="https://localhost/" target="_blank">Главная</a> / <a href="https://localhost/about/" target="_blank">О компании</a>', $result);

        $result = self::$dg2->getBreadcrumbs();
        $this->assertEquals(
            '<a href="https://localhost/" target="_blank">Главная</a> / ' .
            '<a href="https://localhost/catalog/" target="_blank">Каталог продукции</a> / ' .
            '<a href="https://localhost/catalog/category1/" target="_blank">Категория 1</a> / ' .
            '<a href="https://localhost/catalog/category1/category11/" target="_blank">Категория 11</a> / ' .
            '<a href="https://localhost/catalog/category1/category11/category111/" target="_blank">Категория 111</a> / ' .
            '<a href="https://localhost/catalog/category1/category11/category111/tovar_2/" target="_blank">Товар 2</a>',
            $result
        );
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
            '<p><strong>Дата отправки:</strong> 09.01.2018 18:07<br />' . "\n" .
            '<strong>Форма:</strong> Обратная связь 2<br />' . "\n" .
            '<strong>Страница:</strong> <a href="https://localhost/" target="_blank">Главная</a> / <a href="https://localhost/about/" target="_blank">О компании</a><br />' . "\n" .
            '<strong>IP-адрес:</strong> 127.0.0.1<br />' . "\n" .
            '<strong>User-Agent:</strong> Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36<br />' . "\n" .
            '<strong><a href="https://localhost/admin/?p=cms&sub=feedback&action=view&id=2" target="_blank">Просмотреть</a></strong></p>' . "\n\n",
            $result
        );
    }


    /**
     * Tests getData
     */
    public function testGetData()
    {

        $suffixText = '<p>'
                    . '<strong>Дата отправки:</strong> 09.01.2018 18:07<br />' . "\n"
                    . '<strong>Форма:</strong> Обратная связь 2<br />' . "\n"
                    . '<strong>Страница:</strong> <a href="https://localhost/" target="_blank">Главная</a> / <a href="https://localhost/about/" target="_blank">О компании</a><br />' . "\n"
                    . '<strong>IP-адрес:</strong> 127.0.0.1<br />' . "\n"
                    . '<strong>User-Agent:</strong> Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36<br />' . "\n"
                    . '<strong><a href="https://localhost/admin/?p=cms&sub=feedback&action=view&id=2" target="_blank">Просмотреть</a></strong></p>' . "\n\n"
                    . '<p><strong>НАЙДЕНЫ СОВПАДАЮЩИЕ КОНТАКТЫ:</strong><br />' . "\n"
                    . 'Телефон +7 999 000-00-00, e-mail test@test.org: <a href="/crm/contact/details/1/" target="_blank">#1</a>, <a href="/crm/contact/details/2/" target="_blank">#2</a>, <a href="/crm/contact/details/3/" target="_blank">#3</a><br />' . "\n"
                    . 'Телефон +7 999 000-00-00, e-mail test2@test.org: <a href="/crm/contact/details/1/" target="_blank">#1</a>, <a href="/crm/contact/details/2/" target="_blank">#2</a>, <a href="/crm/contact/details/3/" target="_blank">#3</a><br />' . "\n"
                    . 'Телефон +7 999 000-11-11, e-mail test@test.org: <a href="/crm/contact/details/1/" target="_blank">#1</a>, <a href="/crm/contact/details/2/" target="_blank">#2</a>, <a href="/crm/contact/details/3/" target="_blank">#3</a><br />' . "\n"
                    . 'Телефон +7 999 000-11-11, e-mail test2@test.org: <a href="/crm/contact/details/1/" target="_blank">#1</a>, <a href="/crm/contact/details/2/" target="_blank">#2</a>, <a href="/crm/contact/details/3/" target="_blank">#3</a><br />' . "\n"
                    . 'Телефон +7 999 000-22-22, e-mail test@test.org: <a href="/crm/contact/details/1/" target="_blank">#1</a>, <a href="/crm/contact/details/2/" target="_blank">#2</a>, <a href="/crm/contact/details/3/" target="_blank">#3</a><br />' . "\n"
                    . 'Телефон +7 999 000-22-22, e-mail test2@test.org: <a href="/crm/contact/details/1/" target="_blank">#1</a>, <a href="/crm/contact/details/2/" target="_blank">#2</a>, <a href="/crm/contact/details/3/" target="_blank">#3</a><br />' . "\n"
                    . 'Телефон +7 999 000-00-00: <a href="/crm/contact/details/1/" target="_blank">#1</a>, <a href="/crm/contact/details/2/" target="_blank">#2</a>, <a href="/crm/contact/details/3/" target="_blank">#3</a><br />' . "\n"
                    . 'Телефон +7 999 000-11-11: <a href="/crm/contact/details/1/" target="_blank">#1</a>, <a href="/crm/contact/details/2/" target="_blank">#2</a>, <a href="/crm/contact/details/3/" target="_blank">#3</a><br />' . "\n"
                    . 'Телефон +7 999 000-22-22: <a href="/crm/contact/details/1/" target="_blank">#1</a>, <a href="/crm/contact/details/2/" target="_blank">#2</a>, <a href="/crm/contact/details/3/" target="_blank">#3</a><br />' . "\n"
                    . 'E-mail test@test.org: <a href="/crm/contact/details/1/" target="_blank">#1</a>, <a href="/crm/contact/details/2/" target="_blank">#2</a>, <a href="/crm/contact/details/3/" target="_blank">#3</a><br />' . "\n"
                    . 'E-mail test2@test.org: <a href="/crm/contact/details/1/" target="_blank">#1</a>, <a href="/crm/contact/details/2/" target="_blank">#2</a>, <a href="/crm/contact/details/3/" target="_blank">#3</a></p>' . "\n\n";

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
            '<p><strong>Изображение:</strong> <a href="https://localhost/files/cms/common/arboretum_tree_rings_4.jpg" target="_blank">arboretum_tree_rings_4.jpg</a>, <a href="https://localhost/files/cms/common/brownleaves02899_4.jpg" target="_blank">brownleaves02899_4.jpg</a><br />' . "\n" .
            '<strong>Согласен(на) на обработку персональных данных:</strong> Да<br />' . "\n" .
            '<strong>Желаемое время заказа:</strong> 11.01.2018 10:00</p>' . "\n\n" .
            $suffixText,
            $result['fields']['COMMENTS']
        );

        $result = self::$dg->getData(true);
        $this->assertEquals(
            '<p><strong>Фамилия:</strong> Тестовый<br />' . "\n" .
            '<strong>Имя:</strong> Пользователь<br />' . "\n" .
            '<strong>Отчество:</strong> 2006<br />' . "\n" .
            '<strong>Страна:</strong> Россия<br />' . "\n" .
            '<strong>Индекс:</strong> 123456<br />' . "\n" .
            '<strong>Область:</strong> Свердловская<br />' . "\n" .
            '<strong>Дата рождения:</strong> 02.03.1974<br />' . "\n" .
            '<strong>Компания:</strong> Тестовая компания<br />' . "\n" .
            '<strong>Должность:</strong> тестировщик<br />' . "\n" .
            '<strong>E-mail:</strong> <a href="mailto:test@test.org">test@test.org</a>, <a href="mailto:test2@test.org">test2@test.org</a><br />' . "\n" .
            '<strong>Телефон:</strong> +7 999 000-00-00, +7 999 000-11-11; +7 999 000-22-22<br />' . "\n" .
            '<strong>Изображение:</strong> <a href="https://localhost/files/cms/common/arboretum_tree_rings_4.jpg" target="_blank">arboretum_tree_rings_4.jpg</a>, <a href="https://localhost/files/cms/common/brownleaves02899_4.jpg" target="_blank">brownleaves02899_4.jpg</a><br />' . "\n" .
            '<strong>Согласен(на) на обработку персональных данных:</strong> Да<br />' . "\n" .
            '<strong>Адрес:</strong> Тестовый адрес<br />' . "\n" .
            '<strong>Город:</strong> Екатеринбург<br />' . "\n" .
            '<strong>Желаемое время заказа:</strong> 11.01.2018 10:00</p>' . "\n\n" .
            $suffixText,
            $result['fields']['COMMENTS']
        );

        $result = self::$dg3->getData(true);
        $this->assertNotRegExp('/Фамилия/umis', $result['fields']['COMMENTS']);
        $this->assertNotRegExp('/Изображение/umis', $result['fields']['COMMENTS']);
    }
}
