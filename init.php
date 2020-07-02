<?php

// Add routes
OW::getRouter()->addRoute(new OW_Route('autoavatars.admin', 'admin/plugins/autoavatars', 'AUTOAVATARS_CTRL_Admin', 'settings'));
OW::getRouter()->addRoute(new OW_Route('autoavatars.admin_config-remove-item', 'admin/plugins/autoavatars/delete/:sex', 'AUTOAVATARS_CTRL_Admin', 'delete'));

$eventHandler = new AUTOAVATARS_CLASS_EventHandler();
$eventHandler->init();





