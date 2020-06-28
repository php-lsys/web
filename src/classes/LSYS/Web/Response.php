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
	public function setHeader($name,$value=null){
		if (is_array($name)){
			foreach ($name as $k=>$v)$this->setHeader($k,$v);
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
	public function setDownloadHeader(string $name, ?string $mimeType = null, bool $inline = false, ?int $contentLength = null)
	{
		$disposition = $inline ? 'inline' : 'attachment';
		$this->setHeader('Pragma', 'public')
			->setHeader('Accept-Ranges', 'bytes')
			->setHeader('Expires', '0')
			->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
			->setHeader('Content-Disposition', "$disposition; filename=\"$name\"");
		if ($mimeType !== null) {
			$this->setHeader('Content-Type', $mimeType);
		}
		if ($contentLength !== null) {
			$this->setHeader('Content-Length', $contentLength);
		}
		return $this;
	}
	/**
	 * 设置重定向HEADER
	 * @return $this
	 */
	public function setRedirect(string $uri,int $code=302){
	    $this->setHttpCode($code);
		$uri=str_replace(array("\r","\n","\t"), "", $uri);
		$this->setHeader('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT');
		$this->setHeader('Last-Modified', gmdate("D, d M Y H:i:s") . "GMT");
		$this->setHeader('Cache-Control', "no-cache, must-revalidate");
		$this->setHeader('Pragma', "no-cache");
		$this->setHeader('location', $uri);
		return $this;
	}
	/**
	 * 设置HTTP状态码
	 * @return $this
	 */
	public function setHttpCode(int $code){
	    $this->_http_code=$code;
	    return $this;
	}
	/**
	 * 设置资源HEADER
	 * @param resource $f
	 * @return $this
	 */
	public function setStreamHeader($f){
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
        $this->setHeader('Content-Length', $size);
        $mine=mime_content_type($meta['uri']);
        $this->setHeader('Content-type', $mine);
        $mine=mime_content_type($meta['uri']);
        $this->setHeader('Accenpt-Ranges', 'bytes');
        $name=basename($meta['uri']);
        $this->setHeader('Content-Disposition', "attachment; filename=\"$name\"");
        if(isset($_SERVER['HTTP_RANGE'])) {
            http_response_code(206);
            $range = str_replace('=','-',$_SERVER['HTTP_RANGE']);
            $range = explode('-',$range);
            $range = trim($range[1]);
            $range=abs(intval($range));
            $this->setHeader('Content-Range', 'bytes '.$range.'-'.$size2.'/'.$size);
        } else {
            $this->setHeader('Content-Range', 'bytes 0-'.$size2.'/'.$size);
        }
        return $this;
	}
	/**
	 * 清理已设置的HEADER
	 * @return $this
	 */
	public function clearHeader(){
	    $this->_header=[];
	    return $this;
	}
	/**
	 * 获取已设置的指定HEADER
	 * @return string|NULL
	 */
	public function getHeader($name){
	    return isset($this->_header[$name])?$this->_header[$name]:NULL;
	}
	/**
	 * 获取已设置的所有HEADER
	 * @return array
	 */
	public function getHeaders(bool $join = false):array{
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
	public function getHttpCode():?int{
	    return $this->_http_code;
	}
	/**
	 * 输出流,支持断点续传
	 * @param resource $f
	 */
	public static function streamOutput($f){
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