<?php
IncludeModuleLangFile(__FILE__);

if (class_exists("ilia_test")) return;

class ilia_test extends CModule
{
    var $MODULE_ID = "ilia.test";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_GROUP_RIGHTS = "Y";

    public function __construct()
    {
        $arModuleVersion = array();

        include(__DIR__ . '/version.php');

        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }

        $this->MODULE_NAME = GetMessage("MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("MODULE_DESCRIPTION");
    }

    function DoInstall()
    {
        global $APPLICATION;

        $FORM_RIGHT = $APPLICATION->GetGroupRight("ilia.test");
        if ($FORM_RIGHT >= "W") {
            $this->InstallFiles();
            $this->InstallDB();
        }
    }

    function InstallDB()
    {
        global $APPLICATION, $DB, $errors;

        $errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"] . "/local/modules/ilia.test/install/db/mysql/install.sql");

        if (!empty($errors)) {
            $APPLICATION->ThrowException(implode("", $errors));
            return false;
        }

        RegisterModule("ilia.test");
        return true;
    }

    function InstallFiles()
    {
        if ($_ENV["COMPUTERNAME"] != 'BX') {
            CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/local/modules/ilia.test/install/admin", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin", true);
        }
        return true;
    }

    function InstallEvents()
    {
        return true;
    }

    function DoUninstall()
    {
        global $APPLICATION, $errors;

        $FORM_RIGHT = $APPLICATION->GetGroupRight("ilia.test");
        if ($FORM_RIGHT >= "W") {
            $errors = false;
            $this->UnInstallDB();
            $this->UnInstallFiles();
        }
    }


    function UnInstallDB($arParams = array())
    {
        global $APPLICATION, $DB, $errors;

        $errors = false;
        // delete whole base
        $errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"] . "/local/modules/ilia.test/install/db/mysql/uninstall.sql");

        if (!empty($errors)) {
            $APPLICATION->ThrowException(implode("", $errors));
            return false;
        }


        COption::RemoveOption("ilia.test");
        UnRegisterModule("ilia.test");

        return true;
    }

    function UnInstallFiles($arParams = array())
    {
        global $DB;

        // Delete files
        DeleteDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/form/install/admin/", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin");

        return true;
    }

    function UnInstallEvents()
    {
        return true;
    }

    function GetModuleRightList()
    {
        global $MESS;
        $arr = array(
            "reference_id" => array("D", "R", "W"),
            "reference" => array(
                "[D] " . GetMessage("MODULE_DENIED"),
                "[R] " . GetMessage("MODULE_OPENED"),
                "[W] " . GetMessage("MODULE_FULL"))
        );
        return $arr;
    }
}
