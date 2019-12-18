<?php
namespace RAAS\CMS\Bitrix24;

use PHPUnit_Framework_TestCase;
use RAAS\CMS\Feedback;
use ReflectionProperty;
use RAAS\General\Package as General;
use RAAS\Application;
use RAAS\CMS\Material;

/**
 * RAAS CMS to Bitrix24 adapter (notifier) for tasks test
 */
class TaskNotifierTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tests notify
     */
    public function testNotify()
    {
        require_once __DIR__ . '/../mocks/WebhookMock.php';
        $n = new TaskNotifier('http://httpbin.org/post', '123', 33, [11, 22]);
        $p = new ReflectionProperty(TaskNotifier::class, 'auditorsIds');
        $p->setAccessible(true);
        $si = $p->getValue($n);
        $this->assertEquals([11, 22], $si);

        $m = new Material();
        $result = $n->notify($m, true);
        $this->assertFalse($result);

        $n = new TaskNotifier('http://httpbin.org/post', '123', 33, [11, 22]);
        $f = new Feedback(2);
        $result = $n->notify($f);
        $this->assertFalse($result);

        $wh = new WebhookMock('http://httpbin.org/post', '123');
        $p = new ReflectionProperty(TaskNotifier::class, 'webhook');
        $p->setAccessible(true);
        $p->setValue($n, $wh);
        $result = $n->notify($f);
        $this->assertEquals(33, $result->form->{'fields[RESPONSIBLE_ID]'});
        $this->assertEquals(11, $result->form->{'fields[AUDITORS][0]'});
        $this->assertEquals(22, $result->form->{'fields[AUDITORS][1]'});
        $this->assertRegExp('/Имя: Пользователь/', $result->form->{'fields[DESCRIPTION]'});
    }
}
