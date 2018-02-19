<?php
/**
 * RAAS CMS to Bitrix24 data generator
 * @author Alex V. Surnin <info@volumnet.ru>
 * @copyright Volume Networks, 2018
 */
namespace RAAS\CMS\Bitrix24;

use SOME\SOME;
use RAAS\Attachment;
use RAAS\CMS\Feedback;
use RAAS\CMS\Shop\Order;
use SOME\Text;

/**
 * RAAS CMS to Bitrix24 data generator class
 */
abstract class DataGenerator
{
    /**
     * Item to generate data about
     * @var SOME
     */
    protected $item;

    /**
     * Clone checker
     * @var CloneChecker
     */
    protected $cloneChecker;


    /**
     * Affected phones
     * @var array<string>
     */
    protected $affectedPhones = array();


    /**
     * Affected emails
     * @var array<string>
     */
    protected $affectedEmails = array();

    /**
     * Affected fields, not to use in the comments' field
     * @var array<string>
     */
    protected $affectedFields = array(
        'full_name', 'first_name', 'second_name', 'last_name', 'address', 'city',
        'country', 'post_code', 'region', 'district', 'birth_date', 'birthdate',
        'company', 'company_name', 'status', 'job_name', 'post', 'email', 'emails',
        'phone', 'phones', 'phone_call'
    );

    /**
     * Class constructor
     * @param SOME $item Item to generate data about
     * @param CloneChecker|null $cloneChecker Clone Checker, if necessary
     */
    public function __construct(SOME $item, CloneChecker $cloneChecker = null)
    {
        $this->item = $item;
        $this->cloneChecker = $cloneChecker;
    }


    /**
     * Returns the data about some item
     * @param boolean $reportAllFields true, if report all fields in comments; false if only not affected fields
     * @return array<mixed> Data compatible with Bitrix24 crm.lead.add method https://dev.1c-bitrix.ru/rest_help/crm/leads/crm_lead_add.php
     */
    public function getData($reportAllFields = true)
    {
        $result = array();
        $fields = array();

        if ($title = $this->getTitle()) {
            $fields['TITLE'] = $title;
        }
        if ($firstName = $this->getFirstName()) {
            $fields['NAME'] = $firstName;
        }
        if ($secondName = $this->getSecondName()) {
            $fields['SECOND_NAME'] = $secondName;
        }
        if ($lastName = $this->getLastName()) {
            $fields['LAST_NAME'] = $lastName;
        }
        if ($address = $this->getAddress()) {
            $fields['ADDRESS'] = $address;
        }
        if ($city = $this->getCity()) {
            $fields['ADDRESS_CITY'] = $city;
        }
        if ($country = $this->getCountry()) {
            $fields['ADDRESS_COUNTRY'] = $country;
        }
        if ($postCode = $this->getPostCode()) {
            $fields['ADDRESS_POSTAL_CODE'] = $postCode;
        }
        if ($region = $this->getRegion()) {
            $fields['ADDRESS_REGION'] = $region;
        }
        if ($birthDate = $this->getBirthDate()) {
            $fields['BIRTHDAY'] = $birthDate;
        }
        if ($companyTitle = $this->getCompanyTitle()) {
            $fields['COMPANY_TITLE'] = $companyTitle;
        }
        if ($post = $this->getPost()) {
            $fields['POST'] = $post;
        }

        if ($emails = $this->getEmails()) {
            $this->affectedEmails = $this->getRawEmails();
            $fields['EMAIL'] = $emails;
        }
        if ($phones = $this->getPhones()) {
            $this->affectedPhones = $this->getRawPhones();
            $fields['PHONE'] = $phones;
        }
        if ($ims = $this->getIMs()) {
            $fields['IM'] = $ims;
        }
        if ($webs = $this->getWebs()) {
            $fields['WEB'] = $webs;
        }

        if ($comments = $this->getComments($reportAllFields ? array() : $this->affectedFields)) {
            $fields['COMMENTS'] = $comments;
        }
        if ($fields) {
            $result['fields'] = $fields;
        }
        return $result;
    }


    /**
     * Returns lead title
     * @return string
     */
    abstract public function getTitle();


    /**
     * Parses full name string
     * @param string $fullName full name string
     * @param boolean $lastNameFirst true if the order is ('last name' 'first name' 'second name'), false if ('first name' 'second name' 'last name')
     * @return array(string $firstName, string $secondName, string $lastName)
     */
    public function parseFullName($fullName, $lastNameFirst = true)
    {
        $temp = explode(' ', $fullName);
        if (count($temp) >= 3) {
            if ($lastNameFirst) {
                list($lastName, $firstName, $secondName) = $temp;
            } else {
                list($firstName, $secondName, $lastName) = $temp;
            }
        } elseif (count($temp) == 2) {
            $secondName = '';
            if ($lastNameFirst) {
                list($lastName, $firstName) = $temp;
            } else {
                list($firstName, $lastName) = $temp;
            }
        } else {
            $firstName = $fullName;
        }
        return array(trim($firstName), trim($secondName), trim($lastName));
    }


    /**
     * Gets the first name
     * @return string
     */
    public function getFirstName()
    {
        if ($firstName = $this->getSingleValue('first_name')) {
            $firstName = trim($firstName);
        } elseif ($fullName = $this->getSingleValue('full_name')) {
            $firstName = trim($this->parseFullName($fullName)[0]);
        }
        return $firstName;
    }


    /**
     * Gets the second name
     * @return string
     */
    public function getSecondName()
    {
        if ($secondName = $this->getSingleValue('second_name')) {
            $secondName = trim($secondName);
        } elseif ($fullName = $this->getSingleValue('full_name')) {
            $secondName = trim($this->parseFullName($fullName)[1]);
        }
        return $secondName;
    }


    /**
     * Gets the last name
     * @return string
     */
    public function getLastName()
    {
        if ($lastName = $this->getSingleValue('last_name')) {
            $lastName = trim($lastName);
        } elseif ($fullName = $this->getSingleValue('full_name')) {
            $lastName = trim($this->parseFullName($fullName)[2]);
        }
        return $lastName;
    }


    /**
     * Gets the address
     * @return string
     */
    public function getAddress()
    {
        if ($address = $this->getSingleValue('address')) {
            $address = trim($address);
        }
        return $address;
    }


    /**
     * Gets the city name
     * @return string
     */
    public function getCity()
    {
        if ($city = $this->getSingleValue('city')) {
            $city = trim($city);
        }
        return $city;
    }


    /**
     * Gets the country name
     * @return string
     */
    public function getCountry()
    {
        if ($country = $this->getSingleValue('country')) {
            $country = trim($country);
        }
        return $country;
    }


    /**
     * Gets the postal code
     * @return string
     */
    public function getPostCode()
    {
        if ($postCode = $this->getSingleValue('post_code')) {
            $postCode = trim($postCode);
        }
        return $postCode;
    }


    /**
     * Gets the region name
     * @return string
     */
    public function getRegion()
    {
        foreach (array('region', 'district') as $key) {
            if ($region = $this->getSingleValue($key)) {
                return trim($region);
            }
        }
        return '';
    }


    /**
     * Gets birth date
     * @return string date in format of 'YYYY-MM-DD'
     */
    public function getBirthDate()
    {
        foreach (array('birth_date', 'birthdate') as $key) {
            if (isset($this->item->fields[$key])) {
                return trim($this->item->fields[$key]->getValue());
            }
        }
        return '';
    }


    /**
     * Gets the company title
     * @return string
     */
    public function getCompanyTitle()
    {
        foreach (array('company', 'company_name') as $key) {
            if ($companyName = $this->getSingleValue($key)) {
                return trim($companyName);
            }
        }
        return '';
    }


    /**
     * Gets the job post name
     * @return string
     */
    public function getPost()
    {
        foreach (array('status', 'job_name', 'post') as $key) {
            if ($status = $this->getSingleValue($key)) {
                return trim($status);
            }
        }
        return '';
    }


    /**
     * Gets status ID (dummy, for inheritance)
     * @return string
     */
    public function getStatusId()
    {
        return '';
    }


    /**
     * Gets status description (dummy, for inheritance)
     * @return string
     */
    public function getStatusDescription()
    {
        return '';
    }


    /**
     * Gets status semantic ID (dummy, for inheritance)
     * @return string
     */
    public function getStatusSemanticId()
    {
        return '';
    }


    /**
     * Gets the raw array of emails
     * @return array<string>
     */
    public function getRawEmails()
    {
        foreach (array('email', 'emails') as $key) {
            if ($emails = $this->getExplodedValue($key)) {
                return $emails;
            }
        }
        return array();
    }


    /**
     * Gets the array of emails in Bitrix24 format
     * @return array<array('VALUE' => string, 'VALUE_TYPE' => 'WORK')>
     */
    public function getEmails()
    {
        $rawEmails = $this->getRawEmails();
        $result = array();
        foreach ($rawEmails as $val) {
            $result[] = array('VALUE' => $val, 'VALUE_TYPE' => 'WORK');
        }
        return $result;
    }


    /**
     * Gets the raw array of phones
     * @return array<string>
     */
    public function getRawPhones()
    {
        foreach (array('phone', 'phones', 'phone_call') as $key) {
            if ($phones = $this->getExplodedValue($key)) {
                return $phones;
            }
        }
        return array();
    }


    /**
     * Gets the array of phones in Bitrix24 format
     * @return array<array('VALUE' => string, 'VALUE_TYPE' => 'WORK')>
     */
    public function getPhones()
    {
        $rawPhones = $this->getRawPhones();
        $result = array();
        foreach ($rawPhones as $val) {
            $result[] = array('VALUE' => $val, 'VALUE_TYPE' => 'WORK');
        }
        return $result;
    }


    /**
     * Gets the array of instant messengers in Bitrix24 format (dummy, for inheritance)
     * @return array<array('VALUE' => string, 'VALUE_TYPE' => 'WORK')>
     */
    public function getIMs()
    {
        return array();
    }


    /**
     * Gets the array of web addresses in Bitrix24 format (dummy, for inheritance)
     * @return array<array('VALUE' => string, 'VALUE_TYPE' => 'WORK')>
     */
    public function getWebs()
    {
        return array();
    }


    /**
     * Gets the comments text (fields that aren't listed previously)
     * @param array<string> $ignoredFields list of fields' keys to ignore in comments
     * @return string
     */
    protected function getComments(array $ignoredFields = array())
    {
        $temp = array();
        foreach ((array)$this->item->fields as $key => $field) {
            if (!in_array($key, $ignoredFields)) {
                switch ($field->datatype) {
                    case 'image':
                    case 'file':
                        $val = $this->getExplodedValue($key);
                        if ($val) {
                            $val = array_map(function ($x) {
                                return '<a href="' . htmlspecialchars($x) . '" target="_blank">' . htmlspecialchars(basename($x)) . '</a>';
                            }, $val);
                            $temp[] = '<strong>' . $field->name . ':</strong> ' . implode(', ', $val);
                        }
                        break;
                    case 'email':
                        $val = $this->getExplodedValue($key);
                        if ($val) {
                            $val = array_map(function ($x) {
                                return '<a href="mailto:' . htmlspecialchars($x) . '">' . htmlspecialchars($x) . '</a>';
                            }, $val);
                            $temp[] = '<strong>' . $field->name . ':</strong> ' . implode(', ', $val);
                        }
                        break;
                    default:
                        $val = $this->getSingleValue($key);
                        if (trim($val) !== '') {
                            $temp[] = '<strong>' . $field->name . ':</strong> ' . nl2br(htmlspecialchars($this->getSingleValue($key)));
                        }
                        break;
                }
            }
        }
        if ($temp) {
            $temp = '<p>' . implode('<br />' . "\n", $temp) . '</p>' . "\n\n";
        }
        return $temp;
    }


    /**
     * Returns function to use with array_map to doRich values
     * @param string $fieldName Item field name
     * @return callable
     */
    public function doRichFunction($fieldName)
    {
        if (isset($this->item->fields[$fieldName])) {
            $f = $this->item->fields[$fieldName];
            switch ($f->datatype) {
                case 'image':
                case 'file':
                    return function (Attachment $x) {
                        return 'http' . ($_SERVER['HTTPS'] == 'on' ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . '/' . $x->fileURL;
                    };
                    break;
                case 'date':
                    return function ($x) {
                        return date('d.m.Y', strtotime($x));
                    };
                    break;
                case 'datetime-local':
                case 'datetime':
                    return function ($x) {
                        return date('d.m.Y H:i', strtotime($x));
                    };
                    break;
                case 'checkbox':
                    return function ($x) {
                        return (bool)$x ? 'Да' : 'Нет';
                    };
                    break;
                default:
                    return array($f, 'doRich');
                    break;
            }
        }
        return 'trim';
    }


    /**
     * Gets values from field as array
     * @param string $fieldName Item field name
     * @return array<string>
     */
    public function getMultipleValue($fieldName)
    {
        $result = array();
        if (isset($this->item->fields[$fieldName])) {
            $result = (array)$this->item->fields[$fieldName]->getValues(true);
            $result = array_map($this->doRichFunction($fieldName), $result);
        }
        return $result;
    }


    /**
     * Gets values from field as single string
     * @param string $fieldName Item field name
     * @return string
     */
    public function getSingleValue($fieldName)
    {
        if (isset($this->item->fields[$fieldName])) {
            return implode(', ', $this->getMultipleValue($fieldName));
        }
        return '';
    }


    /**
     * Gets values from field as array, splitting single values with comma or semicolon
     * @param string $fieldName Item field name
     * @return array<string>
     */
    public function getExplodedValue($fieldName)
    {
        $result = array();
        if (isset($this->item->fields[$fieldName])) {
            $data = $this->getSingleValue($fieldName);
            $arr = preg_split('/,|;/umi', $data);
            foreach ($arr as $val) {
                if (trim($val)) {
                    $result[] = trim($val);
                }
            }
            $result = array_unique($result);
            $result = array_values($result);
        }
        return $result;
    }


    /**
     * Gets the string about similar contacts
     * @param array('phone' => array<[string] => array<int>>, 'email' => array<[string] => array<int>>) $similarContacts
     * @return string
     */
    public function getSimilarContactsText(array $similarContacts)
    {
        $temp = array();

        $both = array();
        if ($similarContacts['phone'] && $similarContacts['email']) {
            foreach ($similarContacts['phone'] as $phone => $similarsByPhone) {
                foreach ($similarContacts['email'] as $email => $similarsByEmail) {
                    $similarsByPhone = array_filter($similarsByPhone);
                    $similarsByEmail = array_filter($similarsByEmail);
                    $similarsByBoth = array_intersect($similarsByPhone, $similarsByEmail);
                    $similarsByBoth = array_map(
                        function ($x) {
                            return '<a href="/crm/contact/details/' . (int)$x . '/" target="_blank">#' . (int)$x . '</a>';
                        },
                        $similarsByBoth
                    );
                    if ($similarsByBoth) {
                        $temp[] = 'Телефон ' . $phone . ', e-mail ' . $email . ': ' . implode(', ', $similarsByBoth);
                    }
                }
            }
        }
        if ($similarContacts['phone']) {
            foreach ($similarContacts['phone'] as $phone => $similarsByPhone) {
                $similarsByPhone = array_filter($similarsByPhone);
                $similarsByPhone = array_map(
                    function ($x) {
                        return '<a href="/crm/contact/details/' . (int)$x . '/" target="_blank">#' . (int)$x . '</a>';
                    },
                    $similarsByPhone
                );
                if ($similarsByPhone) {
                    $temp[] = 'Телефон ' . $phone . ': ' . implode(', ', $similarsByPhone);
                }
            }
        }
        if ($similarContacts['email']) {
            foreach ($similarContacts['email'] as $email => $similarsByEmail) {
                $similarsByEmail = array_filter($similarsByEmail);
                $similarsByEmail = array_map(
                    function ($x) {
                        return '<a href="/crm/contact/details/' . (int)$x . '/" target="_blank">#' . (int)$x . '</a>';
                    },
                    $similarsByEmail
                );
                if ($similarsByEmail) {
                    $temp[] = 'E-mail ' . $email . ': ' . implode(', ', $similarsByEmail);
                }
            }
        }
        if ($temp) {
            return '<p><strong>НАЙДЕНЫ СОВПАДАЮЩИЕ КОНТАКТЫ:</strong><br />' . "\n" . implode('<br />' . "\n", $temp) . '</p>' . "\n\n";
        }
        return '';
    }


    /**
     * Returns data generator for the certain type of item
     * @param SOME $item Item to generate data for
     * @param CloneChecker|null $cloneChecker Clone Checker, if necessary
     * @return DataGenerator
     */
    public static function spawn(SOME $item, CloneChecker $cloneChecker = null)
    {
        if ($item instanceof Order) {
            return new OrderDataGenerator($item, $cloneChecker);
        } elseif ($item instanceof Feedback) {
            return new FeedbackDataGenerator($item, $cloneChecker);
        }
        return null;
    }
}
