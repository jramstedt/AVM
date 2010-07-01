<?php

/*
	http://wiki.theory.org/Decoding_encoding_bencoded_data_with_PHP
*/
class BDecode
{
	static function decode($s, &$pos=0) {
		if($pos>=strlen($s)) {
			return null;
		}
		switch($s[$pos]) {
			case 'd':
				$pos++;
				$retval=array();
				while ($s[$pos]!='e'){
					$key=self::decode($s, $pos);
					
					// ignore hashes
					if($key == 'pieces') {
						$pos++;
						break;
					}

					$val=self::decode($s, $pos);
					if ($key===null || $val===null)
						break;
						
					$retval[$key]=$val;
				}
				//$retval["isDct"]=true;
				$pos++;
				return $retval;
		
			case 'l':
				$pos++;
				$retval=array();
				while ($s[$pos]!='e'){
					$val=self::decode($s, $pos);
					if ($val===null)
						break;
					$retval[]=$val;
				}
				$pos++;
				return $retval;
		
			case 'i':
				$pos++;
				$digits=strpos($s, 'e', $pos)-$pos;
				$val=(int)substr($s, $pos, $digits);
				$pos+=$digits+1;
				return $val;
	
			default:
				$digits=strpos($s, ':', $pos)-$pos;
				if ($digits<0 || $digits >20)
					return null;
				$len=(int)substr($s, $pos, $digits);
				$pos+=$digits+1;
				$str=substr($s, $pos, $len);
				$pos+=$len;
				//echo "pos: $pos str: [$str] len: $len digits: $digits\n";
				return (string)$str;
		}
		return null;
	}
} // End of class declaration.
?>