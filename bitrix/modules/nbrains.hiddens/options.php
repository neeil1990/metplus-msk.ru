<?php
IncludeModuleLangFile(__FILE__);
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");
\Bitrix\Main\UI\Extension::load("ui.hint");

    $arFields = array(
        'MODULE_ID' => $mid,
        'MESSAGE' => 'Правила успешно сохранены!',
    );

    if ($REQUEST_METHOD == "POST" && strlen($Save) > 0 && check_bitrix_sessid())
    {
        $data = [];

        foreach ($_REQUEST['url'] as $i => $url){

            $data[$i]['url'] = $url;
            $data[$i]['selector'] = $_REQUEST['selector'][$i];
            $data[$i]['quantity'] = $_REQUEST['quantity'][$i];
        }

        if(count($data))
            COption::SetOptionString($mid, "data", json_encode($data));

        CAdminNotify::Add($arFields);

        LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($mid)."&lang=".urlencode(LANGUAGE_ID)."&mid_menu=".urlencode($_REQUEST["mid_menu"]));
    }

    $jsonData = COption::GetOptionString( $mid, "data");

    $arSettings = [];
    if($jsonData)
        $arSettings = json_decode($jsonData, true);

    $aTabs = array(
        array("DIV" => "edit1", "TAB" => GetMessage("MAIN_TAB_SET"), "ICON" => "", "TITLE" => GetMessage("MAIN_TAB_TITLE_SET")),
    );

    $tabControl = new CAdminTabControl("tabControl", $aTabs);

    $tabControl->Begin();

    ?>

    <form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialchars($mid)?>&lang=<?=LANGUAGE_ID?>&mid_menu=<?=urlencode($_REQUEST["mid_menu"])?>">
    <?=bitrix_sessid_post()?>
    <?$tabControl->BeginNextTab();?>
    <tr>
        <td width="100%" style="" colspan="2">
            <?
                if($_REQUEST['Save'] === "1")
                    CAdminMessage::ShowMessage(
                        Array(
                            "TYPE" => "OK",
                            "MESSAGE" => "Saved",
                            "DETAILS" => "",
                            "HTML" => true
                        )
                    );
            ?>
        </td>
    </tr>
    <tr class="heading">
        <td valign="top" colspan="2" align="center">Title</td>
    </tr>
    <tr>
        <td width="100%" style="" colspan="2">
            <table style="width: auto; margin: 0 auto;">
                <tr>
                    <td>
                        <table class="internal" id="container" cellspacing="0" cellpadding="0" border="0">
                            <thead>
                                <tr class="heading">
                                    <td>
                                        URL
                                        <span data-hint="Относительный URL"></span>
                                    </td>
                                    <td>
                                        Selector
                                        <span data-hint="css селектор"></span>
                                    </td>
                                    <td>
                                        Quantity
                                        <span data-hint="С какого элемента применять правило"></span>
                                    </td>
                                    <td></td>
                                </tr>
                            </thead>

                            <tbody>
                                <? if(empty($arSettings)): ?>
                                    <tr>
                                        <td><input name="url[]" value="" type="text" size="60" placeholder="http://site.ru" /></td>
                                        <td><input name="selector[]" value="" type="text" size="60" placeholder=".class span" /></td>
                                        <td><input name="quantity[]" value="" type="text" size="20" pattern="^[ 0-9]+$" placeholder="2" /></td>
                                    </tr>
                                <? else: ?>
                                    <? foreach ($arSettings as $i => $setting): ?>
                                        <tr>
                                            <td><input name="url[]" value="<?=$setting['url']?>" type="text" size="60" /></td>
                                            <td><input name="selector[]" value="<?=$setting['selector']?>" type="text" size="60" /></td>
                                            <td><input name="quantity[]" value="<?=$setting['quantity']?>" type="text" size="20" pattern="^[ 0-9]+$" /></td>
                                            <td>
                                                <? if($i > 0): ?>
                                                    <a href="javascript:void(0)" onclick="remove_row(this)">Удалить</a>
                                                <? endif; ?>
                                            </td>
                                        </tr>
                                    <? endforeach; ?>
                                <? endif; ?>
                            </tbody>
                        </table>

                        <br />
                        <div style="width: 100%; text-align: center;">
                            <input type="button" value="More" onclick="additional_row(this);" />
                        </div>

                        <div style="clear: both"> </div>
                        <br />
                        <?=BeginNote();?>
                            <p><strong>URL</strong> - Относительный URL сайта для которого работает правило. Без домена и протокола http/s. Пример: '/catalog/'</p>
                            <p><strong>Selector</strong> - Элемент к которому нужно обратиться в формате css селекторов. Пример: .class > div</p>
                            <p><strong>Quantity</strong> - С какого элемента применять правило. Другими словами, сколько элементов должно быть пропущено.</p>
                        <?=EndNote();?>
                    </td>
                <tr>
            </table>
        </td>
    </tr>

    <?$tabControl->BeginNextTab();?>

    <?$tabControl->Buttons();?>
        <input type="submit" name="Save" value="<?=GetMessage("MAIN_SAVE")?>" title="<?=GetMessage("MAIN_OPT_SAVE_TITLE")?>">
    <?$tabControl->End();?>
</form>

<script type="text/javascript">

    BX.ready(function() {
        BX.UI.Hint.init(BX('container'));
    });

    function additional_row(el){
        var body = el.closest('tr').querySelector('table > tbody');
        var tr = body.querySelector('tr:last-child');
        var node = tr.cloneNode(true);

        body.appendChild( node );
    }

    function remove_row(el) {
        el.closest('tr').remove();
    }
</script>