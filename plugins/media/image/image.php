<?php
class ImagePlugin
{
    var $imageFile;
    var $imageDir;
    var $imageName = 'image';
    var $sizes = [
        'thumbnail' => [
            'height' => 64,
            'width' => 64,
            'low_quality' => true,
            'prefix' => 'thumb'
        ],
        'post' => [
            'height' => NULL,
            'width' => 200,
            'low_quality' => false,
            'prefix' => 'p'
        ]
    ];

    public function setImage(
        string $imageFile = '',
        string $imageDir = '',
        string $imageName = '',
        array  $sizes = []
    ) : void
    {
        if (!strlen($imageDir) > 0) {
            $imageDir = getcwd();
        }

        $this->imageDir = $imageDir;

        if (strlen($imageFile) > 0) {
            $this->imageFile = "{$imageDir}/{$imageFile}";
        }

        if (strlen($imageName) > 0) {
            $this->imageName = $imageName;
        }

        if (!count($sizes) > 0) {
            throw new Exception('Image Sizes Not Set');
        }

        $this->sizes = $sizes;
    }

    public function imageGen(array $sizes = []) : void
    {
        foreach($sizes as $size){
            $this->_imageResize($size);
        }
    }

    private function _imageResize(string $size = '') : void
    {
        $size = $this->_getImageSize($size);
        $imageObject = $this->_getImageObject();

        $originWidth = (int) $imageObject->getImageGeometry()['width'];
        $originHeight = (int) $imageObject->getImageGeometry()['height'];

        if ($size['width'] == NULL || $size['height'] == NULL) {
            $size = $this->_calculateSize($size, $originWidth, $originHeight);
        } else {
            $imageObject = $this->_changeImageRatio(
                $imageObject,
                $size,
                $originWidth,
                $originHeight
            );
        }

        $imageObject->resizeImage(
            $size['width'],
            $size['height'],
            Imagick::FILTER_LANCZOS,
            1
        );

        $this->_saveImage($imageObject, $size['prefix'], $size['low_quality']);

        $imageObject->clear();
        $imageObject->destroy();
    }

    private function _getImageSize(string $size = '') : array
    {
        if (!array_key_exists($size, $this->sizes)) {
            throw new Exception("Invalid Image Size \"{$size}\"");
        }

        return $this->sizes[$size];
    }

    private function _getImageObject() : Object
    {
        $imageObject = new \Imagick();
        $imageObject->readImage($this->imageFile);
        $imageObject->setImageFormat("png");

        return $imageObject;
    }

    private function _calculateSize(
        array $size = [],
        int $originWidth = 0,
        int $originHeight = 0
    ) : array
    {
        $size['width'] = (int) $size['width'];
        $size['height'] = (int) $size['height'];

        if ($size['width'] < 1 && $size['height'] < 1) {
            throw new Exception("Image Size \"{$size}\" Have Bad Format");
        }

        if ($originWidth < 1 || $originHeight < 1) {
            throw new Exception('Input Image Have Bad Format');
        }

        if ($size['width'] < 1) {
            $size['width'] = ($originWidth / $originHeight) * $size['height'];
            $size['width'] = (int) $size['width'];

            return $size;
        }

        $size['height'] = ($originHeight / $originWidth) * $size['width'];
        $size['height'] = (int) $size['height'];

        return $size;
    }

    private function _changeImageRatio(
        Object $imageObject = NULL,
        array $size = NULL,
        int $originWidth = 0,
        int $originHeight = 0
    ) : Object
    {
        $size['width'] = (int) $size['width'];
        $size['height'] = (int) $size['height'];

        if ($size['width'] < 1 && $size['height'] < 1) {
            throw new Exception("Image Size \"{$size}\" Have Bad Format");
        }

        if ($originWidth < 1 || $originHeight < 1) {
            throw new Exception('Input Image Have Bad Format');
        }

        if ($imageObject == NULL) {
            throw new Exception('Image Object Missing');
        }

        $newWidth = $originWidth;
        $newHeight = (int) ($size['height'] / $size['width']) * $newWidth;

        if ($newHeight > $originHeight) {
            $newHeight = $originHeight;
            $newWidth = (int) ($size['width'] / $size['height']) * $newHeight;
        }

        if ($newWidth < $originWidth) {
            $positionX = (int) (($originWidth-$newWidth) / 2);
        } else {
            $positionX = 0;
        }

        if ($newHeight < $originHeight) {
            $positionY = (int) (($originHeight-$newHeight) / 2);
        } else {
            $positionY = 0;
        }

        $positionX = $positionX > 0 ? $positionX + 1 : 0;
        $positionY = $positionY > 0 ? $positionY + 1 : 0;

        $imageObject->cropImage($newWidth, $newHeight, $positionX, $positionY);

        return $imageObject;
    }

    private function _saveImage(
        Object $imageObject = NULL,
        string $prefix = '',
        bool $isLowQuality = false
    ) : void
    {
        if (strlen($prefix) < 1) {
            throw new Exception('Image File Prefix Has Bad Format');
        }

        if ($imageObject == NULL) {
            throw new Exception('Image Object Missing');
        }

        if ($isLowQuality) {
            $imageObject->setImageFormat("gif");
            $imageObject->setImageCompressionQuality(10);
            $imageFilePath = $this->_getImageFilePath($prefix, 'gif');
        } else {
            $imageObject->setImageCompressionQuality(90);
            $imageFilePath = $this->_getImageFilePath($prefix, 'png');
        }

        $imageObject->setImageCompression(Imagick::COMPRESSION_ZIP);
        $imageObject->writeImage($imageFilePath);
        //chmod($imageFilePath, 0755);
    }

    private function _getImageFilePath(
        string $prefix = '',
        string $extension = 'png'
    ) : string
    {
        if (strlen($prefix) < 1) {
            throw new Exception('Image File Prefix Has Bad Format');
        }

        if (strlen($extension) < 1) {
            throw new Exception('Image File extension Has Bad Format');
        }

        return $this->imageDir.'/'.$this->imageName.'-'.$prefix.'.'.$extension;
    }
}
?>