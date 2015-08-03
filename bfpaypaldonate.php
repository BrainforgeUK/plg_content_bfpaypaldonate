<?php
/**
* @package   Paypal Donate Button
* @version   0.0.1
* @author    http://www.brainforge.co.uk
* @copyright Copyright (C) 2012 Jonathan Brain. All rights reserved.
* @license	 GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

// No direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

class plgContentBFPaypalDonate extends JPlugin{
  private $accountinfo;  
  private static $donateform = false;  
  /**
   *
   */
	public function __construct(&$subject, $config = array()) {
	  parent::__construct($subject, $config);
    $this->accountinfo = $this->params->def('accountinfo');
  }

  /**
   *
   */
	public function onContentPrepare($context, &$article, &$params, $limitstart){	
    if (empty($this->accountinfo)) return;

		$matches = array();
		if (preg_match_all('/{(bfpaypaldonate)\s*(.*?)}/i', $article->text, $matches, PREG_SET_ORDER)) {
  		foreach ($matches as $match){
  		  $article->text = $this->insertDonateButton($match[0], $article->text, trim($match[2], '"'));
  		} 		
  	}
  }

  /**
   *
   */
  private function insertDonateButton($match, $text, $comment=null) {
    if (empty($this->accountinfo)) return;
    
    $text = str_replace($match, '<button class="donateClickButton" onclick="jQuery(\'#donateClickButton\').submit();return false;">' .
                                $comment . '<br/>' .
                                '<img src="https://www.paypalobjects.com/en_GB/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal — The safer, easier way to pay online."/></button>' .
                                '<img alt="" border="0" src="https://www.paypalobjects.com/en_GB/i/scr/pixel.gif" width="1" height="1">', $text);
    if (self::$donateform) {
      return $text;
    }
    self::$donateform = true;

    return $text . '<form id="donateClickButton" action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="encrypted" value="' . $this->accountinfo . '
">
</form>
';
  }
}
?>