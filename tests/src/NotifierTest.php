<?php
namespace RAAS\CMS\Bitrix24;

use PHPUnit_Framework_TestCase;
use RAAS\CMS\Feedback;
use ReflectionProperty;
use RAAS\General\Package as General;
use RAAS\Application;
use RAAS\CMS\Material;

/**
 * RAAS CMS to Bitrix24 adapter (notifier) test
 */
class NotifierTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tests notify
     */
    public function testNotify()
    {
        require_once __DIR__ . '/../mocks/WebhookMock.php';
        $n = new Notifier('http://httpbin.org/post', '123', 33);

        $m = new Material();
        $result = $n->notify($m, true);
        $this->assertFalse($result);

        $n = new Notifier('http://httpbin.org/post', '123', 33);
        $f = new Feedback(2);
        $result = $n->notify($f, true);
        $this->assertFalse($result);

        $wh = new WebhookMock('http://httpbin.org/post', '123');
        $p = new ReflectionProperty(Notifier::class, 'webhook');
        $p->setAccessible(true);
        $p->setValue($n, $wh);
        $result = $n->notify($f, true);
        $this->assertEquals(33, $result->form->{'fields[ASSIGNED_BY_ID]'});
        $this->assertEquals('WEB', $result->form->{'fields[SOURCE_ID]'});
        $this->assertRegExp('/Имя: Пользователь/', $result->form->{'fields[COMMENTS]'});

        $result = $n->notify($f, false);
        $this->assertEquals(33, $result->form->{'fields[ASSIGNED_BY_ID]'});
        $this->assertEquals('WEB', $result->form->{'fields[SOURCE_ID]'});
        $this->assertNotRegExp('/Имя: Пользователь/', $result->form->{'fields[COMMENTS]'});
    }
}
