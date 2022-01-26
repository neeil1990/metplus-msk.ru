<?php

$module_id = 'nbrains.hiddens';
IncludeModuleLangFile(__FILE__);

CModule::AddAutoloadClasses(
    $module_id,
    array(
        "nBrainsBase" => "classes/Base.php",
        "simple_html_dom" => "classes/simple_html_dom.php",
    )
);

