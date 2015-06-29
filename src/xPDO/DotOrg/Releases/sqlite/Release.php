<?php
namespace xPDO\DotOrg\Releases\sqlite;

use xPDO\xPDO;

class Release extends \xPDO\DotOrg\Releases\Release
{

    public static $metaMap = array (
        'package' => 'xPDO\\DotOrg\\Releases',
        'version' => '3.0',
        'table' => 'releases',
        'extends' => 'xPDO\\Om\\xPDOSimpleObject',
        'fields' => 
        array (
            'signature' => NULL,
            'released_on' => NULL,
            'updated_at' => 'CURRENT_TIMESTAMP',
            'version_major' => 0,
            'version_minor' => 0,
            'version_patch' => 0,
            'stability' => 'stable',
            'stability_version' => 0,
            'url' => '',
            'downloads' => 0,
        ),
        'fieldMeta' => 
        array (
            'signature' => 
            array (
                'dbtype' => 'varchar',
                'precision' => '100',
                'phptype' => 'string',
                'null' => false,
                'index' => 'unique',
            ),
            'released_on' => 
            array (
                'dbtype' => 'date',
                'phptype' => 'date',
                'null' => true,
            ),
            'updated_at' => 
            array (
                'dbtype' => 'timestamp',
                'phptype' => 'timestamp',
                'null' => false,
                'default' => 'CURRENT_TIMESTAMP',
            ),
            'version_major' => 
            array (
                'dbtype' => 'tinyint',
                'precision' => '4',
                'phptype' => 'integer',
                'null' => false,
                'default' => 0,
            ),
            'version_minor' => 
            array (
                'dbtype' => 'tinyint',
                'precision' => '4',
                'phptype' => 'integer',
                'null' => false,
                'default' => 0,
            ),
            'version_patch' => 
            array (
                'dbtype' => 'tinyint',
                'precision' => '4',
                'phptype' => 'integer',
                'null' => false,
                'default' => 0,
            ),
            'stability' => 
            array (
                'dbtype' => 'varchar',
                'precision' => '10',
                'phptype' => 'string',
                'null' => false,
                'default' => 'stable',
            ),
            'stability_version' => 
            array (
                'dbtype' => 'tinyint',
                'precision' => '4',
                'phptype' => 'integer',
                'null' => false,
                'default' => 0,
            ),
            'url' => 
            array (
                'dbtype' => 'varchar',
                'precision' => '255',
                'phptype' => 'string',
                'null' => false,
                'default' => '',
            ),
            'downloads' => 
            array (
                'dbtype' => 'integer',
                'precision' => '10',
                'phptype' => 'integer',
                'null' => false,
                'default' => 0,
            ),
        ),
        'indexes' => 
        array (
            'signature' => 
            array (
                'alias' => 'signature',
                'primary' => false,
                'unique' => true,
                'type' => 'BTREE',
                'columns' => 
                array (
                    'signature' => 
                    array (
                        'collation' => 'A',
                        'null' => false,
                    ),
                ),
            ),
            'version' => 
            array (
                'alias' => 'version',
                'primary' => false,
                'unique' => true,
                'type' => 'BTREE',
                'columns' => 
                array (
                    'version_major' => 
                    array (
                        'collation' => 'A',
                        'null' => false,
                    ),
                    'version_minor' => 
                    array (
                        'collation' => 'A',
                        'null' => false,
                    ),
                    'version_patch' => 
                    array (
                        'collation' => 'A',
                        'null' => false,
                    ),
                    'stability' => 
                    array (
                        'collation' => 'A',
                        'null' => false,
                    ),
                    'stability_version' => 
                    array (
                        'collation' => 'A',
                        'null' => true,
                    ),
                ),
            ),
            'released_on' => 
            array (
                'alias' => 'released_on',
                'primary' => false,
                'unique' => false,
                'type' => 'BTREE',
                'columns' => 
                array (
                    'released_on' => 
                    array (
                        'collation' => 'A',
                        'null' => false,
                    ),
                ),
            ),
            'downloads' => 
            array (
                'alias' => 'downloads',
                'primary' => false,
                'unique' => false,
                'type' => 'BTREE',
                'columns' => 
                array (
                    'downloads' => 
                    array (
                        'collation' => 'A',
                        'null' => false,
                    ),
                ),
            ),
        ),
        'validation' => 
        array (
            'class' => 'xPDO\\Validation\\xPDOValidator',
            'rules' => 
            array (
                'stability' => 
                array (
                    'stability_enum' => 
                    array (
                        'type' => 'xPDO\\Validation\\xPDOValidationRule',
                        'rule' => 'xPDO\\DotOrg\\Validation\\StabilityEnum',
                    ),
                ),
            ),
        ),
    );
}
