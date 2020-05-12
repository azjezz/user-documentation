<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the BSD-style license found in the
 *  LICENSE file in the root directory of this source tree. An additional grant
 *  of patent rights can be found in the PATENTS file in the same directory.
 *
 */

use namespace Nuxed\Contract\Http\Message;

trait XHPGetRequest {
  require extends :x:element;

  protected function getRequest(): Message\IServerRequest {
    $x = $this->getContext('IServerRequest');
    invariant(
      $x is Message\IServerRequest,
      '%s is not a server request',
      gettype($x).' ('.get_class($x).')',
    );
    return $x;
  }
}
