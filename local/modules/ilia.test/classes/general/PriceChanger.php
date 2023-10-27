<?php

namespace IliaModule;

use Bitrix\Main,
    Bitrix\Main\Localization\Loc;


class PriceChanger
{

    public static function GetCatalogs()
    {
        \Bitrix\Main\Loader::includeModule('catalog');
        $data = \Bitrix\Catalog\CatalogIblockTable::getList([
        ])->fetchAll();

        foreach ($data as $key => $iblock) {
            $res = \CIBlock::GetByID($iblock["IBLOCK_ID"]);
            if ($ar_res = $res->GetNext()) {
                $data[$key]["NAME"] = $ar_res["NAME"];
            }
        }

        $data = self::GetCatalogsSections($data);

        return $data;
    }

    public static function GetCatalogsSections($arCatalogs)
    {

        foreach ($arCatalogs as $key => $IBLOCK) {

            $arCatalogs[$key]["SECTIONS"] = self::getSectionList(
                array(
                    'IBLOCK_ID' => $IBLOCK["IBLOCK_ID"]
                ),
                array(
                    'NAME',
                    'SECTION_PAGE_URL'
                )
            );

        }

        return $arCatalogs;
    }


    public static function getSectionList($filter, $select)
    {
        $dbSection = \CIBlockSection::GetList(
            array(
                'LEFT_MARGIN' => 'ASC',
            ),
            array_merge(
                array(
                    'ACTIVE' => 'Y',
                    'GLOBAL_ACTIVE' => 'Y'
                ),
                is_array($filter) ? $filter : array()
            ),
            false,
            array_merge(
                array(
                    'ID',
                    'IBLOCK_SECTION_ID'
                ),
                is_array($select) ? $select : array()
            )
        );
        while ($arSection = $dbSection->GetNext(true, false)) {
            $SID = $arSection['ID'];
            $PSID = (int)$arSection['IBLOCK_SECTION_ID'];
            $arLincs[$PSID]['CHILDS'][$SID] = $arSection;
            $arLincs[$SID] = &$arLincs[$PSID]['CHILDS'][$SID];
        }

        if (!empty($arLincs)) {
            return array_shift($arLincs);
        } else {
            return array();
        }

    }

    public static function showMenu($arsect, $glub = 0)
    {
        if (!empty($arsect["CHILDS"])) {
            foreach ($arsect["CHILDS"] as $CHILD) {

                $i = 0;
                $dots = '';
                while ($i < $glub) {
                    $dots .= '.';
                    $i++;
                }

                echo "<option iblock='" . $CHILD["IBLOCK_ID"] . "' value='" . $CHILD["ID"] . "'>" . $dots . " " . $CHILD["NAME"] . "</option>";
                self::showMenu($CHILD, $glub + 1);
            }
        } else {
            return false;
        }
    }

    public static function setPricesToSection($POST)
    {
        \CModule::IncludeModule('iblock');
        \CModule::IncludeModule('catalog');

        global $USER;

        $arTovars = [];
        $arNeedSkys = [];

        $arSelect = array("ID", "NAME", 'CATALOG_TYPE');
        $arFilter = array("IBLOCK_ID" => IntVal($POST["IBLOCK_ID"]), "SECTION_ID" => $POST["SECTION_ID"], 'INCLUDE_SUBSECTIONS' => 'Y');
        $res = \CIBlockElement::GetList(array(), $arFilter, false, array("nTopCount" => 99999), $arSelect); // сюда для пагинации параметр из поста , потом цикли запос в аяксе пока ответ не sucsess
        while ($ob = $res->GetNextElement()) {                                                                               // сейчас долго делать, да и нет в тз пошаговости. Там есть еще очередь.
            $arFields = $ob->GetFields();

            if ($arFields["CATALOG_TYPE"] == "1") {
                $arTovars[] = $arFields;
            } elseif ($arFields["CATALOG_TYPE"] == "3") {
                $arNeedSkys[] = $arFields["ID"];
            }

        }

        $arSkys = \CCatalogSKU::getOffersList(
            $arNeedSkys,
            $iblockID = 0,
            $skuFilter = array(),
            $fields = array('*'),
            $propertyFilter = array()
        );

        foreach ($arSkys as $arSky) {
            foreach ($arSky as $item) {
                $arTovars[] = $item;
            }
        }

        $Persent=(int)$POST["PERSENT"]*0.01;

        foreach ($arTovars as $arItem) {
            $arPrice = \CCatalogProduct::GetOptimalPrice($arItem["ID"], 1, $USER->GetUserGroupArray(), [])["RESULT_PRICE"];
            $newPrice=(int)$arPrice['BASE_PRICE']+(int)$arPrice['BASE_PRICE']*$Persent;
            $result = \Bitrix\Catalog\PriceTable::update($arPrice["ID"], ['PRICE' => $newPrice]);
            if ($result->isSuccess()) {
                continue;
            } else {
                $arAnswer['ANSWER'] = $result->getErrorMessages();
                continue;
            }
        }

        $arAnswer["ELEMENT_COUNT"]=count($arTovars);

        if (empty($arAnswer["ERROR"])) {
            $arAnswer["SUCSESS"] = 'Y';
            $arAnswer["ANSWER"] = 'Элементы обновлены!';
        }

        return $arAnswer;

    }


}
