<?php

return [

    /*
    | The disk that stores uploaded images. "public" keeps files on this app.
    | "s3" uses the S3 disk, including a local SeaweedFS endpoint.
    |
    | ASSETS_URL is the origin the browser can open. APP_URL may include the
    | container port, which the browser cannot use for these files.
    */
    'disk' => env('ASSETS_DISK', 'public'),

    /*
    | Originals can move to a cheaper disk later. The site serves the display
    | image and the thumb, never this copy.
    */
    'archive_disk' => env('ASSETS_ARCHIVE_DISK', env('ASSETS_DISK', 'public')),

    'max_kilobytes' => (int) env('ASSETS_MAX_KILOBYTES', 102400),

    'display_max_edge' => 2000,

    'thumb_max_edge' => 640,

    'display_quality' => 82,

    'thumb_quality' => 75,

    'display_max_bytes' => 2_000_000,

];
