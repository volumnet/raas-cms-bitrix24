# Bitrix24 default adapter for RAAS.CMS

## Installation

```
composer require volumnet/raas-cms-bitrix
```

## Usage

```
/**
 * @param string $domain Bitrix24 domain
 * @param string $webhook Webhook ID#
 * @param int $assignedById Bitrix24 user ID# to assign lead to
 */
$notifier = new Notifier('https://domain.bitrix24.ru', 'webhookId', 10);

/**
 * Notify about some item
 * @param SOME $item Item to notify about
 * @param boolean $reportAllFields true, if report all fields in comments; false if only not affected fields
 * @return array<mixed>|false Response from the server or false in the case of error (also will output in syslog)
 */
$result = $notifier->notify(new \RAAS\CMS\Feedback(), true);
$result2 = $notifier->notify(new \RAAS\CMS\Shop\Order(), false);
```
