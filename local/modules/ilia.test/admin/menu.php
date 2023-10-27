<?
IncludeModuleLangFile(__FILE__);
global $APPLICATION;

if ($APPLICATION->GetGroupRight("form") > "D") {
    $aMenu = array(
        "parent_menu" => "global_menu_settings",
        "section" => "ilia_test",
        "sort" => 100,
        "text" => GetMessage("MODULE_MENU_MAIN"),
        "title" => GetMessage("MODULE_MENU_MAIN"),
        "icon" => "menu_icon",
        "page_icon" => "page_icon",
        "module_id" => "ilia.test",
        "items_id" => "ilia_test",
        "items" => array(),
    );


    $aMenu["items"][] = array(
        "text" => 'Наценка',
        "url" => "ilia_test_settings.php?lang=" . LANGUAGE_ID,
        "more_url" => array(
            "/local/modules/ap.sort/install/admin/ilia_test_settings.php",
        ),
        "title" => 'Наценка'
    );

    return $aMenu;
}
return false;
?>
