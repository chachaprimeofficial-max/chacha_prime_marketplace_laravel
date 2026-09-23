<?php
namespace App\Services;

class TotpService {
    public function generateSecret(int $length=20): string {
        $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ234567'; $bytes=random_bytes($length); $secret='';
        foreach($bytes as $b){$secret.=$chars[$b & 31];}
        return substr($secret,0,32);
    }
    public function verify(string $secret,string $code,int $window=1): bool {
        $code=preg_replace('/\D/','',$code); if(strlen($code)!==6)return false;
        $counter=intdiv(time(),30);
        for($i=-$window;$i<=$window;$i++){if(hash_equals($this->code($secret,$counter+$i),$code))return true;}
        return false;
    }
    public function code(string $secret,int $counter): string {
        $alphabet='ABCDEFGHIJKLMNOPQRSTUVWXYZ234567'; $secret=strtoupper($secret); $bits='';
        foreach(str_split($secret) as $c){$p=strpos($alphabet,$c);if($p===false)continue;$bits.=str_pad(decbin($p),5,'0',STR_PAD_LEFT);}
        $binary=''; foreach(str_split($bits,8) as $chunk){if(strlen($chunk)===8)$binary.=chr(bindec($chunk));}
        $key=$binary; $data=pack('N*',0).pack('N*',$counter); $hash=hash_hmac('sha1',$data,$key,true);
        $offset=ord($hash[19])&15; $num=((ord($hash[$offset])&127)<<24)|((ord($hash[$offset+1])&255)<<16)|((ord($hash[$offset+2])&255)<<8)|(ord($hash[$offset+3])&255);
        return str_pad((string)($num%1000000),6,'0',STR_PAD_LEFT);
    }
    public function uri(string $email,string $secret): string {
        return 'otpauth://totp/Chacha%20Prime:'.rawurlencode($email).'?secret='.$secret.'&issuer=Chacha%20Prime&digits=6&period=30';
    }
}