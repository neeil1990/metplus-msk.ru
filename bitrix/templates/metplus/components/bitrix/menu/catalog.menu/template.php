<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

$this->setFrameMode(true);
?>
<ul class="catalog-menu">
<?foreach($arResult["MENU_STRUCTURE"] as $itemID => $arColumns):?>
    <?$existPictureDescColomn = ($arResult["ALL_ITEMS"][$itemID]["PARAMS"]["picture_src"] || $arResult["ALL_ITEMS"][$itemID]["PARAMS"]["description"]) ? true : false;?>
    <li class="catalog-menu_item">
        <a href="<?=$arResult["ALL_ITEMS"][$itemID]["LINK"]?>">
            <span class="menu-icon <?=$arResult["ALL_ITEMS"][$itemID]["PARAMS"]["ICON_MENU"]?>"></span>

            <? if(in_array($arResult["ALL_ITEMS"][$itemID]["PARAMS"]["ID"], $arResult['PROPERTIES']['UF_JS_MENU'])): ?>
                <span data-text="<?=mb_strtoupper($arResult["ALL_ITEMS"][$itemID]["TEXT"])?>"></span>
            <? else: ?>
                <span><?=mb_strtoupper($arResult["ALL_ITEMS"][$itemID]["TEXT"])?></span>
            <? endif; ?>
        </a>
    <?if (is_array($arColumns) && count($arColumns) > 0):?>
        <?foreach($arColumns as $key=>$arRow):?>
            <ul class="catalog-submenu">
            <?foreach($arRow as $itemIdLevel_2=>$arLevel_3):?>
                <li>
                    <a href="<?=$arResult["ALL_ITEMS"][$itemIdLevel_2]["LINK"]?>">
                        <? if(in_array($arResult["ALL_ITEMS"][$itemIdLevel_2]["PARAMS"]["ID"], $arResult['PROPERTIES']['UF_JS_MENU'])): ?>
                            <span data-text="<?=$arResult["ALL_ITEMS"][$itemIdLevel_2]["TEXT"]?>"></span>
                        <? else: ?>
                            <span><?=$arResult["ALL_ITEMS"][$itemIdLevel_2]["TEXT"]?></span>
                        <? endif; ?>
                    </a>
                </li>
            <?endforeach;?>
            </ul>
        <?endforeach;?>
    <?endif?>
    </li>
<?endforeach;?>
</ul>
