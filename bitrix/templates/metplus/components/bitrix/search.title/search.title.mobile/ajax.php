<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if($arResult["SEARCH"]):?>
	<table class="title-search-result mobile">

        <?foreach($arResult["SEARCH"] as $i => $arItem):?>
        <tr>
            <td class="title-search-more"><a href="<?echo $arItem["URL"]?>"><?echo $arItem["NAME"]?></a></td>
        </tr>
        <?endforeach;?>
	</table>

    <script>
        $('.title-search-result.mobile').parent().css({
            height: '160px',
            "overflow-y": 'auto'
        });
    </script>
<?endif;
?>
