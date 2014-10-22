<?php

require_once './mithril.php';
//
// Some examples

$html = m('#myId.myClass[selected=""]', 'some text');  // OR
$html = m(
    'div',
    ['id' => 'myId', 'class' => 'myClass', 'selected' => ''],
    'some text');
echo compose($html);
// <div id="myId" class="myClass" selected="">some text</div>
$input = m('input[type="text"][value="Init"]');
echo compose($input);
// <input type="text" value="Init">

$nested = m('.some-class', [m('.some-nested-div', 'really nested text')]);
echo compose($nested);

$sideBySide = [m('.first', 'first text'), m('.second', 'second test')];
echo compose($sideBySide);
