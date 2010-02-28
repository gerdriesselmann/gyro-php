<?php
/**
 * Sets and services provides by the SocialBookmarking module
 * 
 * @author Gerd Riesselmann
 * @ingroup SocialBookmarking
 */
class SocialBookmarking {
	const BLOGSMARK = 'blogsmark';
	const DELICIOUS = 'del.icio.us';
	const DIGG = 'digg';
	const FACEBOOK = 'facebook';
	const FURL = 'furl';
	const GOOGLE = 'google';
	const GOOGLE_BUZZ = 'buzz';
	const YAHOO = 'yahoo';
	//const MAGNOLIA = 'ma.gnolia';
	const MYSPACE = 'myspace';
	const NETSCAPE = 'netscape';
	const NEWSVINE = 'newsvine';
	const REDDIT = 'reddit';
	const STUMBLEUPON = 'stumbleupon';
	const TECHNORATI = 'technarati';
	const TWITTER = 'twitter';
	const WINDOWS_LIVE = 'live';
	const EMAIL = 'email';
	
	const MISTER_WONG = 'misterwong';
	const YIGG = 'yigg';
	const WEBNEWS = 'webnews';
	
	/**
	 * Services popular in US 
	 * 
	 * According to http://www.labnol.org/internet/most-popular-social-bookmarking-services/4191/
	 */
	const SET_POPULAR_EN = 'set_en';
	/**
	 * Services popular in Germany 
	 */
	const SET_POPULAR_DE = 'set_de';
	/**
	 * Bookmarking tools commonly found on US tech sites
	 */
	const SET_TECH_EN = 'set_tech_en';
	
	/**
	 * User defined sets
	 * 
	 * @var array
	 */
	private static $defined_sets = array();
	
	/**
	 * Returns configuration for all available services
	 * 
	 * @return array
	 */
	public static function get_all_services() {
		$ret = RuntimeCache::get('socialbookmarking_services', false);
		if ($ret === false) {
			Load::components('socialbookmark');
			$ret = array(
				self::EMAIL => new SocialBookmark(tr('Mail to a friend', 'socialbookmarking'), 'mailto:?subject=%TITLE%&body=%TITLE%: %URL%', 'email.gif'),
				self::TWITTER => new SocialBookmark(tr('Tweet it', 'socialbookmarking'), 'http://twitter.com/home?status=%TITLE%:+%URL%', 'twitter.gif'),
				self::FACEBOOK => new SocialBookmark(tr('Send to Facebook', 'socialbookmarking'), 'http://www.facebook.com/sharer.php?u=%URL%&t=%TITLE%', 'facebook.gif'),
				self::GOOGLE_BUZZ => new SocialBookmark(tr('Buzz this!', 'socialbookmarking'), 'http://www.google.com/reader/link?url=%URL%&title=%TITLE%', 'google_buzz.png'),
				self::DIGG => new SocialBookmark(tr('Digg it!', 'socialbookmarking'), 'http://digg.com/submit?phase=2&url=%URL%', 'digg.png'),
				self::MYSPACE => new SocialBookmark(tr('Add to MySpace', 'socialbookmarking'), 'http://www.myspace.com/Modules/PostTo/Pages/?c=%URL%&t=%TITLE%', 'myspace.gif'),
				self::STUMBLEUPON => new SocialBookmark(tr('Bookmark on StumbleUpon', 'socialbookmarking'), 'http://www.stumbleupon.com/submit?url=%URL%&title=%TITLE%', 'stumbleupon.gif'),
				self::REDDIT => new SocialBookmark(tr('Bookmark on Reddit', 'socialbookmarking'), 'http://reddit.com/submit?url=%URL%&title=%TITLE%', 'reddit.gif'),
				self::DELICIOUS => new SocialBookmark(tr('Add to Delicious', 'socialbookmarking'), 'http://delicious.com/post?url=%URL%&title=%TITLE%', 'delicious.png'),
				self::GOOGLE => new SocialBookmark(tr('Add to Google Bookmarks', 'socialbookmarking'), 'http://www.google.com/bookmarks/mark?op=add&bkmk=%URL%&title=%TITLE%', 'google.gif'),
				self::WINDOWS_LIVE => new SocialBookmark(tr('Bookmark on Live.com', 'socialbookmarking'), 'https://favorites.live.com/quickadd.aspx?marklet=1&mkt=en-us&url=%URL%&title=%TITLE%&top=1', 'live.gif'),
				self::TECHNORATI => new SocialBookmark(tr('Add to Technorati', 'socialbookmarking'), 'http://www.technorati.com/faves?add=%URL%', 'technorati.gif'),
				// End top 10
				self::FURL => new SocialBookmark(tr('Add to Furl', 'socialbookmarking'), 'http://furl.net/storeIt.jsp?t=%TITLE%&u=%URL%', 'furl.png'),
				self::NETSCAPE => new SocialBookmark(tr('Boomark on Netscape', 'socialbookmarking'), 'http://www.netscape.com/submit/?U=%URL%&T=%TITLE%', 'netscape.gif'),
				self::NEWSVINE => new SocialBookmark(tr('Bookmark on Newsvine', 'socialbookmarking'), 'http://www.newsvine.com/_wine/save?u=%URL%&h=%TITLE%', 'newsvine.gif'),
				self::BLOGSMARK => new SocialBookmark(tr('Add to BlogMarks.net', 'socialbookmarking'), 'http://blogmarks.net/my/new.php?mini=1&url=%URL%&title=%TITLE%', 'blogmarks.gif'),
				//self::MAGNOLIA => new SocialBookmark(tr('Add to Ma.gnolia', 'socialbookmarking'), 'http://ma.gnolia.com/bookmarklet/add?url=%URL%&title=%TITLE%', 'magnolia.gif'),
				// German
				self::MISTER_WONG => new SocialBookmark(tr('Bookmark at Mister Wong', 'socialbookmarking'), 'http://www.mister-wong.com/index.php?action=addurl&bm_url=%URL%&bm_description=%TITLE%', 'misterwong.gif'),
				self::YIGG => new SocialBookmark(tr('Add to Yigg', 'socialbookmarks'), 'http://yigg.de/neu?exturl=%URL%&title=%TITLE%', 'yigg.gif'),
				self::WEBNEWS => new SocialBookmark(tr('Post on Webnews', 'socialbookmarks'), 'http://www.webnews.de/einstellen?url=%URL%&title=%TITLE%', 'webnews.gif'),
				
			);
			RuntimeCache::set('socialbookmarking_services', $ret);
		}
		return $ret;
	}
	
	/**
	 * Create a set of Bookmark Items
	 * 
	 * @param $set_name
	 * @return array
	 */
	public static function create_set($set_name) {
		$ret = Arr::get_item(self::$defined_sets, $set_name, false);
		if ($ret === false) {
			switch ($set_name) {
				case self::SET_POPULAR_EN:
					$ret = array(
						self::EMAIL, 
						self::FACEBOOK, self::MYSPACE, self::TWITTER, 
						self::DIGG, self::STUMBLEUPON, self::REDDIT, self::DELICIOUS, 
						self::GOOGLE, self::WINDOWS_LIVE, self::TECHNORATI 
					);
					break;
				case self::SET_TECH_EN:
					$ret = array(
						self::EMAIL, 
						self::TWITTER, self::FACEBOOK, self::GOOGLE_BUZZ,  
						self::DIGG, self::DELICIOUS, self::STUMBLEUPON, self::REDDIT
					);
					break;
				case self::SET_POPULAR_DE:
					$ret = array(
						self::EMAIL, 
						self::FACEBOOK, self::MYSPACE, self::TWITTER, 
						self::MISTER_WONG, self::YIGG, self::DELICIOUS,
						self::WEBNEWS, 
						self::GOOGLE, self::WINDOWS_LIVE 
					);
					break;				
				default:
					$ret = array(self::EMAIL);
					break;
			} 	
		}
		return $ret;
	}
	
	/**
	 * Define a set
	 * 
	 * Predefined sets also can be overwritten
	 * 
	 * @param string $name Name of set
	 * @param array $arr_services Array of services in set
	 * @return void
	 */
	public static function define_set($name, $arr_services) {
		self::$defined_sets[$name] = $arr_services;
	}
	
	/**
	 * Return service with given name - if available
	 * 
	 * @param string $name
	 * @return SocialBookmark
	 */
	public static function get_service($name) {
		$all = self::get_all_services();
		return Arr::get_item($all, $name, false);
	} 
}