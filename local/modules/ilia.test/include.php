<?
global $DB, $MESS, $APPLICATION;
IncludeModuleLangFile(__FILE__);

$DBType = mb_strtolower($DB->type);

CModule::AddAutoloadClasses(
    "ilia.test",
    array(
        // compability classes
        "IliaModule\PriceChanger" => "classes/general/PriceChanger.php",
        "IliaModule\IliaLogtable" => "classes/sql/IliaLogtable.php",
        "IliaModule\IliaQueuetable" => "classes/sql/IliaQueuetable.php",

//		// event handlers
//		"CFormEventHandlers" => "events.php",
//		"Bitrix\\Form\\SenderEventHandler" => "lib/senderconnector.php",
    )
);

// set event handlers
//AddEventHandler('form', 'onAfterResultAdd', array('CFormEventHandlers', 'sendOnAfterResultStatusChange'));

