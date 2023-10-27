<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

use Bitrix\Main\Context;

if (!check_bitrix_sessid()) {
    die("ACCESS_DENIED");
}

$MODULE_ID = 'ilia.test';
CModule::IncludeModule($MODULE_ID);

$request = Context::getCurrent()->getRequest();
if ($flag = $request->isAjaxRequest()) {
    $postValues = $request->getPostList()->toArray();

    if (empty($postValues["IBLOCK_ID"])) {
        $arAnswer["SUCSESS"] = 'N';
        $arAnswer["ANSWER"] = 'Выберите инфоблок!';
    } elseif (empty($postValues["SECTION_ID"])) {
        $arAnswer["SUCSESS"] = 'N';
        $arAnswer["ANSWER"] = 'Выберите раздел!';
    } elseif (empty($postValues["PERSENT"])) {
        $arAnswer["SUCSESS"] = 'N';
        $arAnswer["ANSWER"] = 'Введите процент';
    }elseif ((int)$postValues["PERSENT"] > 99 || (int)$postValues["PERSENT"] < 1) {
        $arAnswer["SUCSESS"] = 'N';
        $arAnswer["ANSWER"] = 'Процент может быть от 1 до 99';
    }

    if (empty($arAnswer["SUCSESS"])) {
        $arAnswer = IliaModule\PriceChanger::setPricesToSection($postValues);
    }

    $objDateTime = new DateTime();
    $now=$objDateTime->format("Y-m-d H:i:s");

    $result=IliaModule\IliaLogtable::add(["TIMESTAMP_X"=>$now,"SECTION_ID"=>$postValues["SECTION_ID"],"STATUS"=>$arAnswer["SUCSESS"],"ELEMENTS_COUNT"=> $arAnswer["ELEMENT_COUNT"],"PERSENT"=> $postValues["PERSENT"]."%"]);

    die(json_encode($arAnswer));
}

