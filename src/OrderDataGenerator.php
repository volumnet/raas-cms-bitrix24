<?php
/**
 * RAAS CMS to Bitrix24 data generator for orders
 * @author Alex V. Surnin <info@volumnet.ru>
 * @copyright Volume Networks, 2018
 */
namespace RAAS\CMS\Bitrix24;

use RAAS\CMS\Shop\Order;
use RAAS\CMS\Material;
use Exception;

/**
 * RAAS CMS to Bitrix24 data generator for orders class
 */
class OrderDataGenerator extends FeedbackDataGenerator
{
    /**
     * Item to generate data about
     * @param Order $item [description]
     * @param CloneChecker|null $cloneChecker Clone Checker, if necessary
     */
    public function __construct(Order $item, CloneChecker $cloneChecker = null)
    {
        parent::__construct($item, $cloneChecker);
    }


    /**
     * Returns lead title
     * @return string
     */
    public function getTitle()
    {
        return 'Заказ #' . (int)$this->item->id . ' с сайта ' . $_SERVER['HTTP_HOST'] . '';
    }


    /**
     * Returns cart name
     * @return string
     */
    public function getCartName()
    {
        return $this->item->parent->name;
    }


    /**
     * Returns order status name
     * @return string
     */
    public function getOrderStatus()
    {
        return $this->item->status->id ? $this->item->status->name : 'Новый';
    }


    /**
     * Returns payment status
     * @return string
     */
    public function getPaymentStatus()
    {
        return $this->item->paid ? 'Оплачен' : 'Не оплачен';
    }


    /**
     * Returns the entry for the ordered item
     * @param Material $item Ordered item
     * @return string
     */
    public function getItemRow(Material $item)
    {
        return $item->amount . ' x ' .
               '<a href="http' . ($_SERVER['HTTPS'] == 'on' ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . $item->url . '" target="_blank">' .
                    htmlspecialchars($item->name) .
               '</a>';
    }


    /**
     * Returns text for the ordered items
     * @return string
     */
    public function getGoods()
    {
        $temp = array();
        foreach ($this->item->items as $row) {
            $temp[] = $this->getItemRow($row);
        }
        return $temp ? ('<p>' . implode('<br />' . "\n", $temp) . '</p>' . "\n\n") : '';
    }


    /**
     * Returns link to view order
     * @return string
     */
    public function getLink()
    {
        return 'http' . ($_SERVER['HTTPS'] == 'on' ? 's' : '') . '://' .
               $_SERVER['HTTP_HOST'] .
               '/admin/?p=cms&m=shop&sub=&action=view&id=' . (int)$this->item->id;
    }


    /**
     * Returns suffix text
     * @return string
     */
    public function getSuffixText()
    {
        $temp = array();

        if ($date = $this->getPostDate()) {
            $temp[] = '<strong>Дата отправки:</strong> ' . $date;
        }
        if ($orderStatus = $this->getOrderStatus()) {
            $temp[] = '<strong>Статус заказа:</strong> ' . $orderStatus;
        }
        if ($paymentStatus = $this->getPaymentStatus()) {
            $temp[] = '<strong>Статус оплаты:</strong> ' . $paymentStatus;
        }
        if ($cartName = $this->getCartName()) {
            $temp[] = '<strong>Корзина:</strong> ' . $cartName;
        }
        if ($breadcrumbs = $this->getBreadcrumbs()) {
            $temp[] = '<strong>Страница:</strong> ' . $breadcrumbs;
        }
        if ($ip = $this->getIp()) {
            $temp[] = '<strong>IP-адрес:</strong> ' . $ip;
        }
        if ($userAgent = $this->getUserAgent()) {
            $temp[] = '<strong>User-Agent:</strong> ' . $userAgent;
        }
        if ($link = $this->getLink()) {
            $temp[] = '<strong><a href="' . ($link) . '" target="_blank">Просмотреть</a></strong>';
        }

        return '<p>' . implode('<br />' . "\n", $temp) . '</p>' . "\n\n";
    }


    /**
     * Returns comments text
     * @param array<string> $ignoredFields list of fields' keys to ignore in comments
     * @return string
     */
    protected function getComments(array $ignoredFields = array())
    {
        $temp = array();
        if ($comments = DataGenerator::getComments($ignoredFields)) {
            $temp[] = $comments;
        }
        if ($goodsText = $this->getGoods()) {
            $temp[] = $goodsText;
        }
        if ($suffix = $this->getSuffixText()) {
            $temp[] = $suffix;
        }
        if ($this->cloneChecker) {
            try {
                if ($similars = $this->cloneChecker->search($this->affectedPhones, $this->affectedEmails)) {
                    if ($similarsText = $this->getSimilarContactsText($similars)) {
                        $temp[] = $similarsText;
                    }
                }
            } catch (Exception $e) {
            }
        }
        $comments = implode('', $temp);
        return $comments;
    }
}
