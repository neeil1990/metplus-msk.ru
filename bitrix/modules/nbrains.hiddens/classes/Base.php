<?php
use \Bitrix\Main\Page\Asset;

class nBrainsBase
{
    private $mid = 'nbrains.hiddens';
    protected $html;
    protected $settings = [];
    protected $current;

    public function __construct(&$content)
    {
        global $APPLICATION;

        $this->current = $APPLICATION->GetCurDir(true);

        $this->html = new simple_html_dom();
        $this->html->load($content);

        $this->settings = $this->getSettings();

        $this->filterUrl();

        if($this->settings){

            $this->handle();

            $content = $this->html->save();

            $this->html->clear();

            unset($this->html);

            $this->setJs($content);
        }
    }

    public function index(&$content)
    {
        return new static($content);
    }

    protected function filterUrl()
    {
        $current = $this->current;

        $this->settings = array_filter($this->settings, function($var) use ($current){
            return $var['url'] === $current;
        });
    }

    protected function handle()
    {
        foreach ($this->settings as $setting){

            foreach ($this->html->find($setting['selector']) as $i => $e) {
                if($i < $setting['quantity'])
                    continue;

                $e->textjs = htmlspecialcharsEx($e->innertext);
                $e->innertext = '';
            }
        }
    }

    protected function setJs(&$content)
    {
        $DOM = new DOMDocument();
        $DOM->loadHTML($content);

        $body = $DOM->getElementsByTagName('body')->item(0);

        $js = "var matches = document.querySelectorAll('[textjs]');
        for (var i = 0; i < matches.length; ++i) {
            var item = matches[i];
            var text = item.getAttribute('textjs');
            item.innerHTML = text;
        }
        console.log('textjs successful insert!');";

        $element = $DOM->createElement('script', $js);

        $body->appendChild($element);

        $content = $DOM->saveHTML();
    }

    private function getSettings()
    {
        $json = COption::GetOptionString($this->mid, "data");

        return ($json) ? json_decode($json, true) : [];
    }
}
