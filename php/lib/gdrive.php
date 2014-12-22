<?php

/**
 * @see Zend_Loader
 * mimeType add-on from http://blog.artistandesigns.com/2011/02/zendgdata-lacks-document-download.html
 */

@require_once (__ROOT__.'php/external/ZendGdata-1.12.2/library/Zend/Loader.php');
set_include_path(__ROOT__.DIRNAME('php/external/ZendGdata-1.12.2/library/.').PATH_SEPARATOR.".");
@Zend_Loader::loadClass('Zend_Gdata');
@Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
@Zend_Loader::loadClass('Zend_Gdata_Spreadsheets');
@Zend_Loader::loadClass('Zend_Gdata_App_AuthException');
@Zend_Loader::loadClass('Zend_Http_Client');
@Zend_Loader::loadClass('Zend_Gdata_Docs');


require_once(__ROOT__ . 'php/external/FirePHPCore/lib/FirePHPCore/FirePHP.class.php');
ob_start(); // Starts FirePHP output buffering
$firephp = FirePHP::getInstance(true);


class gDrive{
    // holds the service tokens
    private $_serviceSessionToken = array();
    private $_user = '';
    private $_pass = '';
    private $_debug = false;
    private $_curl = false;
    
    
    public $client = null; 
    public $docs = null; 

    /**
     * construct
     *
     * @param  string $user The username, in e-mail address format, to authenticate
     * @param  string $pass The password for the user specified
     * @param  array $tokens Array of tokens for clientlogin authentication
     * @return void
     */
    function __construct($username,$passwd,$tokens=array()){
        $this->_user=$username;
        $this->_pass=$passwd;
        foreach($tokens as $service=>$token){
            $this->set_service_token($service,$token);
        }
        
        $this->client = $this->getClientLoginHttpClient(Zend_Gdata_Docs::AUTH_SERVICE_NAME);
        //$this->client->setHeaders('If-Match: *');
        $this->debug('authenticated');
        
        $this->docs = new Zend_Gdata_Docs($this->client);
        
    }
    
    
    /**
     * 
     * Utility function to download and store without authentication remote files. 
     * @param string $sharedLink Full URI to file
     * @param string $format Expected format of the file; important for Google Spreadsheets, specifies in which format file will be downloaded
     * @param string $uploadDir path to location where files will be stored locally
     * @throws Exception
     */
    public static function fetchFile($sharedLink, $format='csv', $saveFileTo){
    	
    	
    	//downloading google spreadhsheet
    	if (stripos($sharedLink, "google") > 0 ){
    		//shared links have this format: 
    	   	//https://docs.google.com/spreadsheet/ccc?key=0AnNH_85fehf9dHB0QVVxdk5uam9yeDdVX0tXSE5RV0E#gid=0
    	   	//but for downloadin this has to be turned into
    	   	//https://docs.google.com/feeds/download/spreadsheets/Export?key={id}&exportFormat=csv&format=csv
    	   	$start = stripos($sharedLink, "key=");
    		$docId = substr($sharedLink,$start, 48);
    		$url = "https://docs.google.com/feeds/download/spreadsheets/Export?".$docId."&exportFormat=".$format."&format=".$format;
    	
    	//all other
    	} else {
    		$url = $sharedLink; 
    	}
    	
    	global $firephp;
    	$firephp->log($url, "download url");

    	     
		$outhandle = fopen($saveFileTo, 'w');
		
		if (!$outhandle)
	        throw new Exception("Export exception. Could not open {$uploadTmpFile} to store fetched file. Make sure that local_config/upload is a writable directory");
	  
	 
		$ch = curl_init($url);
	    curl_setopt($ch, CURLOPT_FILE, $outhandle);
	    
	    curl_exec($ch);
	    
	    //can't get file name from the download URL of google directly
	    //$responseURL = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);  
    	//$firephp->log($responseContent, "curl url");
	    	
	    curl_close($ch);
	    fclose($outhandle);
	    
	    return $saveFileTo; 
    }
    
    
    
    function nameExists($filename){
        	
    	$feed = $this->docs->getDocumentListFeed('https://docs.google.com/feeds/documents/private/full/-/spreadsheet');

		$link = false; 
		global $firephp; 
		foreach($feed->entries as $entry) { 
			
		    if($entry->title->text == $filename){
		    	$link = $entry->getLink();
		    	$link = $link[5]->href; 
		    	$firephp->log($link, "found url for ". $filename);
		    	break;
		    }        
		}
		
		return $link; 
 
    }
    
    function deleteDoc($uri){
   
		//set the header and protocol version! 
		//the example at stackoverflow uses this for undeleting http://stackoverflow.com/questions/7607508/restoring-a-google-doc-from-trash-using-zend-gdata	
    	$this->client->setHeaders('If-Match: *');
    	$this->docs = new Zend_Gdata_Docs($this->client);
		$this->docs->setMajorProtocolVersion(3);
	
		global $firephp;
		
		//correct format: 
		//$link = "https://docs.google.com/feeds/default/private/full/0AnNH_85fehf9dEMzTmU1NTlXWUhLV2lnMWpfUUlleWc#gid=0";
		
		//need to replace the documents with default?!!!!!!!!!!!!!!
		$uri = str_replace('feeds/documents/private', 'feeds/default/private', $uri );
		
		$firephp->log($uri,"url to delete");
		
		$this->docs->delete($uri);
    }
    
    
    function debug($message){
        if($this->_debug)
            echo date('Y-m-d H:i:s').' :: '.$message."  <br/>\n";
    }

    /**
     * convert
     *
     * @param  string           $filename              the file name (either direct path to file or name of file with $tempfile holding the path to actual tmp file
     * @param  string           $newfilename           save as this file name
     * @param  string           $tempfile              file location if upload (/tmp/...)
     * @param  string           $format                format of file to download
     *                                                 http://code.google.com/apis/documents/docs/3.0/developers_guide_protocol.html#DownloadingSpreadsheets
     * @param  string           $gid                   The gid parameter is an absolute identifier for worksheets
     *                                                 for spreadsheets (if not numberic it will download entire workbook as one sheet)
     * @return void
     */
    /*
    function convert($filename, $newfilename='', $tempfile='',$format='csv',$gid=0){
        $this->debug('convert file');
        // authenticate to docs list (wordly)
        
    
        
        // upload temporary file to ggl
        $newDoc = $this->uploadDocument($filename, $tempfile);
        $this->debug('uploaded');
        // get the content source url
        $src = $newDoc->content->getSrc();
        // download the data to the new filename
        if($this->_curl){
            $content = $this->curlSrc($src, $format, $gid, $newfilename);
        } else {
            $content = $this->downloadSrc($src, $format, $gid, $newfilename);
        }
        $this->debug('downloaded');
        // delete the temporary file on ggl
        //$newDoc->delete();
        $this->debug('deleted');
    }*/

    /**
     * set_service_token
     *
     * @param  string $service Which service to authenticate against.
     * @param  string $token Token for the service identified
     * @return void
     */
    function set_service_token($service,$token){
        //echo "$service :: $token    <br/>\n";
        $this->_serviceSessionToken[$service] = trim($token);// make sure it is clean.
    }

    /**
     * get_service_token
     *
     * @param  string $service Which service to authenticate against.
     * @return string
     */
    function get_service_token($service){
        if(!empty($this->_serviceSessionToken[$service])){
            //echo "$service :: ".$this->_serviceSessionToken[$service]."    <br/>\n";
            return $this->_serviceSessionToken[$service];
        }
        throw new Exception("session token not found for service {$service}\n");
        return false;
    }

    /**
     * Returns a HTTP client object with the appropriate headers for communicating
     * with Google using the ClientLogin credentials supplied.
     *
     * @param  string $service Which service to authenticate against.
     * @return Zend_Http_Client
     */
    function getClientLoginHttpClient($service='writely'){
        try{
            $token = $this->get_service_token($service);
            $this->debug('token');
            $client = new Zend_Gdata_HttpClient();
            $client->setClientLoginToken($token);
        } catch(Exception $e) {
            // no token found so make it.
            $this->debug('newtoken');
            $client = Zend_Gdata_ClientLogin::getHttpClient($this->_user, $this->_pass, $service);
            $this->set_service_token($service,$client->getClientLoginToken());  
            /*
                    example on how to catch exceptions, not doing it here, the app needs to handle it.
                    try {
                        $client = Zend_Gdata_ClientLogin::getHttpClient($this->_user, $this->_pass, $service);
                    } catch (Zend_Gdata_App_AuthException $e) {
                        echo "Error: Unable to authenticate. Please check your";
                        echo " credentials.\n";
                        exit(1);
                    } catch (Zend_Gdata_App_CaptchaRequiredException $e) {
                        echo 'CAPTCHA answer required to login';
                        echo $e->getCaptchaUrl();
                        exit;
                        // http://code.google.com/apis/gdata/docs/auth/clientlogin.html
                    } catch (Exception $e) {
                        echo 'Unknown Exception';
                        exit;
                    }
            */
        }
        $config = array(
            'timeout' => 60 /* timeout after 60 seconds */
        );        
        $client->setConfig($config);
        return $client;

    }
    

    /**
     * Upload the specified document
     *
     * @param  Zend_Gdata_Docs $docs                  The service object to use for communicating with
     *                                                the Google Documents server.
     * @param  string          $originalFileName      The name of the file to be uploaded. The mime type
     *                                                of the file is determined from the extension on
     *                                                this file name. For example, test.csv is uploaded
     *                                                as a comma seperated volume and converted into a
     *                                                spreadsheet.
     * @param  string          $temporaryFileLocation (optional) The file in which the data for the
     *                                                document is stored. This is used when the file has
     *                                                been uploaded from the client's machine to the
     *                                                server and is stored in a temporary file which
     *                                                does not have an extension. If this parameter is
     *                                                null, the file is read from the originalFileName.
     * @return Zend_Gdata_Docs_DocumentListEntry
     */
    function uploadDocument($fileLocation, $fileName) {
		
    	//$this->client->setHeaders(array());
    	//$this->docs = new Zend_Gdata_Docs($this->client);
	

        // get mimetype from original file name
        $filenameParts = explode('.', $fileLocation);
        $fileExtension = end($filenameParts);
        $mimeType = Zend_Gdata_Docs::lookupMimeType($fileExtension);
        if(!$mimeType){
            $mimeType = $this->mimetype($fileLocation);
        }
        if(!$mimeType){
            throw new Exception("No Mime Type!");
            return false;
        }
        
        // Upload the file and convert it into a Google Document. The original
        // file name is used as the title of the document and the mime type
        // is determined based on the extension on the original file name.
        $e=true;
        $counter=0;
        while($e && $counter<10){
            try {
                $this->debug('upload');
                //uploadFile(string $fileLocation, string $title = null, string $mimeType = null, string $uri = null)
                $newDocumentEntry = $this->docs->uploadFile($fileLocation, $fileName, $mimeType, Zend_Gdata_Docs::DOCUMENTS_LIST_FEED_URI);
                $e=false;
            } catch (Zend_Gdata_App_HttpException $e){
                $r = $e->getResponse();
                if($r->getStatus() == '408'){
                    // timed out
                    $counter++;
                    $this->debug('try again');
                } else {
                    echo "<b style='color:red;font-size:1em;'>GOOGLE ERROR: ".$r->getMessage()." : ".$r->getBody()."</b><br/>\n";
                    $counter=10;// stop loop
                }                
                $e=true;
            }
        }
        if($counter==10){
            throw new Exception("failed to upload file");
            return false;
        }
        return $newDocumentEntry;
    }

    /**
     * get the mimetype for the file
     *
     * @param  string          $file               Link to the source file to download
     *
     * @return string
     */
    function mimetype($file){
        if(class_exists('finfo')){
            // new way, must be installed on php
            $this->finfo = new finfo(FILEINFO_MIME,'/usr/share/file/magic'); // use to return mime type ala mimetype extension
            if(!$this->finfo){
                $mimetype='unknown';
            } else {
                $mimetype = $this->finfo->file($file);
            }
        } else {
            $mimetype = mime_content_type($file);
        }
        return $mimetype;
    }

    /**
     * Upload the specified document
     *
     * @param  string          $src_url               Link to the source file to download
     * @param  string          $format                format of file to download
     *                                                http://code.google.com/apis/documents/docs/3.0/developers_guide_protocol.html#DownloadingSpreadsheets
     * @param  string          $gid                   The gid parameter is an absolute identifier for worksheets
     *                                                for spreadsheets (if not numberic it will download entire workbook as one sheet)
     *
     *
     * @return Zend_Gdata_Docs_DocumentListEntry
     */
    function downloadSrc($src_url, $format='csv', $gid=0, $file=false) {
        // find service based on url
        $service = $this->src_url_service($src_url);
        // authenticate to service
        $this->getClientLoginHttpClient($service);
        // get the token from the service
        $sessionToken = $this->get_service_token($service);
        // now try to do our thing...
        $opts = array(  
            'http' => array(
                'method' => 'GET',  
                'header' => "GData-Version: 3.0\r\n".  
                "Authorization: GoogleLogin auth=$sessionToken\r\n"
            )  
        );  
        // BUILD URL
        $src_url =  $src_url . '&chrome=false';
        if($format){
            $src_url =  $src_url . '&format='.$format.'&exportFormat='.$format.'';
        }
        if(is_numeric($gid)){
            $src_url =  $src_url . '&gid='.$gid.'';
        }
        // GET DATA
        $data = file_get_contents($src_url, false, stream_context_create($opts));

        if($file){
            file_put_contents($file,$data);
        }
        return $data;
    }  

    /**
     * Upload the specified document
     *
     * @param  string          $src_url               Link to the source file to download
     * @param  string          $format                format of file to download
     *                                                http://code.google.com/apis/documents/docs/3.0/developers_guide_protocol.html#DownloadingSpreadsheets
     * @param  string          $gid                   The gid parameter is an absolute identifier for worksheets
     *                                                for spreadsheets (if not numberic it will download entire workbook as one sheet)
     * @param  string          $file                  location of the file to save the data to
     *
     *
     * @return Zend_Gdata_Docs_DocumentListEntry
     */
    // curl -o tmp1 -H "Authorization: GoogleLogin auth={authcode}" "http://spreadsheets.google.com/feeds/download/spreadsheets/Export?key={dockey}&exportFormat={format}"
    private function curlSrc($src_url, $format='csv', $gid=0, $file=false){
        // find service based on url
        $service = $this->src_url_service($src_url);
        // authenticate to service
        $this->getClientLoginHttpClient($service);
        // get the token from the service
        $sessionToken = $this->get_service_token($service);
        // now try to do our thing...
        if($file){ // open file if saving to file.
            $file = fopen($file,"w+");
        }
        // BUILD URL
        $src_url =  $src_url . '&chrome=false';
        if($format){
            $src_url =  $src_url . '&format='.$format.'&exportFormat='.$format.'';
        }
        if(is_numeric($gid)){
            $src_url =  $src_url . '&gid='.$gid.'';
        }
        // INIT CURL
        $curl = curl_init($src_url);
        // Setup headers - I used the same headers from Firefox version 2.0.0.6
        // below was split up because php.net said the line was too long. :/
        $header[] = "GData-Version: 3.0";
        $header[] = "Authorization: GoogleLogin auth=$sessionToken";
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);// follow redirects!!
        curl_setopt($curl, CURLOPT_POST, false); 
        curl_setopt($curl, CURLINFO_HEADER_OUT,true); // TRUE to track the handle's request string. 

        if($file){
            curl_setopt($curl, CURLOPT_FILE,$file); // file to write output to
            $data = curl_exec($curl); // execute the curl command
        } else {
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // output to variable
            $data = curl_exec($curl); // execute the curl command
        }

        // debug info
        if($this->_debug){
            echo curl_getinfo($curl, CURLINFO_HEADER_OUT);
            var_dump($data);
        }

        curl_close($curl); // close the connection
        return $data;
    }

    private function src_url_service($src_url){
        if(stristr($src_url,'spreadsheet')){
            return Zend_Gdata_Spreadsheets::AUTH_SERVICE_NAME;
        } else {
            return Zend_Gdata_Docs::AUTH_SERVICE_NAME;
        }
        // not sure how to handle pdg with Zend.
        
        // http://code.google.com/apis/documents/docs/3.0/developers_guide_protocol.html#DownloadingDocs
    }

    
    
   /*public static function generateFileName($length=8) {
		$chars = "abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$i = 0;
		$name = "";
		while ($i <= $length) {
			$name .= $chars{mt_rand(0,strlen($chars))};
			$i++;
		}
		return $name;
	}*/

}
