<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

use Bitrix\Main\Context;

CJSCore::Init(array("jquery"));


$MODULE_ID = 'ilia.test';

CModule::IncludeModule($MODULE_ID);

require_once($_SERVER["DOCUMENT_ROOT"] . "/local/modules/$MODULE_ID/include.php");

$request = Context::getCurrent()->getRequest();
global $APPLICATION;

$USER_RIGHT = $POST_RIGHT = $APPLICATION->GetGroupRight($MODULE_ID);

if (!in_array($USER_RIGHT, array("W", "GU"))) { // "D" ACCESS_DENIED
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

$aTabs = array(
    array("DIV" => "edit1", "TAB" => 'Запустить наценку', "ICON" => "main_user_edit", "TITLE" => ''),
    array("DIV" => "edit2", "TAB" => 'Просмотр истории запусков', "ICON" => "main_user_edit", "TITLE" => ''),
//    array("DIV" => "edit3", "TAB" => 'Просмотр очереди на запуск', "ICON" => "main_user_edit", "TITLE" => ''),
);

$tabControl = new CAdminTabControl("tabControl", $aTabs);


$APPLICATION->SetTitle('Изменение цен');
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php"); ?>
<?
$tabControl->Begin();
$tabControl->BeginNextTab();

$arCatalogIblocks = IliaModule\PriceChanger::GetCatalogs();
$arLogs = IliaModule\IliaLogtable::getList()->fetchAll();

?>

    <form id="SET_PRICES" action="\local\modules\ilia.test\ajax\setPrice_ajax.php" method="POST"
          class="unloadig_sort" enctype="multipart/form-data">
        <?= bitrix_sessid_post() ?>
        <h3>Выберите торговый каталог</h3>
        <div class="input-field">
            <select required name="IBLOCK_ID" onchange="hideSections(this);">
                <option selected value="0">Выберите каталог</option>
                <? foreach ($arCatalogIblocks as $IBLOCK) {
                    ?>
                    <option value="<?= $IBLOCK["IBLOCK_ID"] ?>"><?= $IBLOCK["NAME"] ?></option>
                    <?
                } ?>
            </select>
        </div>
        <div id="showSectionID">
            <h3>Выберите раздел</h3>
            <div class="input-field">
                <select required id="SECTIONS" name="SECTION_ID">
                    <option selected value="0">Выберите каегорию</option>
                    <? foreach ($arCatalogIblocks as $IBLOCK) {
                        if (!empty($IBLOCK["SECTIONS"])) {
                            IliaModule\PriceChanger::showMenu($IBLOCK["SECTIONS"]);
                        }
                    } ?>
                </select>
            </div>
            <h3>Введите процент</h3>
            <div class="input-field">
                <input type="text" required name="PERSENT">
            </div>
        </div>

        <br/>
        <div id="RESPONSE" style=""></div>
        <br/>
        <div class="loader" style="display:none">loading</div>
        <input name="SUBMIT" type="submit" class="type" id="SUBMIT" class="btn" value="Сохранить">


    </form>


<? $tabControl->BeginNextTab(); ?>
    <div style="display:block">
        <div class="adm-list-table-header" style="display:flex">
            <div style="width:5%" class="adm-list-table-cell">
                <div class="adm-list-table-cell-inner">ID</div>
            </div>
            <div style="width:20%" class="adm-list-table-cell adm-list-table-cell-sort"
                 title="Сортировка: TIMESTAMP_X">
                <div class="adm-list-table-cell-inner">TIMESTAMP_X</div>
            </div>
            <div style="width:20%" class="adm-list-table-cell adm-list-table-cell-sort"
                 title="Сортировка: SECTION_ID">
                <div class="adm-list-table-cell-inner">SECTION_ID</div>
            </div>
            <div style="width:20%" class="adm-list-table-cell adm-list-table-cell-sort"
                 title="Сортировка: ELEMENTS_COUNT">
                <div class="adm-list-table-cell-inner">ELEMENTS_COUNT</div>
            </div>
            <div style="width:20%" class="adm-list-table-cell adm-list-table-cell-sort"
                 title="Сортировка: PERSENT">
                <div class="adm-list-table-cell-inner">PERSENT</div>
            </div>
            <div style="width:15%" class="adm-list-table-cell adm-list-table-cell-sort"
                 title="Сортировка: STATUS">
                <div class="adm-list-table-cell-inner">STATUS</div>
            </div>
        </div>

        <? foreach ($arLogs as $arLog) {
            ?>
            <div style="display:flex">
                <div style="width:5%" class="adm-list-table-cell align-right"><span
                            class="perfmon_number"><?= $arLog["ID"] ?></span>
                </div>
                <div style="width:20%" class="adm-list-table-cell"><?= $arLog["TIMESTAMP_X"] ?></div>
                <div style="width:20%" class="adm-list-table-cell"><?= $arLog["SECTION_ID"] ?></div>
                <div style="width:20%" class="adm-list-table-cell"><?= $arLog["ELEMENTS_COUNT"] ?></div>
                <div style="width:20%" class="adm-list-table-cell"><?= $arLog["PERSENT"] ?></div>
                <div style="width:15%"
                     class="adm-list-table-cell adm-list-table-cell-last"><?= $arLog["STATUS"] ?></div>
            </div>
            <?
        } ?>

    </div>

<?
$tabControl->End();
?>
    <script>

        function hideSections(element) {
            $iblock = $(element).val();
            $('#SECTIONS').find('option').hide();
            $('#SECTIONS').find('option[iblock="' + $iblock + '"]').show();
            $('#SECTIONS').prop('selectedIndex', 0);
        }

        $(document).on("submit", "#SET_PRICES", function (e) {
            $(".loader").css({"display": "block"});
            $("#start").attr("disabled", "disabled");
            e.preventDefault();
            var form = $(this);
            formData = new FormData(form[0]);
            jQuery.ajax({

                url: form.attr("action"),
                type: "POST",
                dataType: 'JSON',
                data: formData,
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    $(".loader").css({"display": "none"});
                    $("#start").removeAttr("disabled");
                    $('#RESPONSE').html(data.ANSWER);
                },
            });
            return false;
        });
    </script>

    <style>
        form .input-field select {
            padding: 11px 15px;
            font-family: Onest;
            font-weight: 400;
            font-size: 14px;
            line-height: 19px;
            width: 100%;
            max-width: 290px;
            border: 1px solid #676767;
            outline: none;
            border-radius: 0 !important;
            background-color: transparent;
        }

        .loader {
            overflow: hidden;
            font-size: 10px;
            margin: 50px auto;
            text-indent: -9999em;
            width: 11em;
            height: 11em;
            border-radius: 50%;
            background: #65abf8;
            background: -moz-linear-gradient(left, #65abf8 10%, rgba(101, 171, 248, 0) 42%);
            background: -webkit-linear-gradient(left, #65abf8 10%, rgba(101, 171, 248, 0) 42%);
            background: -o-linear-gradient(left, #65abf8 10%, rgba(101, 171, 248, 0) 42%);
            background: -ms-linear-gradient(left, #65abf8 10%, rgba(101, 171, 248, 0) 42%);
            background: linear-gradient(to right, #65abf8 10%, rgba(101, 171, 248, 0) 42%);
            position: relative;
            -webkit-animation: load3 1.4s infinite linear;
            animation: load3 1.4s infinite linear;
            -webkit-transform: translateZ(0);
            -ms-transform: translateZ(0);
            transform: translateZ(0);
        }

        .loader:before {
            width: 50%;
            height: 50%;
            background: #65abf8;
            border-radius: 100% 0 0 0;
            position: absolute;
            top: 0;
            left: 0;
            content: '';
        }

        .loader:after {
            background: white;
            width: 75%;
            height: 75%;
            border-radius: 50%;
            content: '';
            margin: auto;
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
        }

        @-webkit-keyframes load3 {
            0% {
                -webkit-transform: rotate(0deg);
                transform: rotate(0deg);
            }
            100% {
                -webkit-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }
    </style>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php"); ?>