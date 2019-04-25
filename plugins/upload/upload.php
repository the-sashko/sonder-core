<?php
/**
 * Plugin For Uploading Files
 */
class UploadPlugin
{
    /**
     * @var int KiloByte Value In Bytes
     */
    const KBYTE = 1024;

    /**
     * @var int MegaByte Value In Bytes
     */
    const MBYTE = 1048576;

    /**
     * @var int GigaByte Value In Bytes
     */
    const GBYTE = 1073741824;

    /**
     * @var array List Of Uploding Files
     */
    public $files = [];

    /**
     * @var bool Is Uploaded Files Will Accessed From Web
     */
    public $isPublic = FALSE;

    /**
     * @var string Uploads Directory Name
     */
    public $uploadsDir = 'uploads';

    /**
     * @var int Max Allowed Size Of File (Bytes)
     */
    public $maxSize = -1;

    /**
     * @var array List Of Allowed File Extensions
     */
    public $extensions = [];

    public function __construct()
    {
        foreach ($_FILES as $filesIDX => $file) {
            if (
                !array_key_exists('name', $file) ||
                !is_array($file['name'])
            ) {
                $this->files[$filesIDX] = [$file];

                continue;
            }

            $fileMultiple = [];
            foreach ($file as $fileIDX => $values) {
                foreach ($values as $valuesIDX => $value) {
                    if (!array_key_exists($valuesIDX, $fileMultiple)) {
                        $fileMultiple[$valuesIDX] = [];
                    }

                    $fileMultiple[$valuesIDX][$fileIDX] = $value;
                }
           }

           $this->files[$filesIDX] = $fileMultiple;
        }

        $this->maxSize = 2 * static::MBYTE;
    }

    /**
     * Upload Files From Request To Server
     *
     * @param array  $extensions List Of Allowed Files Extensions
     * @param int    $maxSize    Max Allowed Size Of File (Bytes)
     * @param string $uploadsDir Path To Directory Of Uploaded Files
     * @param bool   $isPublic   Is Uploaded File Will Accessed From Web
     *
     * @return array List Of Uploaded Files
     */
    public function upload(
        array  $extensions = [],
        int    $maxSize    = -1,
        string $uploadsDir = 'uploads',
        bool   $isPublic   = FALSE
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

    /**
     * Remove Emply Elements From Array Of Uploaded Files
     */
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
                $file['data']   = (string) $file['data'];

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

    /**
     * Upload Single File
     *
     * @param array $file Meta Data Of Input File
     *
     * @return array Meta Data Of Uploaded File
     */
    private function _uploadFile(
        array $file = []
    ) : array
    {
        if (
            !array_key_exists('size', $file) ||
            !array_key_exists('name', $file) ||
            !array_key_exists('tmp_name', $file) ||
            !array_key_exists('error', $file) ||
            UPLOAD_ERR_OK !== $file['error'] ||
            $file['size'] < 1
        ) {
            return [
                'status' => TRUE,
                'data'   => NULL
            ];
        }

        if ($file['size'] > $this->maxSize) {
            return [
                'status' => FALSE,
                'data'   => 'File Is Too Large'
            ];
        }

        $fileExtension = $this->_getExtensionFromName($file['name']);

        if (!in_array($fileExtension, $this->extensions, TRUE)) {
            return [
                'status' => FALSE,
                'data'   => 'Bad File Type'
            ];
        }

        $fileName = md5($file['name']).'.'.$fileExtension;

        $fileDir = __DIR__.'/../../../'.$this->uploadDir;

        if ($this->isPublic) {
            $fileDir = __DIR__.'/../../../../public/'.$this->uploadsDir;
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
                'status' => FALSE,
                'data'   => 'Can Not Save File'
            ];
        }

        return [
            'status' => TRUE,
            'data'   => $fileName
        ];
    }

    /**
     * Get Extension By File Name
     *
     * @param string $name Name Of File
     *
     * @return string Extension
     */
    private function _getExtensionFromName(string $name = '') : string
    {
        $extension = '';
        $name      = mb_convert_case($name, MB_CASE_LOWER);

        if (preg_match('/^(.*?)\.([a-z]+)$/su', $name)) {
            $extension = preg_replace('/^(.*?)\.([a-z]+)$/su', '$2', $name);
        }

        return $extension;
    }

    /**
     * Set Settings Of Upload Files
     *
     * @param array  $extensions List Of Allowed Files Extensions
     * @param int    $maxSize    Max Allowed Size Of File (Bytes)
     * @param string $uploadsDir Path To Directory Of Uploaded Files
     * @param bool   $isPublic   Is Uploaded File Will Accessed From Web
     */
    private function _setSettings(
        array  $extensions = [],
        int    $maxSize    = -1,
        string $uploadsDir = 'uploads',
        bool   $isPublic   = FALSE
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
