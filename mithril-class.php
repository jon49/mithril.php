<?php

//Simple port of Mithril.js
class Mithril {

    static private $parser = '/(?:(^|#|\.)([^#\.\[\]]+))|(\[.+?\])/';
    static private $attrParser = '/\[(.+?)(?:=("|\'|)(.*?)\2)?\]/';
    static private $voidElements = '/AREA|BASE|BR|COL|COMMAND|EMBED|HR|IMG|INPUT|KEYGEN|LINK|META|PARAM|SOURCE|TRACK|WBR/';

    static public function value($arr, $prop, $default) {
        if (isset($arr[$prop])) {
            return ($arr[$prop]) ? $arr[$prop] : $default;
        }
        return $default;
    }

    // parse input into friendly array for composing into
    // html e.g., 
    // $html = Mithril::parse('#myId.myClass[selected=""]', 'some text');  // OR
    // $html = Mithril::parse('div', ['id' => 'myId', 'class' => 'myClass', 'selected => ''], 'some text');
    // Which creates after passing into 
    // Mithril::compose($html);
    // <div id="myId" class="myClass" selected="">some text</div>
    // You can also do:
    // $input = Mithril::parse('input[type="text"][value="Init"]');
    // Mithril::compose($input);
    // <input type="text" value="Init">
    static public function parse() {

        $args = func_get_args();
        $hasAttrs = 
            isset($args[1]) 
            && is_array($args[1]) 
            && !isset($args[1][0]['_mithril']);
        $attrs = $hasAttrs ? $args[1] : [];
        $cell = [
            'tag' => 'div',
            'attrs' => [],
            '_mithril' => '',
            'children' => ''
        ];

        $cell['children'] = 
            $hasAttrs 
            ? self::value($args, 2, '') 
            : self::value($args, 1, '');

        $matches = $classes = [];
        preg_match_all(self::$parser, $args[0], $matches);
        for ($i = 0; $i < count($matches[0]); $i++) {
            if (!$matches[1][$i] && $matches[0][$i][0] != '[')
                $cell['tag'] = $matches[2][$i];
            else if ($matches[1][$i] == '#') {
                $cell['attrs']['id'] = $matches[2][$i];
            }
            else if ($matches[1][$i] == '.') {
                $classes[] = $matches[2][$i];
            }
            else if ($matches[3][$i][0] == '[') {
                $pair;
                preg_match_all(self::$attrParser, $matches[3][$i], $pair);
                print_r($pair);
                $cell['attrs'][$pair[1][0]] = self::value($pair[3], 0, '');
            }
        }

        if (count($classes))
            $cell['attrs']['class'] = implode(' ', $classes);

        foreach ($attrs as $attrName => $attrValue) {
            if ($attrName == 'class') {
                if (!isset($cell['attrs']['class'])) 
                    $cell['attrs']['class'] = '';
                $cell['attrs'][$attrName] .= ' '.$attrValue;
            }
            else $cell['attrs'][$attrName] = $attrValue;
        }

        print_r($cell);
        return $cell;
    }

    //Compose associative array created in Mithril::parse into
    //html code.
    static public function compose($tags) {

        $html = '';
        if (isset($tags['tag'])) {

            //compose tag
            $html .= '<'.$tags['tag'].' ';
            foreach ($tags['attrs'] as $prop => $value)
                $html .= $prop.'='.'"'.$value.'" ';
            $html .= '>';

            // Get children
            $html .= (is_array($tags['children']))
                ? self::compose($tags['children'])
                : $tags['children'];

            // Close the tag
            if (!preg_match(self::$voidElements, strtoupper($tags['tag'])))
                $html .= '</'.$tags['tag'].'>';
        } 
        else {
            //loop through each tag in index array
            foreach ($tags as $tag) {
                $html .= self::compose($tag);
            }
        }

        return $html;
    }

}
