<?php

class NoHasher implements Illuminate\Contracts\Hashing\Hasher{


	/**
     * Not Hash the given value.
     *
     * @param  string  $value
     * @return array   $options
     * @return string
     */
	public function make($value, array $options = array()) {
		//Devolve o valor sem Hash
        return $value;
    }


     /**
     * Check the given plain value against a hash not hashed.
     *
     * @param  string  $value
     * @param  string  $hashedValue
     * @param  array   $options
     * @return bool
     */
    public function check($value, $hashedValue, array $options = array()) {

        return $this->make($value) === $hashedValue;
    }

    /**
     * Check if the given hash has been hashed using the given options. But it will not be hashed.
     *
     * @param  string  $hashedValue
     * @param  array   $options
     * @return bool
     */
    public function needsRehash($hashedValue, array $options = array()) {
        return false;
    }


}