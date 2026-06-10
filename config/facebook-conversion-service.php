<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Facebook Pixel ID
    |--------------------------------------------------------------------------
    |
    | Your Facebook Pixel ID from Facebook Events Manager.
    |
    */
    'pixel_id' => env('FACEBOOK_PIXEL_ID', ''),

    /*
    |--------------------------------------------------------------------------
    | Facebook Conversion API Token
    |--------------------------------------------------------------------------
    |
    | The access token generated from Facebook Events Manager > Settings >
    | Conversion API > Generate Access Token.
    |
    */
    'access_token' => env('FACEBOOK_PIXEL_API', ''),

    /*
    |--------------------------------------------------------------------------
    | Test Event Code
    |--------------------------------------------------------------------------
    |
    | Optional test event code for validating events in Facebook Events Manager
    | before going live. Remove or leave empty in production.
    |
    */
    'test_event_code' => env('FACEBOOK_TEST_EVENT_CODE', ''),

    /*
    |--------------------------------------------------------------------------
    | Facebook Graph API Version
    |--------------------------------------------------------------------------
    |
    | The version of the Facebook Graph API to use for Conversion API requests.
    |
    */
    'api_version' => env('FACEBOOK_API_VERSION', 'v21.0'),

    /*
    |--------------------------------------------------------------------------
    | Enable / Disable
    |--------------------------------------------------------------------------
    |
    | Master switch for Facebook tracking. When disabled, no events are sent
    | to either Pixel or Conversion API.
    |
    */
    'enabled' => env('FACEBOOK_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Queue Support
    |--------------------------------------------------------------------------
    |
    | When enabled, Conversion API events are dispatched to the queue instead
    | of being sent synchronously. This prevents blocking HTTP requests.
    |
    */
    'queue_enabled' => env('FACEBOOK_QUEUE', false),

    /*
    |--------------------------------------------------------------------------
    | Queue Connection
    |--------------------------------------------------------------------------
    |
    | The queue connection to use for dispatching events. Leave null to use
    | the default queue connection.
    |
    */
    'queue_connection' => env('FACEBOOK_QUEUE_CONNECTION', null),

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | When enabled, all requests, responses, and errors will be logged to the
    | 'facebook_conversion' log channel.
    |
    */
    'logging_enabled' => env('FACEBOOK_LOGGING', true),

    /*
    |--------------------------------------------------------------------------
    | Log Channel
    |--------------------------------------------------------------------------
    |
    | The log channel to use for Facebook Conversion API logs. This will be
    | registered dynamically if it does not exist.
    |
    */
    'log_channel' => env('FACEBOOK_LOG_CHANNEL', 'facebook_conversion'),

    /*
    |--------------------------------------------------------------------------
    | Timeout (seconds)
    |--------------------------------------------------------------------------
    |
    | The timeout for HTTP requests to the Facebook Graph API.
    |
    */
    'timeout' => env('FACEBOOK_TIMEOUT', 5),

    /*
    |--------------------------------------------------------------------------
    | Retry Attempts
    |--------------------------------------------------------------------------
    |
    | Number of times to retry failed Conversion API requests.
    |
    */
    'retry_attempts' => env('FACEBOOK_RETRY_ATTEMPTS', 3),

    /*
    |--------------------------------------------------------------------------
    | Retry Delay (milliseconds)
    |--------------------------------------------------------------------------
    |
    | Delay between retry attempts in milliseconds.
    |
    */
    'retry_delay' => env('FACEBOOK_RETRY_DELAY', 100),

];
