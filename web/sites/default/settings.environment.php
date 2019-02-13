<?php

$databases['default']['default'] = array (
    'database' =>  getenv('DATABASE_NAME'),
    'username' => getenv('DATABASE_USER'),
    'password' => getenv('DATABASE_PASSWORD'),
    'prefix' => '',
    'host' => getenv('DATABASE_HOST'),
    'port' => getenv('DATABASE_PORT'),
    'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
    'driver' => 'mysql',
);

$settings['hash_salt'] = 'pvRBd2rCKbmcGrOu7yVOum6zAur3XmTQwaBr7LPRQmat1ASsJWncO3YGoDqLk0BwE8Q3t4bzIQ';
$settings['install_profile'] = 'kinect_consulting_site';
$config_directories['sync'] = 'sites/default/files/config_TskOC_MAt1AsM1rAnDA_ByYjWl9qSAOjjno6Nc8w8WcCZGUoT1oOWxxO1Z5hXqz-NoNZvIJuFQ/sync';

return;
