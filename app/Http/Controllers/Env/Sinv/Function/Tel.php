<?php

namespace  App\Http\Controllers\Env\Sinv\Function;

Class Tel 
    {
        public static function standardizePhoneNumber($tel)
        {
            $cleanedNumber = preg_replace('/[^0-9]/', '', $tel);
            return $cleanedNumber;
        }
    }