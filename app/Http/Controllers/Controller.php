<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param {String} folder
     * @param {Object} laravel_image_resource, the resource
     * @param {Array} versinos
     */
    public function saveImageVersions($folder, $laravel_image_resource, $versions)
    {
        //Make UUID
        $uuid = Str::uuid()->toString();

        // Ensure the folder has a trailing slash
        $folder = rtrim($folder, '/') . '/';

        // Ensure the directory exists
        if (!File::exists(public_path($folder))) {
            File::makeDirectory(public_path($folder), 0755, true);
        }

        //Make the versions
        foreach ($versions as $key => $version) {
            $filename = $uuid . "_" . $version['name'] . "." . "jpg";
            if (isset($version['w']) && isset($version['h'])) {
                $img = Image::make($laravel_image_resource->getRealPath())->fit($version['w'], $version['h']);
                $img->save(public_path($folder) . $filename);
            } else {
                //Original image
                $laravel_image_resource->move(public_path($folder), $filename);
            }
        }

        return $uuid;
    }
}
