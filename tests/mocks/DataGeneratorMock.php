<?php
namespace RAAS\CMS\Bitrix24;

class DataGeneratorMock extends DataGenerator
{
    /**
     * Returns lead title
     * @return string
     */
    public function getTitle()
    {
        return 'Test';
    }


    /**
     * Gets the array of instant messengers in Bitrix24 format (dummy, for inheritance)
     * @return array<array('VALUE' => string, 'VALUE_TYPE' => 'WORK')>
     */
    public function getIMs()
    {
        return array(
            array(
                'VALUE' => '123456789',
                'VALUE_TYPE' => 'WORK'
            )
        );
    }


    /**
     * Gets the array of web addresses in Bitrix24 format (dummy, for inheritance)
     * @return array<array('VALUE' => string, 'VALUE_TYPE' => 'WORK')>
     */
    public function getWebs()
    {
        return array(
            array(
                'VALUE' => 'http://test.org',
                'VALUE_TYPE' => 'WORK'
            )
        );
    }
}
