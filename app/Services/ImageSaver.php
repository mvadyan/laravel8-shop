<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ImageSaver
{
    /**
     * Сохраняет изображение при создании или редактировании категории,
     * бренда или товара; создает два уменьшенных изображения.
     * @param $request
     * @param $item
     * @param $dir
     * @return string|null
     */
    public function upload($request, $item, $dir)
    {
        $name = $item->image ?? null;

        if ($item && $request->remove) {
            $this->remove($item, $dir);
            $name = null;
        }

        $source = $request->file('image');

        if ($source) {
            if ($item && $item->image) {
                $this->remove($item, $dir);
            }

            $ext = $source->extension();
            $path = $source->store('catalog/' . $dir . '/source', 'public');
            $path = Storage::disk('public')->path($path); // абсолютный путь
            $name = basename($path); // имя файла

            $dst = 'catalog/' . $dir . '/image/';
            $this->resize($path, $dst, 600, 300, $ext);
            // создаем уменьшенное изображение 300x150px, качество 100%
            $dst = 'catalog/' . $dir . '/thumb/';
            $this->resize($path, $dst, 300, 150, $ext);
        }

        return $name;
    }

    /**
     * Создает уменьшенную копию изображения
     * @param $src
     * @param $dst
     * @param $width
     * @param $height
     * @param $ext
     */
    private function resize($src, $dst, $width, $height, $ext)
    {
        $image = Image::make($src)
            ->heighten($height)
            ->resizeCanvas($width, $height, 'center', false, 'eeeeee')
            ->encode($ext, 100);

        $name = basename($src);
        Storage::disk('public')->put($dst . $name, $image);
        $image->destroy();
    }

    /**
     * Удаляет изображение при удалении категории, бренда или товара
     * @param $item
     * @param $dir
     */
    public function remove($item, $dir)
    {
        $old = $item->image;

        if ($old) {
            Storage::disk('public')->delete('catalog/' . $dir . '/source/' . $old);
            Storage::disk('public')->delete('catalog/' . $dir . '/image/' . $old);
            Storage::disk('public')->delete('catalog/' . $dir . '/thumb/' . $old);
        }
    }

}
