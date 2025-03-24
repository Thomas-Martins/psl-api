<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageUploadService
{
    /**
     * Upload une image dans un dossier spécifique sur le disque public et retourne son chemin relatif.
     *
     * @param UploadedFile $file   L'image à uploader.
     * @param string       $folder Le dossier de destination (ex: 'users', 'products').
     * @param string       $prefix Préfixe pour le nom du fichier (ex: 'user').
     * @return string              Le chemin relatif de l'image (ex: "users/user_1612345678.jpg").
     */
    public function upload(UploadedFile $file, string $folder, string $prefix = '')
    {
        // Créer un nom de fichier unique
        $filename = ($prefix ? $prefix . '_' : '') . time() . '.' . $file->getClientOriginalExtension();

        // Stocker le fichier sur le disque "public" dans le dossier spécifié
        $path = Storage::disk('public')->putFileAs($folder, $file, $filename);

        // Le chemin retourné est relatif à storage/app/public (ex: "users/user_1612345678.jpg")
        return $path;
    }
}
