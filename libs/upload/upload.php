<?php

class UploadLib
{
    const KBYTE = 1024;
    const MBYTE = 1048576;
    const GBYTE = 1073741824;

    public $files = [];
    public $isPublic = false;
    public $uploadsDir = 'uploads';
    public $maxSize = -1;
    public $extensions = [];

    public function __construct()
    {
        foreach ($_FILES as $filesIDX => $file) {
            if (
                array_key_exists('name', $file) &&
                is_array($file['name'])
            ) {
                $fileMultiple = [];
                foreach ($file as $fileIDX => $values) {
                    foreach ($values as $valuesIDX => $value) {
                        if (!array_key_exists($valuesIDX, $fileMultiple)) {
                            $fileMultiple[$valuesIDX] = [];
                        }
                        $fileMultiple[$valuesIDX][$fileIDX] = $value;
                    }
                }

                $file = $fileMultiple;
            } else {
                $file = [$file];
            }

            $this->files[$filesIDX] = $file;
        }

        $this->maxSize = 2*static::MBYTE;
    }

    public function upload(
        array $extensions = [],
        int $maxSize = -1,
        string $uploadsDir = 'uploads',
        bool $isPublic = false
    ) : array
    {
        $this->_setSettings($extensions, $maxSize, $uploadsDir, $isPublic);

        foreach ($this->files as $groupName => $groupFiles) {
            foreach ($groupFiles as $groupIDX => $file) {
                $this->files[$groupName][$groupIDX] = $this->_uploadFile(
                    $file
                );
            }
        }

        $this->_cleanEmptyFiles();

        return $this->files;
    }

    private function _cleanEmptyFiles() : void
    {
        foreach ($this->files as $groupName => $groupFiles) {
            foreach ($groupFiles as $groupIDX => $file) {
                if (
                    !array_key_exists('status', $file) ||
                    !array_key_exists('data', $file)
                ) {
                    unset($this->files[$groupName][$groupIDX]);
                }

                $file['status'] = (bool) $file['status'];
                $file['data'] = (string) $file['data'];

                if (
                    !$file['status'] &&
                    strlen($file['data']) < 0
                ) {
                    unset($this->files[$groupName][$groupIDX]);
                } else {
                    $this->files[$groupName][$groupIDX] = $file;
                }
            }
        }

        foreach ($this->files as $groupName => $groupFiles) {
            if (count($groupFiles) < 1) {
                unset($this->files[$groupName]);
            }
        }
    }

    private function _uploadFile(
        array $file = []
    ) : array
    {
        if (
            !array_key_exists('size', $file) ||
            !array_key_exists('name', $file) ||
            !array_key_exists('tmp_name', $file) ||
            !array_key_exists('error', $file) ||
            $file['error'] != UPLOAD_ERR_OK ||
            $file['size'] < 1
        ) {
            return [
                'status' => true,
                'data'   => NULL
            ];
        }

        if ($file['size'] > $this->maxSize) {
            return [
                'status' => false,
                'data'   => 'File Is Too Large'
            ];
        }

        $fileExtension = $this->_getExtensionFromName($file['name']);

        if (!in_array($fileExtension, $this->extensions)) {
            return [
                'status'    => false,
                'data' => 'Bad File Type'
            ];
        }

        $fileName = md5($file['name']).'.'.$fileExtension;

        if ($this->isPublic) {
            $fileDir = __DIR__.'/../../../../public/'.$this->uploadsDir;
        } else {
            $fileDir = __DIR__.'/../../../'.$this->uploadsDir;
        }

        if (
            !file_exists($fileDir) ||
            !is_dir($fileDir)
        ) {
            throw new Exception('Upload Directory Is Not Exist');
        }

        $uploadedFilePath = $fileDir.'/'.$fileName;

        $status = move_uploaded_file($file['tmp_name'], $uploadedFilePath);

        if (!$status) {
            return [
                'status' => false,
                'data'   => 'Can Not Save File'
            ];
        }

        return [
            'status' => true,
            'data'   => $fileName
        ];
    }

    private function _getExtensionFromName(string $name = '') : string
    {
        $extension = '';
        $name = mb_convert_case($name, MB_CASE_LOWER);

        if (preg_match('/^(.*?)\.([a-z]+)$/su', $name)) {
            $extension = preg_replace('/^(.*?)\.([a-z]+)$/su', '$2', $name);
        }

        return $extension;
    }

    private function _setSettings(
        array $extensions = [],
        int $maxSize = -1,
        string $uploadsDir = 'uploads',
        bool $isPublic = false
    ) : void
    {
        if (count($extensions) < 1) {
            throw new Exception('Extentions For Uploaded Files Not Set!');
        }

        $this->extensions = $extensions;

        if ($maxSize > 1) {
            $this->maxSize = $maxSize;
        }

        if (strlen($uploadsDir) > 1) {
            $this->uploadsDir = $uploadsDir;
        }

        $this->isPublic = $isPublic;
    }
}
?>