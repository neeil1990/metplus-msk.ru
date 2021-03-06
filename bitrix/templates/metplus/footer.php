<footer class="main-footer">
    <div class="container">
        <div class="row">
            <?$APPLICATION->IncludeComponent("bitrix:menu", "bottom.menu", Array(
                "ROOT_MENU_TYPE" => "bottom",   // Тип меню для первого уровня
                "MENU_CACHE_TYPE" => "A",   // Тип кеширования
                "MENU_CACHE_TIME" => "36000000",    // Время кеширования (сек.)
                "MENU_CACHE_USE_GROUPS" => "Y", // Учитывать права доступа
                "MENU_THEME" => "site", // Тема меню
                "CACHE_SELECTED_ITEMS" => "N",
                "MENU_CACHE_GET_VARS" => "",    // Значимые переменные запроса
                "MAX_LEVEL" => "2", // Уровень вложенности меню
                "CHILD_MENU_TYPE" => "bottom_left", // Тип меню для остальных уровней
                "USE_EXT" => "Y",   // Подключать файлы с именами вида .тип_меню.menu_ext.php
                "DELAY" => "N", // Откладывать выполнение шаблона меню
                "ALLOW_MULTI_SELECT" => "N",    // Разрешить несколько активных пунктов одновременно
                "COMPONENT_TEMPLATE" => "catalog_horizontal"
            ),
                false
            );?>

            <?$APPLICATION->IncludeComponent("bitrix:menu", "bottom_useful.menu", Array(
                "ROOT_MENU_TYPE" => "bottom_useful",    // Тип меню для первого уровня
                "MENU_CACHE_TYPE" => "A",   // Тип кеширования
                "MENU_CACHE_TIME" => "36000000",    // Время кеширования (сек.)
                "MENU_CACHE_USE_GROUPS" => "Y", // Учитывать права доступа
                "MENU_THEME" => "site", // Тема меню
                "CACHE_SELECTED_ITEMS" => "N",
                "MENU_CACHE_GET_VARS" => "",    // Значимые переменные запроса
                "MAX_LEVEL" => "1", // Уровень вложенности меню
                "CHILD_MENU_TYPE" => "bottom_useful",   // Тип меню для остальных уровней
                "USE_EXT" => "Y",   // Подключать файлы с именами вида .тип_меню.menu_ext.php
                "DELAY" => "N", // Откладывать выполнение шаблона меню
                "ALLOW_MULTI_SELECT" => "N",    // Разрешить несколько активных пунктов одновременно
                "COMPONENT_TEMPLATE" => "catalog_horizontal"
            ),
                false
            );?>
            <div class="footer-column">
                <ul class="footer-contact_list">
                    <li><a href="tel:+74951140102"><span class="glipf-call-answer"></span>+7 (495) 114-01-02</a></li>
                    <li><a href="mailto:avs@metplus-vrn.ru" class="footer-mail"><span class="glipf-email"></span>avs@metplus-vrn.ru</a></li>
                    <li>
                        <span class="glipf-clock"></span>Будни: 8:00 - 17:00 <br>Суббота, воскресенье - выходные
                    </li>
					<noindex><li><a href="https://vk.com/metplusvrn"  rel="nofollow"><span class="instagram-link"><img style="width:14px; height: 14px; margin-right: 6px;" src="https://metplus-osk.ru/upload/medialibrary/9f5/9f502909be683766dde6842684115a95.png" alt="vk"> Наш ВКонтакте</span></a></li></noindex>
				</ul>
                <ul class="social-network">
                    <!--
                    <li><a href=""><span class="glipf-vk"></span></a></li>
                    <li><a href=""><span class="glipf-facebook"></span></a></li>
                    <li><a href=""><span class="glipf-odnoklassniki"></span></a></li>
                    <li><a href=""><span class="glipf-twitter"></span></a></li>
                -->
                </ul>
 </div>
        </div>
        <div class="row">
            <div class="col-lg-10">
                <div class="rules">
					@ 2006-2022. ООО «Корпорация Металлинвест»
                    <a href="/upload/politics.pdf" target="_blank">Политика конфиденциальности</a>
                    <a href="/upload/compliance.pdf" target="_blank">Согласие на обработку персональных данных</a>
                </div>
            </div>
            <div class="col-lg-2">
                <a class="prime-incut white colour" style="padding: 1.2em 0 0;"></a>
            </div>
</div>

</footer>
<!-- end main-footer -->
<div class="scroll-to-top"></div>
</div>
<!-- END GLOBAL-WRAPPER -->
<div class="cart-content">
    <div class="cart-content_first"></div>
    <div class="cart-content_second"></div>
</div>

<div aria-hidden="true" class="modal fade js-modal" id="citySelect" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-dialog-city" role="document">
        <div class="modal-content">
            <div class="modal-city_title section-title">Выберите ваш город</div>
            <button aria-label="Close" class="close uhified_close-btn" data-dismiss="modal" type="button"></button>
            <div class="row">
                <ul class="modal-city_list-unstyled col-sm-6">
                    <li>Москва</li>
                    <li>
                        <a href="https://metplus-vrn.ru/">Воронеж</a>
                    </li>
                </ul>
                <ul class="modal-city_list-unstyled col-sm-6">

                    <li><a href="http://metplus-osk.ru/">Старый Оскол</a></li>
                    <li>
                        Лиски
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" class="modal fade js-modal" id="callback" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-callback" role="document">
        <div class="modal-content">
            <button aria-label="Close" class="close uhified_close-btn" data-dismiss="modal" type="button"></button>

            <?$APPLICATION->IncludeComponent(
                "prime:main.feedback",
                "call",
                array(
                    "EVENT_MESSAGE_ID" => array(
                        0 => "86",
                    ),
                    "IBLOCK_ID" => "31",
                    "IBLOCK_TYPE" => "feedback",
                    "OK_TEXT" => "Спасибо, ваше сообщение принято.",
                    "PROPERTY_CODE" => array(
                        0 => "NAME",
                        1 => "PHONE",
                    ),
                    "USE_CAPTCHA" => "N",
                    "CAPTCHA_SITE_KEY" => "6Ld60c4UAAAAAGXC83w4_ZPy-Q6OErFzaVYjjNQl",
                    "CAPTCHA_SERVER_KEY" => "6Ld60c4UAAAAAP7qkcYtAQ_byWeHtD0kgGFiH0Q9",
                    "COMPONENT_TEMPLATE" => "call"
                ),
                false
            );?>
        </div>
    </div>
</div>

<? if($_REQUEST["success"]): ?>
    <div aria-hidden="true" class="modal fade js-modal" id="success_msg" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-callback" role="document">
            <div class="modal-content">
                <button aria-label="Close" class="close uhified_close-btn" data-dismiss="modal" type="button"></button>
                <div class="form-callback_title">
                    Сообщение отправлено!
                    <small>Мы обязательно <span class="min">вам перезвоним.</span></small>
                </div>
            </div>
        </div>
    </div>
<? endif; ?>

<link href="<?=SITE_TEMPLATE_PATH?>/libs/jquery-ui/jquery-ui.css" rel="stylesheet" />
<link href="<?=SITE_TEMPLATE_PATH?>/css/min.css" rel="stylesheet" />
<link href="<?=SITE_TEMPLATE_PATH?>/css/main.css" rel="stylesheet" />

<script src="<?=SITE_TEMPLATE_PATH?>/js/min.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/libs/fancyTable.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/libs/parallax.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/libs/jquery.sticky-kit.min.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/libs/jquery.cookie.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/libs/jquery-ui/jquery-ui.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/js/main.js"></script>


<? if(isDebug()): ?>
<script>
        (function(w,d,u){
                var s=d.createElement('script');s.async=true;s.src=u+'?'+(Date.now()/60000|0);
                var h=d.getElementsByTagName('script')[0];h.parentNode.insertBefore(s,h);
        })(window,document,'https://cdn-ru.bitrix24.ru/b7243579/crm/site_button/loader_2_s8ozty.js');
</script>
<? endif; ?>

</body>
</html>
