<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("test");

include($_SERVER['DOCUMENT_ROOT'] . '/test/simple_html_dom.php');

$html = new simple_html_dom();
$html->load("<html><body><span>Hello!</span></body></html>");

$span = $html->find('span');

var_dump($span[0]->innertext);

$content = $html->save();

var_dump($content);

?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
