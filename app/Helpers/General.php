<?php

function uploadImage($folder, $image)
{
    $extension = strtolower($image->getClientOriginalExtension());

    $filename = time() . '_' . uniqid() . '.' . $extension;

    $destinationPath = public_path($folder);

    if (!file_exists($destinationPath)) {
        mkdir($destinationPath, 0755, true);
    }

    $image->move($destinationPath, $filename);

    return $filename;
}


function uploadFile($file, $folder)
{
    $path = $file->store($folder);
    return $path;
}



