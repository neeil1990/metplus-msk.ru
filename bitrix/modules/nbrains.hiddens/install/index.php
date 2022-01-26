<?

use Bitrix\Main\Localization\Loc;

IncludeModuleLangFile(__FILE__);

if (class_exists('nbrains_hiddens')) return;

class nbrains_hiddens extends CModule
{
    var $MODULE_ID = "nbrains.hiddens";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;

    public function __construct()
    {
        $arModuleVersion = array();

        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path."/version.php");

        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
        {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }

        $this->PARTNER_NAME = "nBrains";
        $this->PARTNER_URI = '';

        $this->MODULE_NAME = GetMessage('nBrains_hidden_MODULE_NAME');
        $this->MODULE_DESCRIPTION = GetMessage('nBrains_hidden_MODULE_DESCRIPTION');
    }

    public function DoInstall()
    {
        global $USER, $APPLICATION, $step;
        $step = IntVal($step);
        if ($step < 2)
        {
            $APPLICATION->IncludeAdminFile('nBrains', $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/$this->MODULE_ID/install/step.php");
        }
        elseif ($step == 2)
        {
            RegisterModule($this->MODULE_ID);

            RegisterModuleDependences("main", "OnEndBufferContent", $this->MODULE_ID, "nBrainsBase", "index");

            return true;
        }
    }

    public function DoUninstall()
    {
        UnRegisterModuleDependences("main", "OnEndBufferContent", $this->MODULE_ID, "nBrainsBase", "index");

        UnRegisterModule($this->MODULE_ID);

        return true;
    }
}
?>
