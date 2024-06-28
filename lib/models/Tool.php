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
 * @property string $name database column
 * @property string $description database column
 * @property string $url database column
 **/

class Tool extends SimpleORMap
{

    protected static function configure($config = [])
    {
        $config['db_table'] = 'kit_tools';

        parent::configure($config);
    }
}