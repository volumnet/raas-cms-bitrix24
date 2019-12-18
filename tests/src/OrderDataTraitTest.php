<?php
namespace RAAS\CMS\Bitrix24;

use PHPUnit_Framework_TestCase;
use RAAS\General\Package as General;
use RAAS\Application;
use RAAS\CMS\Shop\Order;
use RAAS\CMS\Material;

/**
 * RAAS CMS to Bitrix24 data generator trait for orders test
 */
class OrderDataTraitTest extends PHPUnit_Framework_TestCase
{
    protected static $dg;

    public static function setUpBeforeClass()
    {
        require_once __DIR__ . '/../mocks/CloneCheckerMock.php';
        $cloneChecker = new CloneCheckerMock();
        self::$dg = new OrderDataGenerator(new Order(1), $cloneChecker);
    }


    /**
     * Tests getTitle
     */
    public function testGetTitle()
    {
        $result = self::$dg->getTitle();
        $this->assertEquals('Заказ #1 с сайта localhost', $result);
    }


    /**
     * Tests getCartName
     */
    public function testGetCartName()
    {
        $result = self::$dg->getCartName();
        $this->assertEquals('Корзина', $result);
    }


    /**
     * Tests getOrderStatus
     */
    public function testGetOrderStatus()
    {
        $result = self::$dg->getOrderStatus();
        $this->assertEquals('Новый', $result);
    }


    /**
     * Tests getPaymentStatus
     */
    public function testGetPaymentStatus()
    {
        $result = self::$dg->getPaymentStatus();
        $this->assertEquals('Не оплачен', $result);
    }


    /**
     * Tests getItemRow
     */
    public function testGetItemRow()
    {
        $m = new Material(11);
        $m->amount = 3;
        $result = self::$dg->getItemRow($m);
        $this->assertEquals('3 x <a href="https://localhost/catalog/category1/category11/category111/tovar_2/" target="_blank">Товар 2</a>', $result);
    }


    /**
     * Tests getGoods
     */
    public function testGetGoods()
    {
        $result = self::$dg->getGoods();
        $this->assertEquals(
            '<p>1 x <a href="https://localhost/catalog/category1/category11/category111/tovar_2/" target="_blank">Товар 2</a><br />' . "\n" .
            '2 x <a href="https://localhost/catalog/category1/category11/category111/tovar_3/" target="_blank">Товар 3</a><br />' . "\n" .
            '3 x <a href="https://localhost/catalog/category1/category11/category111/tovar_4/" target="_blank">Товар 4</a></p>' . "\n\n",
            $result
        );
    }


    /**
     * Tests getLink
     */
    public function testGetLink()
    {
        $result = self::$dg->getLink();
        $this->assertEquals('https://localhost/admin/?p=cms&m=shop&sub=&action=view&id=1', $result);
    }


    /**
     * Tests getSuffixText
     */
    public function testGetSuffixText()
    {
        $result = self::$dg->getSuffixText();
        $this->assertEquals(
            '<p><strong>Дата отправки:</strong> 09.01.2018 18:08<br />' . "\n" .
            '<strong>Статус заказа:</strong> Новый<br />' . "\n" .
            '<strong>Статус оплаты:</strong> Не оплачен<br />' . "\n" .
            '<strong>Корзина:</strong> Корзина<br />' . "\n" .
            '<strong>Страница:</strong> <a href="https://localhost/" target="_blank">Главная</a> / <a href="https://localhost/cart/" target="_blank">Корзина</a><br />' . "\n" .
            '<strong>IP-адрес:</strong> 127.0.0.1<br />' . "\n" .
            '<strong>User-Agent:</strong> Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36<br />' . "\n" .
            '<strong><a href="https://localhost/admin/?p=cms&m=shop&sub=&action=view&id=1" target="_blank">Просмотреть</a></strong></p>' . "\n\n",
            $result
        );
    }


    /**
     * Tests getComments
     */
    public function testGetData()
    {
        $suffixText = '<p>1 x <a href="https://localhost/catalog/category1/category11/category111/tovar_2/" target="_blank">Товар 2</a><br />' . "\n"
                    . '2 x <a href="https://localhost/catalog/category1/category11/category111/tovar_3/" target="_blank">Товар 3</a><br />' . "\n"
                    . '3 x <a href="https://localhost/catalog/category1/category11/category111/tovar_4/" target="_blank">Товар 4</a></p>' . "\n\n"
                    . '<p><strong>Дата отправки:</strong> 09.01.2018 18:08<br />' . "\n"
                    . '<strong>Статус заказа:</strong> Новый<br />' . "\n"
                    . '<strong>Статус оплаты:</strong> Не оплачен<br />' . "\n"
                    . '<strong>Корзина:</strong> Корзина<br />' . "\n"
                    . '<strong>Страница:</strong> <a href="https://localhost/" target="_blank">Главная</a> / <a href="https://localhost/cart/" target="_blank">Корзина</a><br />' . "\n"
                    . '<strong>IP-адрес:</strong> 127.0.0.1<br />' . "\n"
                    . '<strong>User-Agent:</strong> Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36<br />' . "\n"
                    . '<strong><a href="https://localhost/admin/?p=cms&m=shop&sub=&action=view&id=1" target="_blank">Просмотреть</a></strong></p>' . "\n\n"
                    . '<p><strong>НАЙДЕНЫ СОВПАДАЮЩИЕ КОНТАКТЫ:</strong><br />' . "\n"
                    . 'Телефон +7 999 000-00-00, e-mail test@test.org: <a href="/crm/contact/details/1/" target="_blank">#1</a>, <a href="/crm/contact/details/2/" target="_blank">#2</a>, <a href="/crm/contact/details/3/" target="_blank">#3</a><br />' . "\n"
                    . 'Телефон +7 999 000-00-00: <a href="/crm/contact/details/1/" target="_blank">#1</a>, <a href="/crm/contact/details/2/" target="_blank">#2</a>, <a href="/crm/contact/details/3/" target="_blank">#3</a><br />' . "\n"
                    . 'E-mail test@test.org: <a href="/crm/contact/details/1/" target="_blank">#1</a>, <a href="/crm/contact/details/2/" target="_blank">#2</a>, <a href="/crm/contact/details/3/" target="_blank">#3</a></p>' . "\n\n";

        $result = self::$dg->getData(false);
        $this->assertEquals(
            '<p><strong>Комментарий:</strong> Тестовый комментарий<br />' . "\n" .
            '<strong>Согласен(на) на обработку персональных данных:</strong> Да</p>' . "\n\n" .
            $suffixText,
            $result['fields']['COMMENTS']
        );
    }
}
