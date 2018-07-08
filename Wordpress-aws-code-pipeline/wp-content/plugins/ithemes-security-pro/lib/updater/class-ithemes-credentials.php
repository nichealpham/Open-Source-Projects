<?php

    //-----------------------------------------------------------------------------
    
    class iThemes_Credentials
    {
    
        //-----------------------------------------------------------------------------
        
        protected $hash             = 'sha256';                
                
        protected $salt_padding     = 'wdHVwU&HcYcWnllo%kTUUnxpScy4%ICM29';
        
        protected $iteration_count  = 131072;
        
        protected $key_length       = 64;
               
        protected $password;
        
        //-----------------------------------------------------------------------------
        
        public function __construct($username, $password, $options = array())
        {                                                            
            
            $this->username = $username;
            
            $this->password = $password;
            
            
            if(!empty($options['hash']))
                $this->hash             = strtolower(trim($options['hash']));
            
            if(!empty($options['iterations']))
                $this->iteration_count  = intval($options['iterations']);
                                   
            if(!empty($options['salt']))
                $this->salt_padding     = $options['salt'];
            
            if(!empty($options['key_length']))
                $this->key_length       = intval($options['key_length']);
        
        }                
        
        //-----------------------------------------------------------------------------        
        
        public static function get_password_hash($username, $password, $options = array())
        {
            
            $hasher = new iThemes_Credentials($username, $password, $options);
            
            return $hasher->get_pbkdf2();
            
        }                                
        
        //-----------------------------------------------------------------------------
        
        public function get_salt()
        {
            
            return strtolower(trim($this->username)) . $this->salt_padding;            
            
        }                
        
        //-----------------------------------------------------------------------------
        
        public function get_pbkdf2()
        {
            
            return $this->pbkdf2($this->hash, 
                                 $this->password, 
                                 $this->get_salt(), 
                                 $this->iteration_count, 
                                 $this->key_length / 2, 
                                 false);
            
        }
        
        //-----------------------------------------------------------------------------        
        
        /*
         * PBKDF2 key derivation function as defined by RSA's PKCS #5: https://www.ietf.org/rfc/rfc2898.txt
         * $algorithm - The hash algorithm to use. Recommended: SHA256
         * $password - The password.
         * $salt - A salt that is unique to the password.
         * $count - Iteration count. Higher is better, but slower. Recommended: At least 1000.
         * $key_length - The length of the derived key in bytes.
         * $raw_output - If true, the key is returned in raw binary format. Hex encoded otherwise.
         * Returns: A $key_length-byte key derived from the password and salt.
         *
         * Test vectors can be found here: https://www.ietf.org/rfc/rfc6070.txt
         *
         * This implementation of PBKDF2 was originally created by https://defuse.ca
         * With improvements by http://www.variations-of-shadow.com
         */
        private function pbkdf2($algorithm, $password, $salt, $count, $key_length, $raw_output = false)
        {
            
            $algorithm = strtolower($algorithm);
            
            if(!in_array($algorithm, hash_algos(), true))
                trigger_error('PBKDF2 ERROR: Invalid hash algorithm.', E_USER_ERROR);
            
            if($count <= 0 || $key_length <= 0)
                trigger_error('PBKDF2 ERROR: Invalid parameters.', E_USER_ERROR);
                    
        
            $hash_length = strlen(hash($algorithm, '', true));
            $block_count = ceil($key_length / $hash_length);
        
            $output = '';
            
            for($i = 1; $i <= $block_count; $i++) 
            {
                
                // $i encoded as 4 bytes, big endian.
                $last = $salt . pack("N", $i);
                
                // first iteration
                $last = $xorsum = hash_hmac($algorithm, $last, $password, true);
                
                // perform the other $count - 1 iterations
                for ($j = 1; $j < $count; $j++) 
                {
                    $xorsum ^= ($last = hash_hmac($algorithm, $last, $password, true));
                }
                
                $output .= $xorsum;
                
            }
        
            if($raw_output)
                return substr($output, 0, $key_length);
            else
                return bin2hex(substr($output, 0, $key_length));
                
        }

        //-----------------------------------------------------------------------------
                                
    }
    
    //-----------------------------------------------------------------------------
    