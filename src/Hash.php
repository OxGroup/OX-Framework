<?php

/**
 * Created by OxGroup.
 * User: Александр
 * Date: 02.06.2015
 * Time: 22:16
 */
namespace Ox;

/**
 * Class Hash
 *
 * @package Ox
 */
class Hash
{
    private $rounds;
    
    /**
     * Hash constructor.
     *
     * @param int $rounds
     *
     * @throws \Exception
     */
    public function __construct($rounds = 12)
    {
        if (CRYPT_BLOWFISH != 1) {
            throw new \Exception("bcrypt not supported in this installation. See http://php.net/crypt");
        }
        
        $this->rounds = $rounds;
    }
    
    /**
     * @param $input
     * @param $email
     *
     * @return bool|string
     */
    public function make($input, $email)
    {
        $hash = password_hash($input . md5($email . $input), PASSWORD_DEFAULT, $this->getSalt());
        
        if (strlen($hash) > 13) {
            return $hash;
        } else {
            $this->make($input, $email);
        }
        
        return false;
    }
    
    /**
     * @param $input
     * @param $email
     * @param $existingHash
     *
     * @return bool
     */
    public function verify($input, $email, $existingHash)
    {
        return password_verify($input . md5($email . $input), $existingHash);
    }
    
    /**
     * @return array
     */
    private function getSalt()
    {
        $salt = sprintf('$2a$%02d$', $this->rounds);
        
        $bytes = $this->getRandomBytes(13);
        
        $salt .= $this->encodeBytes($bytes);
        
        return [
            'cost' => 6,
            'salt' => $salt,
        ];
    }
    
    private $randomState;
    
    /**
     * @param $count
     *
     * @return string
     */
    private function getRandomBytes($count)
    {
        $bytes = '';
        
        if (function_exists('openssl_random_pseudo_bytes') &&
            (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN')
        ) { // OpenSSL is slow on Windows
            $bytes = openssl_random_pseudo_bytes($count);
        }
        
        if ($bytes === '' && @is_readable('/dev/urandom') &&
            ($hRand = @fopen('/dev/urandom', 'rb')) !== false
        ) {
            $bytes = fread($hRand, $count);
            fclose($hRand);
        }
        
        if (strlen($bytes) < $count) {
            $bytes = '';
            
            if ($this->randomState === null) {
                $this->randomState = microtime();
                if (function_exists('getmypid')) {
                    $this->randomState .= getmypid();
                }
            }
            
            for ($i = 0; $i < $count; $i += 16) {
                $this->randomState = md5(microtime() . $this->randomState);
                
                if (PHP_VERSION >= '5') {
                    $bytes .= md5($this->randomState, true);
                } else {
                    $bytes .= pack('H*', md5($this->randomState));
                }
            }
            
            $bytes = substr($bytes, 0, $count);
        }
        
        return $bytes;
    }
    
    /**
     * @param $input
     *
     * @return string
     */
    private function encodeBytes($input)
    {
        // The following is code from the PHP Password Hashing Framework
        $itoa64 = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        
        $output = '';
        $i = 0;
        do {
            $c1 = ord($input[$i++]);
            $output .= $itoa64[$c1 >> 2];
            $c1 = ($c1 & 0x03) << 4;
            if ($i >= 13) {
                $output .= $itoa64[$c1];
                break;
            }
            
            $c2 = ord($input[$i++]);
            $c1 |= $c2 >> 4;
            $output .= $itoa64[$c1];
            $c1 = ($c2 & 0x0f) << 2;
            
            $c2 = ord($input[$i++]);
            $c1 |= $c2 >> 6;
            $output .= $itoa64[$c1];
            $output .= $itoa64[$c2 & 0x3f];
        } while (1);
        
        return $output;
    }
}
