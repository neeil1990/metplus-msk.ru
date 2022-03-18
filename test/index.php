<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("test");
?>

<main class="main-content">
    <div class="container">

    <?$APPLICATION->IncludeComponent("bitrix:news.line", "accordion", Array(
        "ACTIVE_DATE_FORMAT" => "d.M.Y",	// Формат показа даты
        "CACHE_GROUPS" => "Y",	// Учитывать права доступа
        "CACHE_TIME" => "300",	// Время кеширования (сек.)
        "CACHE_TYPE" => "A",	// Тип кеширования
        "DETAIL_URL" => "",	// URL, ведущий на страницу с содержимым элемента раздела
        "FIELD_CODE" => array(	// Поля
            0 => "DETAIL_TEXT",
            1 => "",
        ),
        "IBLOCKS" => array(	// Код информационного блока
            0 => "35",
        ),
        "IBLOCK_TYPE" => "components",	// Тип информационного блока
        "NEWS_COUNT" => "100",	// Количество новостей на странице
        "SORT_BY1" => "SORT",	// Поле для первой сортировки новостей
        "SORT_BY2" => "ID",	// Поле для второй сортировки новостей
        "SORT_ORDER1" => "ASC",	// Направление для первой сортировки новостей
        "SORT_ORDER2" => "ASC",	// Направление для второй сортировки новостей
    ),
        false
    );?>

    </div>
</main>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
