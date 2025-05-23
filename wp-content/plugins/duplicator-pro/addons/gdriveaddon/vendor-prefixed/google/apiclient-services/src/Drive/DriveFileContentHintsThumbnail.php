<?php

/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */
namespace VendorDuplicator\Google\Service\Drive;

class DriveFileContentHintsThumbnail extends \VendorDuplicator\Google\Model
{
    /**
     * @var string
     */
    public $image;
    /**
     * @var string
     */
    public $mimeType;
    /**
     * @param string
     */
    public function setImage($image)
    {
        $this->image = $image;
    }
    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }
    /**
     * @param string
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;
    }
    /**
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }
}
// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DriveFileContentHintsThumbnail::class, 'VendorDuplicator\Google_Service_Drive_DriveFileContentHintsThumbnail');
