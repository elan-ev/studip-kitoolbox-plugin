<?php
StudipAutoloader::addAutoloadPath(__DIR__ . '/lib/classes', 'KIToolbox\\classes');
StudipAutoloader::addAutoloadPath(__DIR__ . '/lib/models', 'KIToolbox\\models');
StudipAutoloader::addAutoloadPath(__DIR__ . '/lib/JsonApi', 'KIToolbox\\JsonApi');
StudipAutoloader::addAutoloadPath(__DIR__ . '/lib/JWT', 'KIToolbox\\JWT');
StudipAutoloader::addAutoloadPath(__DIR__ . '/lib/ToolsApi', 'KIToolbox\\ToolsApi');
StudipAutoloader::addAutoloadPath(__DIR__ . '/lib/OpenIDConnect', 'KIToolbox\\OpenIDConnect');
StudipAutoloader::addAutoloadPath(__DIR__, 'KIToolbox');
