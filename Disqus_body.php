<?php

class Disqus {

    public static function onParserFirstCallInit(Parser &$parser) {
        $parser->setHook('disqus', array('Disqus', 'render'));
        return true;
    }

    public static function render($input, $params, $parser, $frame) {
        $content = "<script>";
        if (isset($params['thread'])) {
            $content .= "var wgExtDisqusThread = '" . $params['thread'] . "';";
        }
        if (isset($params['force'])) {
            $content .= "var wgExtDisqusDom = 'comment-here';";
        }
        $content .= "</script>"; 
        if (isset($params['force'])) {
            $content .= '<div id="comment-here"></div>';
        }
        return $content;
    }

    public static function onSkinAfterContent(&$data, Skin $skin) {
        global $wgTitle, $wgRequest, $wgOut;

        if($wgTitle->isSpecialPage()
            || $wgTitle->getArticleID() == 0
            || !$wgTitle->canTalk()
            || $wgTitle->isTalkPage()
            || method_exists($wgTitle, 'isMainPage') && $wgTitle->isMainPage()
            || in_array($wgTitle->getNamespace(), array(NS_MEDIAWIKI, NS_TEMPLATE, NS_CATEGORY, NS_TOPIC))
            || $wgOut->isPrintable()
            || $wgRequest->getVal('action', 'view') != "view")
            return true;
        
        $data .= '<div id="comment"></div>';
        return true;
    }
  
    public static function onBeforePageDisplay(OutputPage &$out, Skin &$skin) {
        global $wgTitle, $wgExtDisqusThread;
        $title = $wgTitle->getPrefixedText();
        if ($wgTitle->isRedirect()) {
          $page = WikiPage::factory($wgTitle);
          $tmpTitle = $page->getRedirectTarget();
          if ($tmpTitle instanceof Title) {
            $title = $tmpTitle->getPrefixedText();
          }
        }
        
        $out->addHeadItem('iDisqus-css', '<link href="//static.kcwiki.org/iDisqus.min.css" rel="stylesheet">');
        $out->addHeadItem('iDisqus-js', '<script src="//static.kcwiki.org/iDisqus.min.js"></script>');
        $out->addInlineScript(<<<eot
        var emojiList = [{
          code:'smile',
          title:'笑脸',
          unicode:'1f604'
        },{
          code:'mask',
          title:'生病',
          unicode:'1f637'
        },{
          code:'joy',
          title:'破涕为笑',
          unicode:'1f602'
        },{
          code:'stuck_out_tongue_closed_eyes',
          title:'吐舌',
          unicode:'1f61d'
        },{
          code:'flushed',
          title:'脸红',
          unicode:'1f633'
        },{
          code:'scream',
          title:'恐惧',
          unicode:'1f631'
        },{
          code:'pensive',
          title:'失望',
          unicode:'1f614'
        },{
          code:'unamused',
          title:'无语',
          unicode:'1f612'
        },{
          code:'grin',
          title:'露齿笑',
          unicode:'1f601'
        },{
          code:'heart_eyes',
          title:'色',
          unicode:'1f60d'
        },{
          code:'sweat',
          title:'汗',
          unicode:'1f613'
        },{
          code:'smirk',
          title:'得意',
          unicode:'1f60f'
        }];
        
        var disqusThread = window.wgExtDisqusThread || '$title';
        var disqusDom = window.wgExtDisqusDom || 'comment';
        if (document.getElementById(disqusDom)) {
          var disq = new iDisqus(disqusDom, {
            forum: 'kcwikizh',
            url: '/wiki/' + disqusThread,
            site: 'https://zh.kcwiki.org',
            api: 'https://disqus.kcwiki.org/api-wiki',
            mode: 2,
            timeout: 3000,
            init: true,
            emoji_list: emojiList,
            emoji_preview: true,
            auto: true
          });
          
          document.getElementsByClassName("comment-form-url")[0].style.display = 'none';
        }
eot
        );
        return true;
    }

    public static function onBeforePageDisplayMobile(OutputPage $out) {
        global $wgTitle, $wgRequest;

        if($wgTitle->isSpecialPage()
            || $wgTitle->getArticleID() == 0
            || !$wgTitle->canTalk()
            || $wgTitle->isTalkPage()
            || method_exists($wgTitle, 'isMainPage') && $wgTitle->isMainPage()
            || in_array($wgTitle->getNamespace(), array(NS_MEDIAWIKI, NS_TEMPLATE, NS_CATEGORY, NS_TOPIC))
            || $out->isPrintable()
            || $wgRequest->getVal('action', 'view') != "view")
            return true;
      
        $out->addHTML('<div id="comment"></div>');
        return true;
    }
}