<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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
if($arResult['BLOCK']['ACTIVE'] === "N")
    return false;

$class = 'col-md-6';
$DESCRIPTION = $arResult['BLOCK']['DESCRIPTION'];
if(!strlen($DESCRIPTION))
    $class = 'col-md-12';

?>
<div class="accordion">
    <div class="row">
        <? if($DESCRIPTION):?>
            <div class="<?=$class?>">
                <?=$arResult['BLOCK']['DESCRIPTION']?>
            </div>
        <? endif; ?>

        <div class="<?=$class?>">
            <div id="accordion">
                <?
                foreach($arResult["ITEMS"] as $arItem):?>
                <?
                $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
                $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
                ?>
                <h3><?echo $arItem["NAME"]?></h3>
                <div id="<?=$this->GetEditAreaId($arItem['ID']);?>">
                    <?=$arItem["DETAIL_TEXT"]?>

                    <?
                    if($tags = $arResult['PROPS'][$arItem['ID']]['UF_TAGS_LIST']['VALUE']){
                        $tags['UF_TAGS_LIST'] = $tags;

                        /* TAGS LIST */
                        $APPLICATION->IncludeFile(SITE_TEMPLATE_PATH."/inc/tags_list.php", Array("SECTION" => $tags), Array(
                            "MODE"      => "php",
                            "NAME"      => "",
                            "TEMPLATE"  => "",
                            "SHOW_BORDER" => false,
                        ));
                    }
                    ?>
                </div>
                <?endforeach;?>
            </div>
        </div>
    </div>
</div>

