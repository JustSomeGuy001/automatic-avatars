<?php

$path = OW::getPluginManager()->getPlugin('autoavatars')->getRootDir() . 'langs.zip';
BOL_LanguageService::getInstance()->importPrefixFromZip($path, 'autoavatars');

OW::getPluginManager()->addPluginSettingsRouteName('autoavatars', 'autoavatars.admin');




