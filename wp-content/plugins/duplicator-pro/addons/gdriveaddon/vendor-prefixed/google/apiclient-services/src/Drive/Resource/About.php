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
namespace VendorDuplicator\Google\Service\Drive\Resource;

use VendorDuplicator\Google\Service\Drive\About as AboutModel;
/**
 * The "about" collection of methods.
 * Typical usage is:
 *  <code>
 *   $driveService = new Google\Service\Drive(...);
 *   $about = $driveService->about;
 *  </code>
 */
class About extends \VendorDuplicator\Google\Service\Resource
{
    /**
     * Gets information about the user, the user's Drive, and system capabilities.
     * (about.get)
     *
     * @param array $optParams Optional parameters.
     * @return AboutModel
     */
    public function get($optParams = [])
    {
        $params = [];
        $params = array_merge($params, $optParams);
        return $this->call('get', [$params], AboutModel::class);
    }
}
// Adding a class alias for backwards compatibility with the previous class name.
class_alias(About::class, 'VendorDuplicator\Google_Service_Drive_Resource_About');
