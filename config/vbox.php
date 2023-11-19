<?php

return [
    "host" => env("VBOX_HOST", "127.0.0.1"),
    "user" => env("VBOX_USER", "vbox"),
    "password" => env("VBOX_PASSWORD", "vbox"),
    "images" => env("VBOX_IMAGES", storage_path("app/images"))
];
