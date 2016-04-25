<?php

namespace Lym125\UploadFile;

use Illuminate\Http\UploadedFile;
use Illuminate\Contracts\Filesystem\Filesystem;

class UploadFile
{
    /**
     * Filesystem 实例
     *
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * 上传文件相关配置
     *
     * @var array
     */
    protected $configs = [];

    /**
     * 创建一个新的 UploadFile 实例
     *
     * @param  \Illuminate\Contracts\Filesystem\Filesystem $filesystem
     * @param  array $configs
     * @return void
     */
    public function __construct(Filesystem $filesystem, array $configs = [])
    {
        $this->filesystem = $filesystem;

        $this->configs = $configs;
    }
    

    /**
     * 文件上传处理
     *
     * @param  \Illuminate\Http\UploadedFile $file
     * @param  array $configs
     * @return array
     */
    public function handle($file, $configs = [])
    {
        if (is_array($file)) {
            $jsonFormatDataArr = [];

            foreach ($file as $uploadedFile) {
                $jsonFormatDataArr[] = $this->handle($uploadedFile, $configs);
            }

            return $jsonFormatDataArr;
        } else {
            try {
                $configs = array_merge($this->configs, $configs);

                if (is_null($file)) {
                    throw new UploadFileException('Upload file can not be null!');
                }

                if (! $this->isUploadedFile($file)) {
                    throw new UploadFileException('Upload file is not a \Illuminate\Http\UploadedFile instance!');
                }

                $jsonFormatData = $this->getJosnFormatData($file);

                if (! $this->isValidUploadFileSize($jsonFormatData['size'], $configs['max_size'])) {
                    throw new UploadFileException('The upload file may not be greater than ' . $this->size2mb($configs['max_size']) . '.');
                }

                if (! $this->isValidUploadFileExtension($jsonFormatData['extension'], $configs['extensions'])) {
                    throw new UploadFileException("The upload file suffix may only " . implode(', ', $configs['extensions']) . '.');
                }

                if (! $this->isValidUploadFileMimeType($jsonFormatData['mime_type'], $configs['mime_types'])) {
                    throw new UploadFileException('The upload file mime-type may only ' . implode(', ', $configs['mime_types']) . '.');
                }

                $jsonFormatData['savename'] = $this->makeFilename($file);

                $jsonFormatData['savepath'] = $this->getSavepath($configs['directory'], $configs['timestamps']);

                if (empty($jsonFormatData['savepath'])) {
                    $jsonFormatData['fullpath'] = $jsonFormatData['savename'];
                } else {
                    $jsonFormatData['fullpath'] = "{$jsonFormatData['savepath']}/{$jsonFormatData['savename']}";
                }
                
                $result = $this->filesystem->put(
                    $jsonFormatData['fullpath'],
                    file_get_contents($file->getRealPath())
                );

                if (! $result) {
                    throw new UploadFileException('Unknown reason cause upload failed!');
                }

                $jsonFormatData['status'] = 'success';
            } catch (UploadFileException $e) {
                $jsonFormatData['status'] = 'failure';

                $jsonFormatData['error'] = $e->getMessage();
            }

            return $jsonFormatData;
        }
    }

    /**
     * 获取上传文件存储目录
     *
     * @param  string $directory
     * @param  bool $timestamps
     * @return string
     */
    protected function getSavepath($directory, $timestamps = true)
    {
        if (! $timestamps) {
            return $directory;
        }

        return (!empty($directory) ? "{$directory}/" : "") . date('Y').'/'.date('m').'/'.date('d');
    }

    /**
     * 生成文件名
     *
     * @param  \Illuminate\Http\UploadedFile $file
     * @return string
     */
    protected function makeFilename(UploadedFile $file)
    {
        return md5_file($file->getRealPath()) . "." . $file->getClientOriginalExtension();
    }

    /**
     * 上传文件信息
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @return array
     */
    protected function getJosnFormatData(UploadedFile $file)
    {
        return [
            'original_name' => $file->getClientOriginalName(),
            'mime_type'     => $file->getClientMimeType(),
            'extension'     => $file->getClientOriginalExtension(),
            'size'          => $file->getClientSize(),
        ];
    }

    /**
     * 是否是允许的媒体类型
     *
     * @param  string $mimeType
     * @param  array  $allowedMimeTypes
     * @return bool
     */
    protected function isValidUploadFileMimeType($mimeType, array $allowedMimeTypes = ['*'])
    {
        return in_array($mimeType, $allowedMimeTypes) || $allowedMimeTypes == ['*'];
    }

    /**
     * 是否是允许的文件后缀
     *
     * @param  string $extension
     * @param  array  $allowedExtensions
     * @return bool
     */
    protected function isValidUploadFileExtension($extension, array $allowedExtensions = ['*'])
    {
        return in_array($extension, $allowedExtensions) || $allowedExtensions == ['*'];
    }

    /**
     * 是否是允许的大小
     *
     * @param  int $size
     * @param  int $maxSize
     * @return bool
     */
    protected function isValidUploadFileSize($size, $maxSize = 0)
    {
        return $maxSize >= $size || $maxSize === 0;
    }

    /**
     * 判断是否是 UploadedFile 实例
     *
     * @param  mixed $file
     * @return bool
     */
    protected function isUploadedFile($file) {
        return $file instanceof UploadedFile;
    }

    /**
     * 转换数字为文件长度格式
     *
     * @param numeric $size
     * @param int $digits
     * @return string
     */
    protected function size2mb($size, $digits = 2)
    {
        //单位数组，是必须1024进制依次
        $unit = array('', 'K', 'M', 'G', 'T', 'P');

        //对数的基数
        $base = 1024;

        //字节数对1024取对数，值向下取整。
        $i = floor(log($size, $base));

        return round($size / pow($base, $i), $digits) . ' ' . $unit[$i] . 'B';
    }
}