<?php

namespace Phpro\Filesystem\Metadata\Image;

use Imagick;
use Phpro\Filesystem\File\FileInterface;
use Phpro\Filesystem\Metadata\AbstractMetadata;

/**
 * Class Identify
 *
 * @package Phpro\Filesystem\Metadata\Image
 */
class Identify extends AbstractMetadata
{
    /**
     * @var Imagick
     */
    protected $imagick;

    /**
     * @param $fileSystem
     * @param $imagick
     */
    public function __construct($fileSystem, $imagick)
    {
        $this->filesystem = $fileSystem;
        $this->imagick = $imagick;
    }

    /**
     * {@inheritdoc}
     * @return array
     */
    public function getMetadataForFile(FileInterface $file, array $options = [])
    {
        $this->guardFileExists($file);

        return $this->parseIdentify($file, $options);
    }

    /**
     * @param FileInterface $file
     *
     * @return array
     */
    protected function parseIdentify(FileInterface $file, array $options = [])
    {
        try {
            $image = $this->imagick;
            $image->readImage($file->getPath());
            $identifyData = $image->identifyImage();

            if (isset($options['extended']) && $options['extended']) {
                $identifyData['hasSpotColors'] = $this->hasSpotColors($image);
            }

            $image->clear();
        } catch (\Exception $e) {
            return [];
        }

        return $identifyData;
    }

    /**
     * Check if an image has spot colors.
     *
     * @param Imagick $image
     *
     * @return bool
     */
    protected function hasSpotColors(Imagick $image)
    {
        $spotColors = $image->getImageProperties('*SpotColor*');

        return count($spotColors) ? true : false;
    }
}
