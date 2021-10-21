<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

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
?><div class="category-section_mod">
	<div class="container">
		<div class="row">
			<? $APPLICATION->IncludeComponent("bitrix:catalog.section.list", "sections", array(
				"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
				"IBLOCK_ID" => $arParams["IBLOCK_ID"],
				"CACHE_TYPE" => $arParams["CACHE_TYPE"],
				"CACHE_TIME" => $arParams["CACHE_TIME"],
				"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
				"COUNT_ELEMENTS" => $arParams["SECTION_COUNT_ELEMENTS"],
				"TOP_DEPTH" => $arParams["SECTION_TOP_DEPTH"],
				"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
				"VIEW_MODE" => $arParams["SECTIONS_VIEW_MODE"],
				"SHOW_PARENT_NAME" => $arParams["SECTIONS_SHOW_PARENT_NAME"],
				"HIDE_SECTION_NAME" => (isset($arParams["SECTIONS_HIDE_SECTION_NAME"]) ? $arParams["SECTIONS_HIDE_SECTION_NAME"] : "N"),
				"ADD_SECTIONS_CHAIN" => (isset($arParams["ADD_SECTIONS_CHAIN"]) ? $arParams["ADD_SECTIONS_CHAIN"] : ''),
                "SECTION_USER_FIELDS" => array("UF_*")
			),
				$component,
				($arParams["SHOW_TOP_ELEMENTS"] !== "N" ? array("HIDE_ICONS" => "Y") : array())
			);?>

            <div class="category-column">
                <div class="category-item_mod category-item_mod-other">
                    <span class="category-icon category-icon_first"></span>
                    <span class="text">Кровельные материалы</span>
                    <a href="http://metprof-vrn.ru/" class="site-link">Перейти на сайт</a>
                </div>
                <div class="category-item_mod category-item_mod-other">
                    <span class="category-icon category-icon_second"></span>
                    <span class="text">Инженерная сантехника</span>
                    <a href="https://polimer-vrn.ru/" class="site-link">Перейти на сайт</a>
                </div>
                <div class="category-item_mod category-item_mod-other">
                    <span class="category-icon category-icon_third"></span>
                    <span class="text">Художественная ковка</span>
                    <a href="https://vrn-ehk.ru/" class="site-link">Перейти на сайт</a>
                </div>
            </div>

            <div class="category-column">
                <div class="price-list_box">
                    <div class="price-list_box-content">
                        <img data-src="<?=SITE_TEMPLATE_PATH?>/img/static/price-list_img.jpg" alt="alt">
						<a href="/prays/price_metall.xls" class="download-price_btn main-btn"><span class="glipf-download"></span>Скачать прайс</a>
                    </div>
                </div>
            </div>

		</div>
	</div>
</div>
