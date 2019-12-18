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
 * RAAS CMS to Bitrix24 task data generator test
 */
class TaskDataGeneratorTest extends PHPUnit_Framework_TestCase
{

    protected static $dg;

    public static function setUpBeforeClass()
    {
        self::$dg = new FeedbackTaskDataGenerator(new Feedback(1));
    }


    /**
     * Tests spawn
     */
    public function testSpawn()
    {
        $result = TaskDataGenerator::spawn(new Feedback());
        $this->assertInstanceOf(FeedbackTaskDataGenerator::class, $result);

        $result = TaskDataGenerator::spawn(new Order());
        $this->assertInstanceOf(OrderTaskDataGenerator::class, $result);

        $result = TaskDataGenerator::spawn(new Material());
        $this->assertNull($result);
    }


    /**
     * Tests getData
     */
    public function testGetData()
    {
        $result = self::$dg->getData();
        $this->assertEquals('Заявка #1 с формы «Обратная связь» (localhost)', $result['fields']['TITLE']);
        $this->assertContains('Телефон: +7 999 000-00-00, +7 999 000-11-22', $result['fields']['DESCRIPTION']);
    }
}
