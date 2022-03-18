<?php
$arResult['BLOCK'] = CIBlock::GetByID($arParams['IBLOCKS'][0])->GetNext();

foreach($arResult["ITEMS"] as $arItem)
    $props[$arItem['ID']] = [];

CIBlockElement::GetPropertyValuesArray($props, $arResult['BLOCK']['ID'], []);
$arResult['PROPS'] = $props;

