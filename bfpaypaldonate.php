<?php
/**
* @package   Paypal Donate Button
* @version   0.0.1
* @author    http://www.brainforge.co.uk
* @copyright Copyright (C) 2012-2025 Jonathan Brain. All rights reserved.
* @license	 GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

// No direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

class plgContentBFPaypalDonate extends JPlugin{
	protected $app;
    protected $accountinfo;
	protected static $donateform = false;

	/**
	*/
	public function __construct(&$subject, $config = array())
	{
		parent::__construct($subject, $config);

        $this->accountinfo = $this->params->def('accountinfo');
	}

	/**
	*
	*/
	public function onContentPrepare($context, &$article, &$params, $limitstart)
	{
        if (!$this->app->isClient('site')) return;

		if (empty($this->accountinfo)) return;

        // Allow the escape sequence to be embedded in text without triggering a match.
        $article->text = str_replace('{{bfpaypaldonate', '&#123;bfpaypaldonate', $article->text);

        $matches = array();
        if (preg_match_all('/{(bfpaypaldonate\s*)(.*?)}/i', $article->text, $matches, PREG_SET_ORDER))
		{
		    foreach ($matches as $match)
			{
                $article->text = $this->insertDonateButton($match[0], $article->text, trim($match[2], '"'));
		    }
		}
	}

	/*
	*/
	private function insertDonateButton($match, $text, $comment='')
	{
        $match = trim($match);
        $comment = trim($comment);

		ob_start();
		?>
		<button class="donateClickButton"
		  onclick="document.getElementById('donateClickButton').submit();return false;" >
			<?php echo empty($comment) ? '' : $comment . '<br/>'; ?>
			<img src="https://www.paypalobjects.com/en_GB/i/btn/btn_donate_LG.gif"
			     style="border:none;"
			     name="submit"
			     alt="PayPal � The safer, easier way to pay online." />
		</button>

		<img alt=""
		     style="border:none"
		     src="https://www.paypalobjects.com/en_GB/i/scr/pixel.gif" width="1" height="1"/>
		<?php
		$html = ob_get_clean();

		$html = str_replace($match, $html, $text);

		if (self::$donateform) return $html;
		self::$donateform = true;

		ob_start();
		?>
			<form id="donateClickButton"
			      action="https://www.paypal.com/cgi-bin/webscr"
			      method="post">
				<input type="hidden" name="cmd" value="_s-xclick"/>
				<input type="hidden" name="encrypted" value="<?php echo $this->accountinfo; ?>">
			</form>
		<?php
		return $html . ob_get_clean();
	}
}
?>