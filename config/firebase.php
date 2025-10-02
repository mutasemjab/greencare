<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Firebase Project ID
    |--------------------------------------------------------------------------
    */
    'project_id' => env('FIREBASE_PROJECT_ID'),

    /*
    |--------------------------------------------------------------------------
    | Firebase Credentials Path
    |--------------------------------------------------------------------------
    | 
    | Path to your Firebase service account JSON file
    |
    */
    'credentials' => env('FIREBASE_CREDENTIALS_PATH') 
        ? base_path(env('FIREBASE_CREDENTIALS_PATH'))
        : storage_path('app/firebase/service-account.json'),

    /*
    |--------------------------------------------------------------------------
    | Firebase Credentials JSON
    |--------------------------------------------------------------------------
    |
    | Alternatively, you can provide the service account as a JSON string
    |
    */
    'credentials_json' => env('FIREBASE_CREDENTIALS_JSON'),

    /*
    |--------------------------------------------------------------------------
    | Firebase Database URL
    |--------------------------------------------------------------------------
    */
    'database_url' => env('FIREBASE_DATABASE_URL'),

    /*
    |--------------------------------------------------------------------------
    | Firebase Cloud Messaging
    |--------------------------------------------------------------------------
    */
    'fcm_server_key' => env('FCM_SERVER_KEY'),

];