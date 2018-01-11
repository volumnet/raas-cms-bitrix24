<?php
namespace RAAS\CMS\Bitrix24;

use PHPUnit_Framework_TestCase;
use RAAS\General\Package as General;
use RAAS\Application;
use RAAS\CMS\Shop\Order;
use RAAS\CMS\Material;

/**
 * RAAS CMS to Bitrix24 data generator for orders test
 */
class OrderDataGeneratorTest extends PHPUnit_Framework_TestCase
{
    protected static $dg;

    public static function setUpBeforeClass()
    {
        ob_start();
        @General::i()->backupSQL();
        $sql = ob_get_clean();
        file_put_contents(__DIR__ . '/../../../../../backup-test.sql', $sql);
        $newSQL = file_get_contents(__DIR__ . '/../resources/test.sql');
        Application::i()->SQL->query($newSQL);
        require_once __DIR__ . '/../mocks/CloneCheckerMock.php';
        $cloneChecker = new CloneCheckerMock();
        self::$dg = new OrderDataGenerator(new Order(1), $cloneChecker);
    }


    public static function tearDownAfterClass()
    {
        $sql = file_get_contents(__DIR__ . '/../../../../../backup-test.sql');
        Application::i()->SQL->query($sql);
        unlink(__DIR__ . '/../../../../../backup-test.sql');
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
        $this->assertEquals('3 x Товар 2 (https://localhost/catalog/category1/category11/category111/tovar_2/)', $result);
    }


    /**
     * Tests getGoods
     */
    public function testGetGoods()
    {
        $result = self::$dg->getGoods();
        $this->assertEquals(
            "1 x Товар 2 (https://localhost/catalog/category1/category11/category111/tovar_2/)\n" .
            "2 x Товар 3 (https://localhost/catalog/category1/category11/category111/tovar_3/)\n" .
            "3 x Товар 4 (https://localhost/catalog/category1/category11/category111/tovar_4/)",
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
            "Дата отправки: 09.01.2018 18:08\n" .
            "Статус заказа: Новый\n" .
            "Статус оплаты: Не оплачен\n" .
            "Корзина: Корзина\n" .
            "Страница: Главная / Корзина\n" .
            "IP-адрес: 127.0.0.1\n" .
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36\n" .
            "Просмотреть: https://localhost/admin/?p=cms&m=shop&sub=&action=view&id=1",
            $result
        );
    }


    /**
     * Tests getComments
     */
    public function testGetData()
    {
        $suffixText = "\n"
                    . "1 x Товар 2 (https://localhost/catalog/category1/category11/category111/tovar_2/)\n"
                    . "2 x Товар 3 (https://localhost/catalog/category1/category11/category111/tovar_3/)\n"
                    . "3 x Товар 4 (https://localhost/catalog/category1/category11/category111/tovar_4/)\n"
                    . "\n"
                    . "Дата отправки: 09.01.2018 18:08\n"
                    . "Статус заказа: Новый\n"
                    . "Статус оплаты: Не оплачен\n"
                    . "Корзина: Корзина\n"
                    . "Страница: Главная / Корзина\n"
                    . "IP-адрес: 127.0.0.1\n"
                    . "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36\n"
                    . "Просмотреть: https://localhost/admin/?p=cms&m=shop&sub=&action=view&id=1\n"
                    . "\n"
                    . "НАЙДЕНЫ СОВПАДАЮЩИЕ КОНТАКТЫ:\n"
                    . "Телефон +7 999 000-00-00, e-mail test@test.org: #1, #2, #3\n"
                    . "Телефон +7 999 000-00-00: #1, #2, #3\n"
                    . "E-mail test@test.org: #1, #2, #3";

        $result = self::$dg->getData(false);
        $this->assertEquals(
            "Комментарий: Тестовый комментарий\n" .
            "Согласен(на) на обработку персональных данных: Да\n" .
            $suffixText,
            $result['fields']['COMMENTS']
        );
    }
}
