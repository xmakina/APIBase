<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace APIBase;

/**
 * Description of PasswordHasher
 *
 * @author Alex
 */
class PasswordHasher {

    public static function HashPassword($password) {
        $options = [
            'cost' => 12,
        ];
        
        return password_hash($password, PASSWORD_BCRYPT, $options) . "\n";
    }

}
