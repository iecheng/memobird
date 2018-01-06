class memobird
{
	private $ak = ''; //access key
	private $server = 'http://open.memobird.cn/home/';
	private $url = array(
		'getUserId' => 'setuserbind',
		'getUser' =>  'getuser',
		'printPaper' => 'printpaper',
		'printUrl' => 'printpaperFromUrl',
		'printHtml' => 'printpaperFromHtml',
		'getPrintStatus' => 'getprintstatus',
		'getPic' => 'getSignalBase64Pic',
	);
	private $contents = '';

	public function getUserId($memobirdID,$useridentifying){
		$params=array(
			'ak'=> $this->ak,
			'timestamp'=>date('Y-m-d h:m:s',time()),
			'memobirdID'=>$memobirdID,
			'useridentifying'=>$useridentifying
		);
		$paramsString = http_build_query($params);
		return $this->curl($this->url['getUserId'],$paramsString);
	}
	

	public function getUser($userid){
		$params=array(
			'ak'=> $this->ak,
			'timestamp'=>date('Y-m-d h:m:s',time()),
			'userid'=>$userid
		);
		$paramsString = http_build_query($params);
		return $this->curl($this->url['getUser'],$paramsString);
	}

	public function printPaper($memobirdID,$userID){
		$params=array(
			'ak'=> $this->ak,
			'timestamp'=>date('Y-m-d h:m:s',time()),
			'printcontent'=>$this->contents,
			'memobirdID'=>$memobirdID,
			'userID'=>$userID
		);
		$paramsString = http_build_query($params);
		return $this->curl($this->url['printPaper'],$paramsString);
	}
	
	public function getPaperStatus($printcontentID){
		$params=array(
			'ak'=> $this->ak,
			'timestamp'=>date('Y-m-d h:m:s',time()),
			'printcontentID'=>$printcontentID
		);
		$paramsString = http_build_query($params);
		return $this->curl($this->url['getPrintStatus'],$paramsString);
	}
	
	
	public function getPic($content){
		$params=array(
			'ak'=> $this->ak,
			'imgBase64String'=>$content
		);
		$paramsString = http_build_query($params);
		return $this->curl($this->url['getPic'],$paramsString);
	}
	
	public function addTextContent($text){
		$c = 'T:'.base64_encode($this->charsetToGBK($text)."\n");
		if($this->contents){
			$c = '|'.$c;
		}
		$this->contents .= $c;
		return $this->contents;
	}
	
	public function addImagesByUrl($url){
		$c = $this->c_file($url);
		$r = $this->getPic(base64_encode($c));
		if($r['showapi_res_code'] == 1){
			return $this->addImagesContent($r['result']);
		}
		return false;
	}
	
	public function addImagesContent($content){
		$c = 'P:'.base64_encode($content);
		if($this->contents){
			$c = '|'.$c;
		}
		$this->contents .= $c;
		return $this->contents;
	}
	
	public function addContent($content){
		if($this->contents){
			$content = '|'.$content;
		}
		$this->contents .= $content;
		return $this->contents;
	}
	
	//构造printPaper方法中$printcontent格式，多个可以循环并用|拼接
	public function contentSet($type,$content){
		switch($type){
			case 'T':
				$ret = $type.':'.base64_encode($this->charsetToGBK($content)."\n");break;
			case 'P':
				$ret = 'P:'.base64_encode($content);
			default:
		}
		return $ret;
	}
	
	public  function c_file($file){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $file);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
　　	curl_setopt($ch, CURLOPT_HEADER, 0);
        $ret = curl_exec($ch);
        if (false === $ret) {
            $ret =  curl_errno($ch);
        }
        curl_close($ch);
        return $ret;
    }


    /**
     * 创建http header参数
     * @param array $data
     * @return bool
     */
    private function createHttpHeader() {
        //
    }
    /**
     * 发起 server 请求
     * @param $action
     * @param $params
     * @param $httpHeader
     * @return mixed
     */
    public  function curl($action,$params) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->server . $action);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false); //处理http证书问题
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $ret = curl_exec($ch);
        if (false === $ret) {
            $ret =  curl_errno($ch);
        }
        curl_close($ch);
        return json_decode($ret,true);
    }
	
	public function charsetToGBK($mixed){
		if (is_array($mixed)) {
			foreach ($mixed as $k => $v) {
				if (is_array($v)) {
					$mixed[$k] = charsetToGBK($v);
				} else {
					$encode = mb_detect_encoding($v, array('ASCII', 'UTF-8', 'GB2312', 'GBK', 'BIG5'));
					if ($encode == 'UTF-8') {
						$mixed[$k] = iconv('UTF-8', 'GBK', $v);
					}
				}
			}
		} else {
			$encode = mb_detect_encoding($mixed, array('ASCII', 'UTF-8', 'GB2312', 'GBK', 'BIG5'));
			//var_dump($encode);
			if ($encode == 'UTF-8') {
				$mixed = iconv('UTF-8', 'GBK', $mixed);
			}
		}
		return $mixed;
	}


}
