<?php

$params = require __DIR__ . '/params.php';

if(YII_ENV_PROD){
    $db = require __DIR__ . '/dbserver.php';
    $dbaudit = require __DIR__ . '/dbauditserver.php';
}
else{
    $db = require __DIR__ . '/db.php';
    $dbaudit = require __DIR__ . '/dbaudit.php';
}
   

$config = [
    'id' => 'Hermanos Don Bosco',
    'language' => 'es',
    'sourceLanguage'=>'es',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset', 
        '@afip'   => '@app/servicios/afip', 
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'JtYFayiSLk7Utv7hbN1M9Rx9dmxRctEg',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'class' => yii\web\User::class,
            'identityClass' => Da\User\Model\User::class,
        ],
//        'errorHandler' => [
//            'class' => '\bedezign\yii2\audit\components\web\ErrorHandler',
//            'errorAction' => 'site/error', 
//        ],     
        'i18n' => [
            'translations' => [
                '*' => [
                    'class'          => 'yii\i18n\PhpMessageSource',
                    'basePath'       => '@app/messages', // if advanced application, set @frontend/messages
                    'sourceLanguage' => 'es',
                    'fileMap'        => [
                        //'main' => 'main.php',
                    ],
                ],
                'usuario*' => [
                    'class' => \yii\i18n\PhpMessageSource::class,
                    'basePath' => '@app/messages/usuario',
                    'sourceLanguage' => 'en-US',                
                ],
            ],
        ],
        'assetManager' => [
            'forceCopy' => YII_ENV_DEV ? true : false,
        ],
        'view' => [
            'theme' => [
                'pathMap' => [
                    '@Da/User/resources/views' => '@app/views/user',
                   
                ]
            ]
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'dbaudit' => $dbaudit,
        //servicios        
        'serviceEstablecimiento'=>[
            'class'=>'app\servicios\EstablecimientoService'
        ],    
        'serviceGrupoFamiliar'=>[
            'class'=>'app\servicios\GrupoFamiliarService'
        ], 
        'serviceAlumno'=>[
            'class'=>'app\servicios\AlumnoService'
        ], 
        'serviceServicioOfrecido'=>[
            'class'=>'app\servicios\ServicioOfrecidoServices'
        ],
        'serviceDebitoAutomatico'=>[
            'class'=>'app\servicios\DebitoAutomaticoService'
        ],
        'serviceConvenioPago'=>[
            'class'=>'app\servicios\ConvenioPagoService'
        ],
        'serviceServicioAlumno'=>[
            'class'=>'app\servicios\ServicioAlumnoService'
        ],   
        'serviceFacturaAfip'=>[
            'class'=>'app\servicios\FacturaAfipService'
        ],  
        'urlManager' => [           
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'audit' => 'audit',
                '' => 'site/index',  
                '/' => 'site/index',
                'login' => 'site/login',
                
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
                '<module:\w+>/<controller:\w+>/<action:\w+>' => '<module>/<controller>/<action>',
            ],
        ],
    ],
    'modules' => [        
        'user' => [
            'class' => Da\User\Module::class,
            'enableEmailConfirmation' => false,
            'generatePasswords'       => false,  
            'allowUnconfirmedEmailLogin'=> true,
            'enableRegistration'=> false,            
            'administrators' => ['agusAdmins','agusAdmin'],  
            'classMap' => [
                'Profile' => app\models\usuarios\models\Profile::class,
                'User' => app\models\usuarios\models\User::class,
            ],
            'controllerMap' => [
                   'security' => 'app\controllers\user\SecurityController',
                   'recovery' => 'app\controllers\user\RecoveryController',
                   //'settings' => 'app\controllers\user\SettingsController',
                   
                ],            
        ],
        
        'audit' => [
            'class' => 'bedezign\yii2\audit\Audit',
            'db' => 'dbaudit',
            'ignoreActions' => ['audit/*', 'debug/*'],
            'accessRoles' => null, 
            'layout' => 'main',
            //'userIdentifierCallback' => ['app\modules\usuarios\models\SegUsuario', 'userIdentifierCallback'],
            //'userIdentifierCallback' => ['app\models\User', 'userIdentifierCallback'],
            //'userFilterCallback' => ['app\modules\usuarios\models\SegUsuario', 'userFilterCallback'],
            'panels'=>[               
                'audit/request',
                'audit/log',
                'audit/mail',
                'audit/trail',
                'audit/error',      // Links the extra error reporting functions (`exception()` and `errorMessage()`)
                'audit/extra',      // Links the data functions (`data()`)
            ]
        ],        
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['*', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => [ '*'],
    ];
}

return $config;
