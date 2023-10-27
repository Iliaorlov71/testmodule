<?php

namespace IliaModule;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\IntegerField;
use Bitrix\Main\Entity\StringField;
use Bitrix\Main\Entity\DatetimeField;

class IliaLogtable extends DataManager
{

    public static function getFilePath()
    {
        return __FILE__;
    }

    public static function getTableName()
    {
        return 'ilia_logtable';
    }

    public static function getMap()
    {
        return array(

            new IntegerField('ID', array(
                'autocomplete' => true,
                'primary' => true
            )),

            new StringField('TIMESTAMP_X', array(
                'required' => true)),

            new StringField('SECTION_ID', array(
                'required' => true,
                'title' => "ID раздела",
            )),

            new StringField('ELEMENTS_COUNT', array(
                'required' => true,
                'title' => "ID раздела",
            )),
            new StringField('PERSENT', array(
                'required' => true,
                'title' => "Процент",
            )),

            new StringField('STATUS', array(
                'required' => true,
                'title' => "ID раздела",
            )),

        );
    }


}