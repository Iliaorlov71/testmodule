<?php

namespace IliaModule;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\IntegerField;
use Bitrix\Main\Entity\StringField;
use Bitrix\Main\Entity\DatetimeField;

class IliaQueuetable extends DataManager
{

    public static function getFilePath()
    {
        return __FILE__;
    }

    public static function getTableName()
    {
        return 'ilia_queuetable';
    }

    public static function getMap()
    {
        return array(
            new IntegerField('ID', array(
                'autocomplete' => true,
                'primary' => true
            )),

            new DatetimeField('TIMESTAMP_X', array(
                'required' => true)),

            new StringField('SECTION_ID', array(
                'required' => true,
                'title' => "ID раздела",
            )),

            new StringField('START_COUNT', array(
                'required' => true,
                'title' => "ID раздела",
            )),
            new StringField('END_COUNT', array(
                'required' => true,
                'title' => "ID раздела",
            )),
        );
    }

}