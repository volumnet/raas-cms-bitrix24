<?php
/**
 * RAAS CMS to Bitrix24 data generator trait for feedbacks
 * @author Alex V. Surnin <info@volumnet.ru>
 * @copyright Volume Networks, 2019
 */
namespace RAAS\CMS\Bitrix24;

use Exception;

/**
 * RAAS CMS to Bitrix24 data generator trait for feedbacks class
 */
trait FeedbackDataTrait
{
    /**
     * Returns lead title
     * @return string
     */
    public function getTitle()
    {
        return 'Заявка #' . (int)$this->item->id . ' с формы «' . $this->getFormName() . '» (' . $_SERVER['HTTP_HOST'] . ')';
    }


    /**
     * Returns feedback post date
     * @return string date/time in format 'DD.MM.YYYY HH:mm'
     */
    public function getPostDate()
    {
        return date('d.m.Y H:i', strtotime($this->item->post_date));
    }


    /**
     * Returns form name
     * @return string
     */
    public function getFormName()
    {
        $formName = '';
        if ($this->item->pid) {
            $formName = $this->item->parent->name;
        }
        return $formName;
    }


    /**
     * Returns the breadcrumbs string for the parent page
     * @return string
     */
    public function getBreadcrumbs()
    {
        $breadcrumbs = array();
        if ($this->item->page->parents) {
            foreach ($this->item->page->parents as $row) {
                $breadcrumbs[] = '<a href="http'
                               .    ($_SERVER['HTTPS'] == 'on' ? 's' : '')
                               .    '://' . $_SERVER['HTTP_HOST']
                               .    $row->url
                               .    '" target="_blank">'
                               .    htmlspecialchars($row->name)
                               . '</a>';
            }
        }
        $breadcrumbs[] = '<a href="http'
                       .    ($_SERVER['HTTPS'] == 'on' ? 's' : '')
                       .    '://' . $_SERVER['HTTP_HOST']
                       .    $this->item->page->url
                       .    '" target="_blank">'
                       .    htmlspecialchars($this->item->page->name)
                       . '</a>';
        if ($this->item->material->id) {
            $breadcrumbs[] = '<a href="http'
                       .    ($_SERVER['HTTPS'] == 'on' ? 's' : '')
                       .    '://' . $_SERVER['HTTP_HOST']
                       .    $this->item->material->url
                       .    '" target="_blank">'
                       .    htmlspecialchars($this->item->material->name)
                       . '</a>';
        }
        return implode(' / ', $breadcrumbs);
    }


    /**
     * Returns the feedback IP-address
     * @return string
     */
    public function getIp()
    {
        return $this->item->ip;
    }


    /**
     * Returns the feedback User-Agent string
     * @return string
     */
    public function getUserAgent()
    {
        return $this->item->user_agent;
    }


    /**
     * Returns link to view feedback
     * @return string
     */
    public function getLink()
    {
        return 'http' . ($_SERVER['HTTPS'] == 'on' ? 's' : '') . '://' .
               $_SERVER['HTTP_HOST'] .
               '/admin/?p=cms&sub=feedback&action=view&id=' . (int)$this->item->id;
    }


    /**
     * Returns suffix text
     * @return string
     */
    public function getSuffixText()
    {
        $temp = array();

        if ($date = $this->getPostDate()) {
            $temp[] = 'Дата отправки: ' . $date;
        }
        if ($formName = $this->getFormName()) {
            $temp[] = 'Форма: ' . $formName;
        }
        if ($breadcrumbs = $this->getBreadcrumbs()) {
            $temp[] = 'Страница: ' . $breadcrumbs;
        }
        if ($ip = $this->getIp()) {
            $temp[] = 'IP-адрес: ' . $ip;
        }
        if ($userAgent = $this->getUserAgent()) {
            $temp[] =  'User-Agent: ' . $userAgent;
        }
        if ($link = $this->getLink()) {
            $temp[] =  '<a href="' . ($link) . '" target="_blank">Просмотреть</a>';
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
        if ($comments = parent::getComments($ignoredFields)) {
            $temp[] = $comments;
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
