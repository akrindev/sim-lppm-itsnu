<?php

namespace App\Support\MediaLibrary;

use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class CustomPathGenerator implements PathGenerator
{
    public function getPath(Media $media): string
    {
        return $this->getBasePath($media).'/';
    }

    public function getPathForConversions(Media $media): string
    {
        return $this->getBasePath($media).'/conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getBasePath($media).'/responsive-images/';
    }

    protected function getBasePath(Media $media): string
    {
        $collection = $media->collection_name;
        $model = $media->model;

        $user = null;

        // Identifikasi User dari Model
        if ($model instanceof \App\Models\User) {
            $user = $model;
        } elseif (method_exists($model, 'submitter')) {
            $user = $model->submitter;
        } elseif (method_exists($model, 'user')) {
            $user = $model->user;
        }

        if ($user) {
            // Cek identity_id (NIDN/NIK) dari relasi identity
            // Kita coba muat relasi jika belum ada
            if (! $user->relationLoaded('identity')) {
                $user->load('identity');
            }

            $identityId = $user->identity->identity_id ?? 'no-id';
            $userName = Str::slug($user->name);
            $userFolder = "{$identityId}-{$userName}";
        } else {
            // Fallback jika tidak ada konteks user
            $modelName = Str::slug(class_basename($media->model_type));
            $modelId = is_string($media->model_id) ? substr($media->model_id, 0, 8) : $media->model_id;
            $userFolder = "{$modelName}-{$modelId}";
        }

        return "{$collection}/{$userFolder}/{$media->id}";
    }
}
