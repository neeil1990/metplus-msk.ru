<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CatalogSectionComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 *
 *  _________________________________________________________________________
 * |	Attention!
 * |	The following comments are for system use
 * |	and are required for the component to work correctly in ajax mode:
 * |	<!-- items-container -->
 * |	<!-- pagination-container -->
 * |	<!-- component-end -->
 */

$this->setFrameMode(true);

if(count($arResult['ITEMS'])) :
?>

<? if($arResult['UF_HIDDEN_COL']):?>
    <?
    $hidden_cols = explode(',', $arResult['UF_HIDDEN_COL']);
    foreach($hidden_cols as $col): ?>
    <style>
        .product-table tr th:nth-child(<?=$col?>),
        .product-table tr td:nth-child(<?=$col?>){
            display: none;
        }
    </style>
    <? endforeach; ?>
<? endif; ?>

<table class="product-table" id="product-table">
    <thead>
        <tr>
            <? foreach ($arResult['FIELDS'] as $field):?>
            <th><?=$field?></th>
            <? endforeach;?>
        </tr>
    </thead>
    <tbody>
        <?
        $inc_manager = 0;
        $inc_instock = 0;
        foreach ($arResult['ITEMS'] as $i => $arItem):
            $priceGroup = getGroupPriceForProduct(16, $arItem['ID']);
            $price = array_map(function($val){
                return $val['PRINT_PRICE'];
            }, $arItem['ITEM_PRICES']);
            ?>
        <tr>
            <td class="product-table_first-cell">
                <span class="product-item_name <? if($arItem['CATALOG_QUANTITY'] < 1000):?>product-item_name-mod<?endif;?>">
                    <?=htmlspecialchars_decode(preg_replace(array('|[\s]+|s','/\(|\)/'), array(' ', '"'), trim($arItem['NAME'])))?>
                </span>
                <span class="product-availability">
                    <? if($arItem['CATALOG_QUANTITY'] < 1000):?>
                        <?
                        if($arResult['UF_JS_MANAGER'] && $arResult['UF_JS_MANAGER'] <= $inc_manager): ?>
                            <span class="manager" data-text="Количество ограничено, уточняйте у менеджера."></span>
                        <?
                            $inc_manager++;
                        else: ?>
                            <span class="manager">Количество ограничено, уточняйте у менеджера.</span>
                        <?
                            $inc_manager++;
                        endif; ?>
                    <? else:?>
                        <?
                        if($arResult['UF_JS_INSTOCK'] && $arResult['UF_JS_INSTOCK'] <= $inc_instock): ?>
                            <span class="instock" data-text="В наличии на складе."></span>
                            <?
                            $inc_instock++;
                        else: ?>
                            <span class="instock">В наличии на складе.</span>
                            <?
                            $inc_instock++;
                        endif; ?>
                    <?endif;?>
                </span>
                <div class="product-item_popup">
                    <div class="product-item_popup-close"><span class="glipf-reset"></span></div>
                    <ul class="product-item_popup-list">
                        <li>
                            <? if($arResult['UF_JS_NAME'] && $arResult['UF_JS_NAME'] <= $i): ?>
                                <strong data-text="Наименование товара"></strong>
                            <? else: ?>
                                <strong>Наименование товара</strong>
                            <? endif; ?>

                            <span class="product-item_name"><?=htmlspecialchars_decode(preg_replace(array('|[\s]+|s','/\(|\)/'), array(' ', '"'), trim($arItem['NAME'])))?></span>
                        </li>
                        <li>
                            <? if($arResult['UF_JS_STEEL'] && $arResult['UF_JS_STEEL'] <= $i): ?>
                                <strong data-text="Марка Стали"></strong>
                            <? else: ?>
                                <strong>Марка Стали</strong>
                            <? endif; ?>

                            <?=$arItem['PROPERTIES']['TYPE_METALL']['VALUE']?>
                        </li>
                        <li>
                            <? if($arResult['UF_JS_WEIGHT'] && $arResult['UF_JS_WEIGHT'] <= $i): ?>
                                <strong data-text="Вес"></strong>
                            <? else: ?>
                                <strong>Вес</strong>
                            <? endif; ?>

                            <?=$arItem['PROPERTIES']['_3_VESPMSAYT']['VALUE']?>
                        </li>
                        <li>
                            <? if($arResult['UF_JS_PRICE_WITH_NDS'] && $arResult['UF_JS_PRICE_WITH_NDS'] <= $i): ?>
                                <strong data-text="Цена руб/кг (с НДС)"></strong>
                            <? else: ?>
                                <strong>Цена руб/кг (с НДС)</strong>
                            <? endif; ?>

                            <?=$priceGroup?>
                        </li>
                        <li>
                            <? if($arResult['UF_JS_SLICE'] && $arResult['UF_JS_SLICE'] <= $i): ?>
                                <strong data-text="Резка, руб"></strong>
                            <? else: ?>
                                <strong>Резка, руб</strong>
                            <? endif; ?>

                            <?=CurrencyFormat($arItem['PROPERTIES']['PRICE_CUTTING']['VALUE'], $arItem['ITEM_PRICES'][0]['CURRENCY']);?>
                        </li>
                    </ul>

                    <? if($arResult['UF_JS_BUY'] && $arResult['UF_JS_BUY'] <= $i): ?>
                        <a href="javascript:void(0)" class="main-btn product-item_buy-btn" data-text="Купить"></a>
                    <? else: ?>
                        <a href="javascript:void(0)" class="main-btn product-item_buy-btn">Купить</a>
                    <? endif; ?>

                </div>
            </td>
            <td><?=$arItem['PROPERTIES']['TYPE_METALL']['VALUE']?></td>
            <td><?=$arItem['PROPERTIES']['_3_VESPMSAYT']['VALUE']?></td>
            <td><?=$priceGroup?></td>
            <td><?=CurrencyFormat($arItem['PROPERTIES']['PRICE_CUTTING']['VALUE'], $arItem['ITEM_PRICES'][0]['CURRENCY']);?></td>
            <td><?=implode(', ', $price)?></td>
            <td>
                <a href="javascript:void(0)" class="product-item_cart-btn main-btn" id="<?=$arItem['ID']?>"><span class="glipf-cart"></span></a>
            </td>
        </tr>
        <?endforeach;?>
    </tbody>
</table>

<div class="row">
    <div class="col-md-6">
        <div class="product-availability_text">— Наличие товара на складе</div>
        <div class="product-availability_text yellow">— Количество ограничено, уточняйте у менеджера</div>
    </div>
    <div class="col-md-6 fancy-pagination">
        <ul class="main-pagination"></ul>
    </div>
</div>

<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
    <?//=$arResult["NAV_STRING"]?>
<?endif;?>

<?
endif;
?>

<? if($arParams["DEPTH_LEVEL"] == "1"): ?>
	<div class="unified-text-section"><?=$arResult['DESCRIPTION'];?></div>
<? endif; ?>
