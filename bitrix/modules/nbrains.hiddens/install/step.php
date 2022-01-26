<?
/** @global CMain $APPLICATION */
IncludeModuleLangFile(__FILE__);
?>

<? if (version_compare(PHP_VERSION, '7.4.0') >= 0):

    $msg = GetMessage("nBrains_you_use_php_ok").GetMessage("MOD_INSTALL");
    echo CAdminMessage::ShowNote($msg);
    ?>

    <form action="<?echo $APPLICATION->GetCurPage(); ?>" name="form1">
        <?echo bitrix_sessid_post(); ?>
        <input type="hidden" name="lang" value="<?echo LANG ?>">
        <input type="hidden" name="id" value="nbrains.hiddens">
        <input type="hidden" name="install" value="Y">
        <input type="hidden" name="step" value="2">
        <input type="submit" name="inst" value="<?echo GetMessage("MOD_INSTALL"); ?>">
    </form>

<? else:
    echo CAdminMessage::ShowMessage(Array("TYPE" => "ERROR", "MESSAGE" => GetMessage("nBrains_you_use_php_error"), "DETAILS" => '', "HTML" => true));
    ?>

    <form action="<?echo $APPLICATION->GetCurPage()?>">
        <input type="hidden" name="lang" value="<?echo LANG?>">
        <input type="submit" name="" value="<?echo GetMessage("MOD_BACK")?>">
    </form>

<? endif; ?>

