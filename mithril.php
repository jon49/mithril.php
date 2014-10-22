<?php

require_once 'mithril-class.php';

// Mithril::parse made easy to use
function m() {
    return call_user_func_array(['Mithril', 'parse'], func_get_args());
}

// Mithril::compose made easy to use
function compose($tags) {
    return call_user_func(['Mithril', 'compose'], $tags);
}
