<?php

    return [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=MariaDB-11.2;dbname=api_books',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8',
    
        // Schema cache options (for production environment)
        'enableSchemaCache' => true,
        'schemaCacheDuration' => 60,
        'schemaCache' => 'cache',
    ];



