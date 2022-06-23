<?php

namespace app\controllers;

use app\core\Controller;
use Cloudinary\Api\ApiUtils;
use Cloudinary\Configuration\Configuration;

require_once '../config/config.php';

Configuration::instance([
    "cloud" => [
        "cloud_name" => "veinsmoke",
        "api_key" => CLOUDINARY_API_KEY,
        "api_secret" => CLOUDINARY_API_SECRET,
        "url" => [
            "secret" => true
        ]
    ]
]);

class Cloudinary extends Controller
{

    public function getSignature(): bool|string
    {
        return json_encode($this->cloudinarySign(["folder" => "thewatchparty"]));
    }


    function cloudinarySign($data = []): array
    {
        $timestamp = strtotime("now");
        $signature = ApiUtils::signParameters([...$data, "timestamp" => $timestamp], CLOUDINARY_API_SECRET);
        return ["signature" => $signature, "timestamp" => $timestamp];
    }
}