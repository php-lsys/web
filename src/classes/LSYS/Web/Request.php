<?php
/**
 * 请求辅助工具
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LSYS\Web;
class Request{
	/**
	 * 是否是get请求
	 * @return bool
	 */
	public static function isGet(){
		return isset($_SERVER['REQUEST_METHOD'])&&$_SERVER['REQUEST_METHOD']=='GET';
	}
	/**
	 * 是否是post请求
	 * @return bool
	 */
	public static function isPost(){
		return isset($_SERVER['REQUEST_METHOD'])&&$_SERVER['REQUEST_METHOD']=='POST';
	}
	/**
	 * 是否是put请求
	 * @return bool
	 */
	public static function isPut(){
		return isset($_SERVER['REQUEST_METHOD'])&&$_SERVER['REQUEST_METHOD']=='PUT';
	}
	/**
	 * 是否是head请求
	 * @return bool
	 */
	public static function isHead(){
		return isset($_SERVER['REQUEST_METHOD'])&&$_SERVER['REQUEST_METHOD']=='HEAD';
	}
	/**
	 * 是否是options请求
	 * @return bool
	 */
	public static function isOptions(){
		return isset($_SERVER['REQUEST_METHOD'])&&$_SERVER['REQUEST_METHOD']=='OPTIONS';
	}
	/**
	 * 是否是DELETE请求
	 * @return bool
	 */
	public static function isDelete(){
		return isset($_SERVER['REQUEST_METHOD'])&&$_SERVER['REQUEST_METHOD']=='DELETE';
	}
	/**
	 * 是否是AJAX请求
	 * @return bool
	 */
	public static function isXmlHttpRequest(){
		return isset($_SERVER["HTTP_X_REQUESTED_WITH"])
			&& strtolower($_SERVER["HTTP_X_REQUESTED_WITH"])=="xmlhttprequest";
	}
	/**
	 * 是否是HTTPS请求
	 * @return bool
	 */
	public static function isSsl(){
		$ssl=isset($_SERVER['HTTPS'])&&strtolower($_SERVER['HTTPS']) == "on";
		if (isset($_SERVER['REQUEST_SCHEME'])&&$_SERVER['REQUEST_SCHEME']=='https')$ssl=true;
		return $ssl;
	}
	/**
	 * 是否是代理请求
	 * @return bool
	 */
	public static function isProxy(){
		if(!empty($_SERVER['HTTP_VIA'])) return true;
		return false;
	}
	/**
	 * 获取当前站点地址
	 * @return string
	 */
	public static function site($domain=null,$is_ssl=null,$port=true){
	    $is_ssl=$is_ssl===null?self::isSsl():$is_ssl;
	    if ($is_ssl)$t="https://";
		else $t=self::scheme().'://';
		$t.=$domain?$domain:self::domain();
		if ($port===true)$port=self::port();
		$port=intval($port);
		if ($port!=80&&$port>0)$t.=":".$port;
		return $t;
	}
	/**
	 * referer
	 * @return string
	 */
	public static function referer($default=null){
		if (isset($_SERVER['HTTP_REFERER'])) return $_SERVER['HTTP_REFERER'];
		return $default;
	}
	/**
	 * domain
	 * @return string
	 */
	public static function domain(){
		$host='localhost';
		if (isset($_SERVER['SERVER_NAME'])) $host=$_SERVER['SERVER_NAME'];
		if (isset($_SERVER['HTTP_HOST'])) $host=$_SERVER['HTTP_HOST'];
		return $host;
	}
	/**
	 * PORT
	 * @return string
	 */
	public static function port(){
		if(isset($_SERVER['SERVER_PORT']))return $_SERVER['SERVER_PORT'];
		return '80';
	}
	/**
	 * SCHEME
	 * @param string $name
	 * @param string $default
	 * @return mixed
	 */
	public static function scheme($default='http'){
		if(isset($_SERVER['REQUEST_SCHEME']))return $_SERVER['REQUEST_SCHEME'];
		return $default;
	}
	/**
	 * QUERY STRING
	 * @param string $name
	 * @param string $default
	 * @return mixed
	 */
	public static function queryString($default=''){
		if(isset($_SERVER['QUERY_STRING']))return $_SERVER['QUERY_STRING'];
		return $default;
	}
	/**
	 * SERVER
	 * @param string $name
	 * @param string $default
	 * @return mixed
	 */
	public static function server($name = null, $default = null){
		if ($name===null) return $_SERVER;
		return isset($_SERVER[$name])?$_SERVER[$name]:$default;
	}
	/**
	 * GET
	 * @param string $name
	 * @param string $default
	 * @return mixed
	 */
	public static function get($name = null, $default = null){
		if ($name===null) return $_GET;
		return isset($_GET[$name])?$_GET[$name]:$default;
	}
	/**
	 * REQUEST
	 * @param string $name
	 * @param string $default
	 * @return mixed
	 */
	public static function request($name = null, $default = null){
		if ($name===null) return $_REQUEST;
		return isset($_REQUEST[$name])?$_REQUEST[$name]:$default;
	}
	/**
	 * POST
	 * @param string $name
	 * @param string $default
	 * @return mixed
	 */
	public static function post($name = null, $default = null){
		if ($name===null) return $_POST;
		return isset($_POST[$name])?$_POST[$name]:$default;
	}
	/**
	 * COOKE
	 * @param string $name
	 * @param string $default
	 * @return mixed
	 */
	public static function cookie($name = null, $default = null){
		if ($name===null) return $_COOKIE;
		return isset($_COOKIE[$name])?$_COOKIE[$name]:$default;
	}
	/**
	 * 获取文件变量
	 * @param string $name
	 * @return array
	 */
	public static function files($name = null){
		if ($name===null) return $_FILES;
		return isset($_FILES[$name])?$_FILES[$name]:NULL;
	}
	/**
	 * 获取当前客户端语言
	 * @param string $default
	 * @return string
	 */
	public static function language($default=NULL){
		if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])){
			return str_replace("-", "_",substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0,5));
		}
		return $default;
	}
	/**
	 * 获取请求路径
	 * @return string
	 */
	public static function requestUri(){
		if (isset($_SERVER['REQUEST_URI'])) return $_SERVER['REQUEST_URI'];
		return '/';
	}
	/**
	 * 获取客户端USER_AGENT
	 * @return string
	 */
	public static function userAgent(){
		return isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:'';
	}
	/**
	 * 获取客户端IP
	 * @return string
	 */
	public static function ip($is_remote=false){
		if ($is_remote===false&&isset($_SERVER['REMOTE_ADDR'])){
			if ($_SERVER['REMOTE_ADDR']=='::1') return '127.0.0.1';
			return $_SERVER['REMOTE_ADDR'];
		}
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])
				AND isset($_SERVER['REMOTE_ADDR']))
		{
			$client_ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
			return array_shift($client_ips);
		}
		elseif (isset($_SERVER['HTTP_X_REAL_IP'])
				AND isset($_SERVER['REMOTE_ADDR']))
		{
			$client_ips = explode(',', $_SERVER['HTTP_X_REAL_IP']);
			return array_shift($client_ips);
		}
		elseif (isset($_SERVER['HTTP_CLIENT_IP'])
				AND isset($_SERVER['REMOTE_ADDR']))
		{
			$client_ips = explode(',', $_SERVER['HTTP_CLIENT_IP']);
			return array_shift($client_ips);
		}else if (isset($_SERVER['REMOTE_ADDR'])){
			if ($_SERVER['REMOTE_ADDR']=='::1') return '127.0.0.1';
			return $_SERVER['REMOTE_ADDR'];
		}
		return '127.0.0.1';
	}
	/**
	 * JSON输出
	 * @var string
	 */
	const ACCEPT_JSON='application/json,text/json';
	/**
	 * 文本输出
	 * @var string
	 */
	const ACCEPT_TEXT='text/plain';
	/**
	 * HTML输出
	 * @var string
	 */
	const ACCEPT_HTML='text/html,application/xhtml+xml';
	/**
	 * XML输出
	 * @var string
	 */
	const ACCEPT_XML='text/xml,application/xml';
	/**
	 * JSONP输出
	 * @var string
	 */
	const ACCEPT_JSONP='application/jsonp';
	/**
	 * JS代码输出
	 * @var string
	 */
	const ACCEPT_JAVASCRIPT='application/javascript';
	/**
	 * 获取客户端accept
	 * @return boolean
	 */
	public static function accept(){
		if (!isset($_SERVER['HTTP_ACCEPT']))return NULL;
		return $_SERVER['HTTP_ACCEPT'];
	}
	/**
	 * 检查客户端是否接受某些输出
	 * @param string $accept ACCEPT_* 常量
	 * @return boolean
	 */
	public static function isAccept($accept){
		if (!isset($_SERVER['HTTP_ACCEPT']))return false;
		$accepts=explode(",", $accept);
		foreach ($accepts as $v){
			if(strpos($_SERVER['HTTP_ACCEPT'], $v)!==false) return true;
		}
		return false;
	}
	/**
	 * 获取优先接受的输出
	 * @param string $accepts 可接受的accept数组
	 * @return string
	 */
	public static function firstAccept($accepts=array()){
		if (!isset($_SERVER['HTTP_ACCEPT'])||empty($_SERVER['HTTP_ACCEPT']))return null;
		foreach (explode(",",$_SERVER['HTTP_ACCEPT']) as $v){
			foreach ($accepts as $accept){
				foreach (explode(",",$accept) as $vv){
					if (strpos($v, $vv)!==false)return $accept;
				}
			}
		}
		return null;
	}
	/**
	 * 获取输入数据
	 * @return string
	 */
	public static function rawPostData(){
		$data = file_get_contents('php://input');
		return urldecode($data);
	}
	/**
	 * IOS浏览器
	 * @var integer
	 */
	const CLIENT_BROWSER_IOS=0;
	/**
	 * QQ浏览器
	 * @var integer
	 */
	const CLIENT_BROWSER_QQ=1;
	/**
	 * 微信
	 * @var integer
	 */
	const CLIENT_WECHAT=2;
	/**
	 * QQ
	 * @var integer
	 */
	const CLIENT_QQ_IM=3;
	/**
	 * 微博
	 * @var integer
	 */
	const CLIENT_WEIBO=4;
	/**
	 * WAP端
	 * @var integer
	 */
	const CLIENT_WAP=5;
	/**
	 * 爬虫
	 * @var integer
	 */
	const CLIENT_REBOT=6;
	/**
	 * CHROME
	 * @var integer
	 */
	const CLIENT_CHROME=7;
	/**
	 * FIREFOX
	 * @var integer
	 */
	const CLIENT_FIREFOX=8;
	/**
	 * IE
	 * @var integer
	 */
	const CLIENT_IE=9;
	/**
	 * 检测是否是指定类型客户端
	 * @param int $browser CLIENT_* 常量
	 * @param string $user_agent 指定user_agent
	 * @return boolean
	 */
	public static function isClient($browser,$user_agent=null){
		$user_agent=$user_agent==null?self::userAgent():$user_agent;
		$status=false;
		switch ($browser){
			case self::CLIENT_BROWSER_IOS:
				if((stripos($user_agent, "iOS")!==false||stripos($user_agent, "iphone")!==false)) return true;
				break;
			case self::CLIENT_CHROME:
				if(stripos(strtolower($user_agent), "chrome")!==false) return true;
				break;
			case self::CLIENT_FIREFOX:
				if(stripos(strtolower($user_agent), "firefox")!==false) return true;
				break;
			case self::CLIENT_IE:
				if(stripos(strtolower($user_agent), "msie")!==false) return true;
				break;
			case self::CLIENT_WECHAT:
				if(!(strpos($user_agent, 'MicroMessenger') === false)) return true;
				break;
			case self::CLIENT_BROWSER_QQ:
				if(preg_match("/mobile.*qq/is",$user_agent)) return true;
				break;
			case self::CLIENT_QQ:
				if(preg_match("/mqqbrowser/is",$user_agent)) return true;
				break;
			case self::CLIENT_WEIBO:
				if(preg_match("/weibo/is",$user_agent)) return true;
				break;
			case self::CLIENT_WAP:
				if(self::userAgent(self::CLIENT_BROWSER_IOS)
				||self::userAgent(self::CLIENT_WECHAT)
				||self::userAgent(self::CLIENT_QQ)
				||self::userAgent(self::CLIENT_BROWSER_QQ)
				||self::userAgent(self::CLIENT_WEIBO)
				)	return true;
				$_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';
				$mobile_browser = 0;
				if(preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', strtolower(@$_SERVER['HTTP_USER_AGENT']))){
					$mobile_browser++;
				}
				if((isset($_SERVER['HTTP_ACCEPT'])) and (strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') !== false)){
					$mobile_browser++;
				}
				if(isset($_SERVER['HTTP_X_WAP_PROFILE'])){
					$mobile_browser++;
				}
				if(isset($_SERVER['HTTP_PROFILE'])){
					$mobile_browser++;
				}
				$mobile_ua = strtolower(substr($user_agent,0,4));
				$mobile_agents = array(
						'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
						'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
						'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
						'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
						'newt','noki','oper','palm','pana','pant','phil','play','port','prox',
						'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
						'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
						'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
						'wapr','webc','winw','winw','xda','xda-'
				);
				if(in_array($mobile_ua, $mobile_agents)){
					$mobile_browser++;
				}
				if(strpos(strtolower(@$_SERVER['ALL_HTTP']), 'operamini') !== false){
					$mobile_browser++;
				}
				// Pre-final check to reset everything if the user is on Windows
				if(strpos(strtolower($user_agent), 'windows') !== false){
					$mobile_browser=0;
				}
				// But WP7 is also Windows, with a slightly different characteristic
				if(strpos(strtolower(@$_SERVER['HTTP_USER_AGENT']), 'windows phone') !== false){
					$mobile_browser++;
				}
				if($mobile_browser>0) return true;
				else	return false;
				break;
			case self::CLIENT_REBOT:
				$spiderSite= array(
						"TencentTraveler",
						"Baiduspider+",
						"BaiduGame",
						"Googlebot",
						"msnbot",
						"Sosospider+",
						"Sogou web spider",
						"ia_archiver",
						"Yahoo! Slurp",
						"YoudaoBot",
						"Yahoo Slurp",
						"MSNBot",
						"Java (Often spam bot)",
						"BaiDuSpider",
						"Voila",
						"Yandex bot",
						"BSpider",
						"twiceler",
						"Sogou Spider",
						"Speedy Spider",
						"Google AdSense",
						"Heritrix",
						"Python-urllib",
						"Alexa (IA Archiver)",
						"Ask",
						"Exabot",
						"Custo",
						"OutfoxBot/YodaoBot",
						"yacy",
						"SurveyBot",
						"legs",
						"lwp-trivial",
						"Nutch",
						"StackRambler",
						"The web archive (IA Archiver)",
						"Perl tool",
						"MJ12bot",
						"Netcraft",
						"MSIECrawler",
						"WGet tools",
						"larbin",
						"Fish search",
				);
				foreach($spiderSite as $val) {
					$str = strtolower($val);
					if (strpos($user_agent, $str) !== false) {
						return true;
					}
				}
				break;
		}
		return false;
	}
	/**
	 * Recursively sanitizes an input variable:
	 *
	 * - Strips slashes if magic quotes are enabled
	 * - Normalizes all newlines to LF
	 *
	 * @param   mixed   $value  any variable
	 * @return  mixed   sanitized variable
	 */
	public static function sanitize($value)
	{
		if (is_array($value) OR is_object($value))
		{
			foreach ($value as $key => $val)
			{
				// Recursively clean each value
				$value[$key] = self::sanitize($val);
			}
		}
		elseif (is_string($value))
		{
			if ((bool) get_magic_quotes_gpc()=== TRUE)
			{
				// Remove slashes added by magic quotes
				$value = stripslashes($value);
			}
			if (strpos($value, "\r") !== FALSE)
			{
				// Standardize newlines
				$value = str_replace(array("\r\n", "\r"), "\n", $value);
			}
		}
		return $value;
	}
}