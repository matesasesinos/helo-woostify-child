<?php

require_once get_stylesheet_directory() . '/inc/plugin-update-checker/plugin-update-checker.php';

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$updateChecker = PucFactory::buildUpdateChecker(
    'https://github.com/matesasesinos/helo-woostify-child.git',
    __FILE__,
    'helo-woostify-child'
);

$updateChecker->setBranch('main');
