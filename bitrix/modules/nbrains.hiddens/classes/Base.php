<?php
use \Bitrix\Main\Page\Asset;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Options;

class nBrainsBase
{
    private $mid = 'nbrains.hiddens';
    protected $dom;
    protected $body;
    protected $settings = [];

    public function __construct(&$content)
    {
        $this->getBody($content);

        if($this->body){

            $this->getSettings();

            $this->filterSettings();

            if(count($this->settings) > 0){

                $this->initDom();

                $this->handle();

                $this->saveBodyTo($content);

                $this->setJs($content);
            }
        }
    }

    protected function initDom()
    {
        $dom = (new Dom)->setOptions((new Options())
            ->setRemoveSmartyScripts(false)
            ->setRemoveStyles(false)
            ->setRemoveScripts(false)
        )->loadStr($this->body);

        if(count($dom->find('#panel')))
            $dom->find('#panel')[0]->delete();

        $this->dom = $dom;
    }

    protected function saveBodyTo(&$content)
    {
        $body = $this->dom->outerHtml;

        $content = preg_replace('#<body(.*?)>(.*?)</body>#is', $body, $content);

        unset($this->dom);
    }

    protected function getBody($content)
    {
        $body = '';
        preg_match('#<body(.*?)>(.*?)</body>#is', $content, $body, PREG_UNMATCHED_AS_NULL);

        $this->body = (isset($body[0]) && $body) ? $body[0] : null;
    }

    public function index(&$content)
    {
        return new static($content);
    }

    protected function filterSettings()
    {
        global $APPLICATION;

        $current = $APPLICATION->GetCurDir(true);

        $this->settings = array_filter($this->settings, function($var) use ($current){
            return $var['url'] === $current;
        });
    }

    protected function handle()
    {
        foreach ($this->settings as $setting){

            foreach ($this->dom->find($setting['selector']) as $i => $e) {
                if($i < $setting['quantity'])
                    continue;

                $e->setAttribute('textjs', $e->innerHtml);

                if(strlen($e->text) > 0)
                    $e->firstChild()->setText('');
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
        $this->settings = ($json) ? json_decode($json, true) : [];
    }
}
