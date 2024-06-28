<?php

namespace KIToolbox\Models;

use SimpleORMap;

/**
 * KI-Toolbox Tools
 *
 * @author  Ron Lucke <lucke@elan-ev.de>
 * @license GPL2 or any later version
 * 
 * @property int $id database column
 **/

class Quota extends SimpleORMap
{

    protected static function configure($config = [])
    {
        $config['db_table'] = 'kit_quotas';

        parent::configure($config);
    }
}