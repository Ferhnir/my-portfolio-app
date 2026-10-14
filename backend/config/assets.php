<?php

return [

    /*
    | The disk that stores uploaded images. "public" keeps files on this app.
    | "s3" uses the S3 disk, including a local SeaweedFS endpoint.
    */
    'disk' => env('ASSETS_DISK', 'public'),

    'max_kilobytes' => (int) env('ASSETS_MAX_KILOBYTES', 5120),

];
