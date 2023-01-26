<?php

/**
 * Register application modules
 */
$application->registerModules(array(
    'backend' => array(
        'className' => 'Forexceccom\Backend\Module',
        'path' => __DIR__ . '/../apps/backend/Module.php'
    )
));
