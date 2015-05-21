<?php

/**
 *  Copyright (c) 2010 Etsy
 *
 *   Permission is hereby granted, free of charge, to any person
 *   obtaining a copy of this software and associated documentation
 *   files (the "Software"), to deal in the Software without
 *   restriction, including without limitation the rights to use,
 *   copy, modify, merge, publish, distribute, sublicense, and/or sell
 *   copies of the Software, and to permit persons to whom the
 *   Software is furnished to do so, subject to the following
 *   conditions:
 *
 *   The above copyright notice and this permission notice shall be
 *   included in all copies or substantial portions of the Software.
 *
 *   THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 *   EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 *   OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 *   NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 *   HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 *   WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 *   FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 *   OTHER DEALINGS IN THE SOFTWARE.
 */

/**
 * This randomizer hashes a (subject ID, test key) pair.
 *
 */
class AB2_Selector_HashRandomizer {
    private $_algo = 'sha256';
    private $_testKey;
    private $_testKeyHash;

    /**
     * @param string $testKey
     * @return void
     */
    public function __construct($testKey) {
        $this->_testKey = $testKey;
    }

    /**
     * Map a subject (user) ID to a value in the half-open interval [0, 1).
     *
     * @param  $subjectID
     * @return float
     */
    public function randomize($subjectID) {
        return !is_null($subjectID) ? $this->hash1($subjectID) : 0;
    }

    private function hash1($subjectID) {
        $h = hash($this->_algo, "$this->_testKey-$subjectID");
        return $this->mapHex($h);
    }

    private function hash2($subjectID) {
        $h = hash($this->_algo, "$this->_testKey-$subjectID");
        $h = hash($this->_algo, $h);
        $w = $this->mapHex($h);
        return $w;
    }

    private function hash3($subjectID) {
        if (is_null($this->_testKeyHash)) {
            $this->_testKeyHash = substr(hash($this->_algo, $this->_testKey), 0, 24);
        }
        $h = hash($this->_algo, "$this->_testKeyHash-$subjectID");
        $h = hash($this->_algo, $h);
        $w = $this->mapHex($h);
        return $w;
    }

    /**
     * Map a hex value to the half-open interval [0, 1) while
     * preserving uniformity of the input distribution.
     *
     * @param string $hex a hex string
     * @return float
     */
    private function mapHex($hex) {
        $len = min(40, strlen($hex));
        $vMax = 1 << $len;
        $v = 0;
        for ($i = 0; $i < $len; $i++) {
            $bit = hexdec($hex[$i]) < 8 ? 0 : 1;
            $v = ($v << 1) + $bit;
        }
        $w = $v / $vMax;
        return $w;
    }
}
