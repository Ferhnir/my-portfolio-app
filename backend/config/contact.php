<?php

return [

    /*
    | The address that receives contact form mail. Falls back to the app sender.
    */
    'inbox' => env('CONTACT_INBOX_ADDRESS', env('MAIL_FROM_ADDRESS', 'hello@example.com')),

    'min_seconds' => 3,

    'max_seconds' => 60 * 60,

    'per_minutes' => 5,

];
