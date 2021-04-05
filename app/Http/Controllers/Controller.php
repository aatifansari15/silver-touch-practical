<?php

namespace App\Http\Controllers;

use Image;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
	 * Create a thumbnail of specified size
	 *
	 * @param string $path path of thumbnail
	 * @param int $width
	 * @param int $height
	 */
	public function __createThumbnail($path, $width, $height)
	{
		// For aspect ratio
	    /*$img = Image::make($path)->resize($width, $height, function ($constraint) {
	        $constraint->aspectRatio();
	    });
	    $img->save($path);*/

	    // For hard code
	    $img = Image::make($path)->resize($width, $height)->save($path);
	}

	public function __removeFileFromFolder($ImageArray)
	{
		foreach ($ImageArray as $key => $image) {
            $filePath = public_path('uploads') . '/' . $image;
            if (file_exists($filePath)) {
                $thumbFilePath = public_path('uploads/thumbnail') . '/' . $image;
                if (file_exists($thumbFilePath)) {
                    unlink($thumbFilePath);
                }
                unlink($filePath);
            }
        }
	}
}
