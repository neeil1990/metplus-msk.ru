<?php
if(!$arParams['SECTION']['UF_TAGS_LIST'])
    return false;

?>

<style>
    .sub-links-2 {
        font-size: 14px;
        font-weight: 500;
        padding: 0;
        margin-right: 120px !important;
        list-style: none;
        width: auto;
        position: relative;
        overflow: hidden;
        display: block !important;
    }

    .sub-links-2.open,
    .sub-links-2.opens {
        height: auto;
        margin: 0 auto 30px auto !important;
    }

    .sub-links-2 li {
        margin: 5px;
        display: inline-block;
    }

    .sub-links-2 a {
        color: #3ebbf3;
        border: #3ebbf3 solid 1px;
        padding: 4px 20px;
        border-radius: 5px;
        display: inline-block;
        text-decoration: none;
    }

    .sub-links-2 a:hover {
        text-decoration: none;
        color: #ffffff;
        border: #3ebbf3 solid 1px;
        background: #3ebbf3;
    }

    .sub-links-2 .slick-arrow {
        margin: auto;
    }


    .sub-links-2 .slick-arrow:before {
        color: #3ebbf3;
        font-size: 26px;
        vertical-align: middle;
    }

    .subcategories {
        display: -webkit-box;
        display: -webkit-flex;
        display: -moz-box;
        display: -ms-flexbox;
        display: flex;
        position: relative;
    }

    .subcategories .navi {
        position: absolute;
        bottom: 0;
        right: 0;
    }

    .subcategories .navi span {
        cursor: pointer;
        color: #3ebbf3;
        text-decoration: dotted underline;
        font-size: 14px;
    }

    .subcategories .navi span:hover {
        text-decoration: none;
    }

    .slick-next:before {
        content: "\f105";
        font-family: FontAwesome;
        display: inline-block;
        font-weight: 400;
        font-style: normal;
        line-height: 1;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        color: #3ebbf3;
        font-size: 40px;
    }

    .slick-prev:before {
        content: "\f104";
        font-family: FontAwesome;
        display: inline-block;
        font-weight: 400;
        font-style: normal;
        line-height: 1;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        color: #3ebbf3;
        font-size: 40px;
    }

    .sub-links-2 .slick-next:after {
        display: inline-block;
        content: '';
        width: 30px;
        height: 39px;
        background: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB4AAAAgCAMAAAAynjhNAAAAaVBMVEX///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////9tAe1xAAAAI3RSTlP9B/Xv6OHZvqmfinVqYFVLQjgmFxAf0Mcws5SAf5W0xi/PHr7jVrUAAABxSURBVCjP3dJZCoAgEIBhbbN9TyvTrPsfMgxDTPQAPn84/gMDIJynZVyHvmubuqIlK/INkStLkzgCQLFQfEjmiGAX7/L1afAth2u2hluMnexLo5K5/bde7OUAy5kvjX5poZXr4f5LDaP8l2az0Gwc8gMuITF/U6PCDgAAAABJRU5ErkJggg==') no-repeat;
        position: absolute;
        top: -3px;
        left: -27px;
    }

    .sub-links-2 .slick-prev:after {
        content: '';
        display: inline-block;
        width: 30px;
        height: 39px;
        background: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB4AAAAgCAMAAAAynjhNAAAAaVBMVEX///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////9tAe1xAAAAI3RSTlP9DfXo4dnQx7Spn4p1amBVS0I4Jhcfve4wBZSAf5XvLwS+HhGGx64AAAB2SURBVCjP3ZJHDoAwEAND7733hP8/kioZEpC4hvPI9qy0hBC10nTDtErbcQev84MwipO0zopcUZoNjxfcn3jiMVtxy2GKcuCjfD7S2AbGtpgW1ZDueDXxMGxTuc0v+N0c5dimcpuPXz6V3cv/Y86e1N7wtONmATKLMZCWB9C4AAAAAElFTkSuQmCC') no-repeat;
        position: absolute;
        top: -3px;
        right: -27px;
    }

    .slick-prev,
    .slick-next {
        width: auto;
        height: auto;
        z-index: 1;
    }

    .slick-next {
        right: 2px;
    }

    .slick-prev {
        left: 2px;
    }

    /* Arrows */

    .slick-prev,
    .slick-next {
        position: absolute;
        display: block;
        height: 30px;
        width: 20px;
        line-height: 0px;
        font-size: 0px;
        cursor: pointer;
        background: transparent;
        color: transparent;
        top: 50%;
        -webkit-transform: translate(0, -50%);
        -ms-transform: translate(0, -50%);
        -moz-transform: translate(0, -50%);
        -o-transform: translate(0, -50%);
        transform: translate(0, -50%);
        padding: 0;
        border: none;
        outline: none;
    }

    .slick-prev:hover,
    .slick-prev:focus,
    .slick-next:hover,
    .slick-next:focus {
        outline: none;
        background: transparent;
        color: transparent;
    }

    .slick-prev:hover:before,
    .slick-prev:focus:before,
    .slick-next:hover:before,
    .slick-next:focus:before {
        opacity: 1;
    }

    .slick-prev.slick-disabled:before,
    .slick-next.slick-disabled:before {
        opacity: 0.25;
    }
</style>

<div class="tags-list">

    <?=$arParams['SECTION']['UF_TAGS_LIST_TITLE']?>

    <div class="subcategories">
        <ul class="tag-slider sub-links-2">
            <? foreach ($arParams['SECTION']['UF_TAGS_LIST'] as $tags):
                $arTags = explode('@', $tags, 2);
                ?>
            <li><a href="<?=$arTags[1]?>"><?=$arTags[0]?></a></li>
            <? endforeach; ?>
        </ul>
        <div class="navi">
            <span class="open">???????????????? ??????</span>
            <span hidden class="close">????????????????</span>
        </div>
    </div>
</div>
