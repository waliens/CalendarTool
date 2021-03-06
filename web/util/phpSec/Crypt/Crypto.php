<?php namespace phpSec\Crypt;
/**
  phpSec - A PHP security library

  @author    Audun Larsen <larsen@xqus.com>
  @copyright Copyright (c) Audun Larsen, 2011, 2012, 2013
  @link      https://github.com/phpsec/phpSec
  @license   http://opensource.org/licenses/mit-license.php The MIT License
  @package   phpSec
 */

/**
 * Provides methods for encrypting data.
 * @package phpSec
 */
class Crypto {
  public $_algo = 'rijndael-256';
  public $_mode = 'ctr';

  protected $_padding = false;
  const HASH_TYPE = 'sha256';

  /**
   * phpSec core Pimple container.
   */
  private $psl = null;

  /**
   * Constructor.
   *
   * @param \phpSec\Core $psl
   *   phpSec core Pimple container.
   */
  public function __construct(\phpSec\Core $psl) {
    $this->psl = $psl;
  }

  /**
   * Encrypt data returning a JSON encoded array safe for storage in a database
   * or file. The array has the following structure before it is encoded:
   * array(
   *   'cdata' => 'Encrypted data, Base 64 encoded',
   *   'iv'    => 'Base64 encoded IV',
   *   'algo'  => 'Algorythm used',
   *   'mode'  => 'Mode used',
   *   'mac'   => 'Message Authentication Code'
   * )
   *
   * @param mixed $data
   *   Data to encrypt.
   *
   * @param string $key
   *   Key to encrypt data with.
   *
   * @return string
   *   Serialized array containing the encrypted data along with some meta data.
   */
  public function encrypt($data, $key) {

    /* Make sure both algorithm and mode are either block or non-block. */
    $isBlockCipher = mcrypt_module_is_block_algorithm($this->_algo);
    $isBlockMode   = mcrypt_module_is_block_algorithm_mode($this->_mode);
    if($isBlockCipher !== $isBlockMode) {
    	throw new \phpSec\Exception\InvalidAlgorithmParameterException('You can not mix block and non-block ciphers and modes');
    	return false;
    }

    $td = mcrypt_module_open($this->_algo, '', $this->_mode, '');

    /* Check key size. */
    $keySize = strlen($key);
    $keySizes = mcrypt_enc_get_supported_key_sizes($td);
    if(count($keySizes) > 0) {
      /* Encryption method requires a specific key size. */
      if(!in_array($keySize, $keySizes)) {
        throw new \phpSec\Exception\InvalidKeySpecException('Key is out of range. Should be one of: '. implode(', ', $keySizes));
        return false;
      }
    } else {
      /* No spsecific size is needed. */
      if($keySize == 0 || $keySize > mcrypt_enc_get_key_size($td)) {
        throw new \phpSec\Exception\InvalidKeySpecException('Key is out of range. Should be between  1 and ' . mcrypt_enc_get_key_size($td).' bytes.');
        return false;
      }
    }

    /* Create IV. */
    $rnd = $this->psl['crypt/rand'];
    $iv = $rnd->bytes(mcrypt_enc_get_iv_size($td));

    /* Init mcrypt. */
    mcrypt_generic_init($td, $key, $iv);

    /* Prepeare the array with data. */
    $serializedData = serialize($data);

    /* Enable padding of data if block cipher moode. */
    if (mcrypt_module_is_block_algorithm_mode($this->_mode) === true)	{
    	$this->_padding = true;
    }

    /* Add padding if enabled. */
    if($this->_padding === true) {
      $block = mcrypt_enc_get_block_size($td);
      $serializedData = $this->pad($block, $serializedData);
      $encrypted['padding'] = 'PKCS7';
    }

    $encrypted['algo']  = $this->_algo;                                        /* Algorithm used to encrypt. */
    $encrypted['mode']  = $this->_mode;                                        /* Algorithm mode. */
    $encrypted['iv']    = base64_encode($iv);                                  /* Initialization vector, just a bunch of randomness. */
    $encrypted['cdata'] = base64_encode(mcrypt_generic($td, $serializedData)); /* The encrypted data. */
    $encrypted['mac']   = base64_encode(                                       /* The message authentication code. Used to make sure the */
                            $this->pbkdf2($encrypted['cdata'], $key, 1000, 32)  /* message is valid when decrypted. */
                          );
    return json_encode($encrypted);
  }

  /**
   * Strip PKCS7 padding and decrypt
   * data encrypted by encrypt().
   *
   * @param string $data
   *   JSON string containing the encrypted data and meta information in the
   *   excact format as returned by encrypt().
   *
   * @return mixed
   *   Decrypted data in it's original form.
   */
  public function decrypt($data, $key) {

    /* Decode the JSON string */
    $data = json_decode($data, true);

    $dataStructure = array(
      'algo'  => true,
      'mode'  => true,
      'iv'    => true,
      'cdata' => true,
      'mac'   => true,
    );

    if($data === NULL || $this->psl->arrayCheck($data, $dataStructure, false) !== true) {
      throw new \phpSec\Exception\GeneralSecurityException('Invalid data passed to decrypt()');
      return false;
    }
    /* Everything looks good so far. Let's continue.*/
    $td = mcrypt_module_open($data['algo'], '', $data['mode'], '');
    $block = mcrypt_enc_get_block_size($td);

    /* Check MAC. */
    if(base64_decode($data['mac']) != $this->pbkdf2($data['cdata'], $key, 1000, 32)) {
      throw new \phpSec\Exception\GeneralSecurityException('Message authentication code invalid');
      return false;
    }

    /* Init mcrypt. */
    mcrypt_generic_init($td, $key, base64_decode($data['iv']));

    $decrypted = rtrim(mdecrypt_generic($td, base64_decode($this->stripPadding($block, $data['cdata']))));

    /* Close up. */
    mcrypt_generic_deinit($td);
    mcrypt_module_close($td);

    /*Return decrypted data. */
    return unserialize($decrypted);

  }

  /**
   * Implement PBKDF2 as described in RFC 2898.
   *
   * @param string $p
   *   Password to protect.
   *
   * @param string $s
   *   Salt.
   *
   * @param integer $c
   *   Iteration count.
   *
   * @param integer $dkLen
   *   Derived key length.
   *
   * @param string $a
   *   A hash algorithm.
   *
   * @return binary
   *   Derived key.
   */
  public function pbkdf2($p, $s, $c, $dkLen, $a = 'sha256') {
    $hLen = strlen(hash($a, null, true)); /* Hash length. */
    $l    = ceil($dkLen / $hLen);         /* Length in blocks of derived key. */
    $dk   = '';                           /* Derived key. */

    /* Step 1. Check dkLen. */
    if($dkLen > (2^32-1) * $hLen) {
      throw new \phpSec\Exception\GeneralSecurityException('Derived key too long');
      return false;
    }

    for ($block = 1; $block<=$l; $block ++) {
      /* Initial hash for this block. */
      $ib = $b = hash_hmac($a, $s . pack('N', $block), $p, true);
      /* Do block iterations. */
      for ($i = 1; $i<$c; $i ++) {
        /* XOR iteration. */
        $ib ^= ($b = hash_hmac($a, $b, $p, true));
      }
      /* Append iterated block. */
      $dk .= $ib;
    }
    /* Returned derived key. */
    return substr($dk, 0, $dkLen);
  }

  /**
   * PKCS7-pad data.
   * Add bytes of data to fill up the last block.
   * PKCS7 padding adds bytes with the same value that the number of bytes that are added.
   * @see http://tools.ietf.org/html/rfc5652#section-6.3
   *
   * @param integer $block
   *   Block size.
   *
   * @param string $data
   *   Data to pad.
   *
   * @return string
   *   Padded data.
   */
  public function pad($block, $data) {
    $pad = $block - (strlen($data) % $block);
    $data .= str_repeat(chr($pad), $pad);

    return $data;
  }

  /**
   * Strip PKCS7-padding.
   *
   * @param integer $block
   *   Block size.
   *
   * @param string $data
   *   Padded data.
   *
   * @return string
   *   Original data.
   */
  public function stripPadding($block, $data) {
    $pad = ord($data[($len = strlen($data)) - 1]);

    /* Check that what we have at the end of the string really is padding, and if it is remove it. */
    if ($pad && $pad < $block && preg_match('/' . chr($pad) . '{' . $pad . '}$/', $data)) {
      return substr($data, 0, -$pad);
    }
    return $data;
  }
}
