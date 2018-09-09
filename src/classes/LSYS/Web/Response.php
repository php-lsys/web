<?php
/**
 * 输出辅助工具类
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LSYS\Web;
class Response{
	/**
	 * @var string
	 */
	protected $_header=[];
	/**
	 * @var string
	 */
	protected $_body;
	/**
	 * @var int
	 */
	protected $_http_code;
	/**
	 * 设置HEADER
	 * @param string|array $name
	 * @param string $value
	 * @return $this
	 */
	public function set_header($name,$value=null){
		if (is_array($name)){
			foreach ($name as $k=>$v)$this->set_header($k,$v);
		}else{
			if (is_array($value))$value=implode(",",$value);
			if (empty($value))return $this;
			$value=strval($value);
			$name=trim($name);
			$value=trim($value);
			$this->_header[$name]=$value;
		}
		return $this;
	}
	/**
	 * 设置下载HEADER
	 * @return $this
	 */
	public function set_download_header($name, $mimeType = null, $inline = false, $contentLength = null)
	{
		$disposition = $inline ? 'inline' : 'attachment';
		$this->set_header('Pragma', 'public')
			->set_header('Accept-Ranges', 'bytes')
			->set_header('Expires', '0')
			->set_header('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
			->set_header('Content-Disposition', "$disposition; filename=\"$name\"");
		if ($mimeType !== null) {
			$this->set_header('Content-Type', $mimeType);
		}
		if ($contentLength !== null) {
			$this->set_header('Content-Length', $contentLength);
		}
		return $this;
	}
	/**
	 * 设置重定向HEADER
	 * @return $this
	 */
	public function set_redirect($uri,$code=302){
	    $this->set_http_code($code);
		$uri=str_replace(array("\r","\n","\t"), "", $uri);
		$this->set_header('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT');
		$this->set_header('Last-Modified', gmdate("D, d M Y H:i:s") . "GMT");
		$this->set_header('Cache-Control', "no-cache, must-revalidate");
		$this->set_header('Pragma', "no-cache");
		$this->set_header('location', $uri);
		return $this;
	}
	/**
	 * 设置HTTP状态码
	 * @return $this
	 */
	public function set_http_code($code){
	    $this->_http_code=$code;
	    return $this;
	}
	/**
	 * 设置资源HEADER
	 * @param resource $f
	 * @return $this
	 */
	public function set_stream_header($f){
	    assert(is_resource($f));
	    $meta=stream_get_meta_data($f);
	    if(!stream_is_local($f)||$meta['stream_type']!='STDIO')return $this;
        $start=ftell($f);
        fseek($f, 0,SEEK_END);
        $end=ftell($f);
        $size = $end-$start;
        fseek($f, $start,SEEK_SET);
        if($size==0)return $this;
        $size2 = $size-1;
        $range = 0;
        $this->set_header('Content-Length', $size);
        $mine=mime_content_type($meta['uri']);
        $this->set_header('Content-type', $mine);
        $mine=mime_content_type($meta['uri']);
        $this->set_header('Accenpt-Ranges', 'bytes');
        $name=basename($meta['uri']);
        $this->set_header('Content-Disposition', "attachment; filename=\"$name\"");
        if(isset($_SERVER['HTTP_RANGE'])) {
            http_response_code(206);
            $range = str_replace('=','-',$_SERVER['HTTP_RANGE']);
            $range = explode('-',$range);
            $range = trim($range[1]);
            $range=abs(intval($range));
            $this->set_header('Content-Range', 'bytes '.$range.'-'.$size2.'/'.$size);
        } else {
            $this->set_header('Content-Range', 'bytes 0-'.$size2.'/'.$size);
        }
        return $this;
	}
	/**
	 * 清理已设置的HEADER
	 * @return $this
	 */
	public function clear_header(){
	    $this->_header=[];
	    return $this;
	}
	/**
	 * 获取已设置的指定HEADER
	 * @return string
	 */
	public function get_header($name){
	    return isset($this->_header[$name])?$this->_header[$name]:NULL;
	}
	/**
	 * 获取已设置的所有HEADER
	 * @return array
	 */
	public function get_headers($join = false){
	    if ($join==false)return $this->_header;
	    $out=array();
	    foreach ($this->_header as $k=>$v){
	        $out[]=$k.":".$v;
	    }
	    return $out;
	}
	/**
	 * 获取已设置HTTP状态码
	 * @return int
	 */
	public function get_http_code(){
	    return $this->_http_code;
	}
	/**
	 * 输出流,支持断点续传
	 * @param resource $f
	 */
	public static function stream_output($f){
	    assert(is_resource($f));
	    $meta=stream_get_meta_data($f);
	    if(!stream_is_local($f)||$meta['stream_type']!='STDIO'){
	        $res=fopen('php://output','w');
	        stream_copy_to_stream($f, $res);
	        return;
	    }
	    $range=0;
	    $start=ftell($f);
	    if(isset($_SERVER['HTTP_RANGE'])) {
	        http_response_code(206);
	        $range = str_replace('=','-',$_SERVER['HTTP_RANGE']);
	        $range = explode('-',$range);
	        $range = trim($range[1]);
	        $range=abs(intval($range));
	    }
	    if ($start==0&&$range==0){
	        readfile($meta['uri']);
	    }else{//断点续传
	        fseek($f,$range+$start);
	        while(!feof($f)) {
	            print(fread($f,4068));
	        }
	    }
	}
}