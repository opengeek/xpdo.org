<?php
/* copy this file to properties.inc.php and make any necessary changes for your environment */
return [
    'sqlite_array_options' => [
        \xPDO\xPDO::OPT_HYDRATE_FIELDS => true,
        \xPDO\xPDO::OPT_HYDRATE_RELATED_OBJECTS => true,
        \xPDO\xPDO::OPT_HYDRATE_ADHOC_FIELDS => true,
        \xPDO\xPDO::OPT_CONNECTIONS => [
            [
                'dsn' => 'sqlite:' . __DIR__ . '/../data/xpdo',
                'username' => '',
                'password' => '',
                'options' => [
                    \xPDO\xPDO::OPT_CONN_MUTABLE => true,
                ],
                'driverOptions' => [],
            ],
        ],
    ]
];
