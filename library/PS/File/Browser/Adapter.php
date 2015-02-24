<?php
class PS_File_Browser_Adapter {
	protected $templates;
  	protected $displaydot;
  	protected $template;
  	protected $version;
  	protected $allocatedSpace;	// Space allocated to user for upload files on server in bytes - null:restricted, 0:unlimited, any integer:no. of bytes allowed
  	
  	protected $requestUri;
  	protected $requestParams;
  	protected $requestBaseUrl;
  	
  	protected $moduleName;
  	protected $controllerName;
  	protected $actionName;
  	
  	protected $frontControllerObj;
  	protected $viewObj;
  	protected $templateObj;
  	
  	protected $baseDir;	// path allocated for user on server
  	protected $baseDirSize;
  	
  	public $separator, $sMessage, $sError;

    private $aFileTypes;
    private $allowedFileTypes;	// types of file to be allowed to upload on server "jpg,bmp,gif"

  	public function __construct($FrontController) {
  		$this->frontControllerObj = $FrontController;
  		$RequestObj = $this->frontControllerObj->getRequest();
  		$this->viewObj = $this->frontControllerObj->view;
  		 		
    	$this->requestUri = $RequestObj->getRequestUri();
    	$this->requestParams = $RequestObj->getParams();
    	$this->requestBaseUrl = $RequestObj->getBaseUrl();
    	
    	$this->moduleName = $RequestObj->getModuleName();
    	$this->controllerName = $RequestObj->getControllerName();
    	$this->actionName = $RequestObj->getActionName();
    	
		$this->templates = null;
		$this->displaydot = false;
		$this->template = null;
		$this->version = "1.3.1";
		$this->allocatedSpace = null;
		
		if (strtoupper(substr(PHP_OS, 0, 3) == 'WIN')) {
            $this->separator = "\\";
        } else { 
            $this->separator = "/";
        }
        
        $this->sError='';
        $this->sMessage='';
            	
    	$this->aFileTypes=array(
								'PlainText' => array(
													'as'=>'','asp'=>'Active Server Page','aspx'=>'','atom'=>'','bat'=>'Batch','cfm'=>'','cmd'=>'','hta'=>'','htm'=>'HTML Document','html'=>'HTML Document','js'=>'Java Script','jsp'=>'Java Server Page','java'=>'Java','mht'=>'','php'=>'PHP Script','pl'=>'Perl script','py'=>'','rb'=>'','rss'=>'','sh'=>'Shell Script','txt'=>'Text File','xhtml'=>'','xml'=>'Xtensible Markup Language','log'=>'Log File','out'=>'','ini'=>'Configuration File','shtml'=>'HTML Document','xsl'=>'','xslt'=>'','backup'=>'Backup File'
													)
								,'Compressed' => array(
													'zip'=>'Zip Archive','rar'=>'Rar Archive','tar'=>'Tape Archive','gz'=>'GZIP Compressed'
													)
								,'Others' => array(
												'ai'=>'Adobe Illustrator','aif'=>'Audio Interchange','aiff'=>'Audio Interchange','ani'=>'Animated Cursor','ans'=>'ANSI Text','api'=>'Application Program Interface','app'=>'Macromedia Authorware Package','arc'=>'ARC Compressed','arj'=>'ARJ Compressed','asc'=>'ASCII Text','asf'=>'Active Streaming','asm'=>'Assembly Source Code','avi'=>'AVI Movie','bak'=>'Backup Copy','bas'=>'BASIC Program','bk$'=>'Backup','bk'=>'Backup Copy','c'=>'C Program','cab'=>'Microsoft Compressed','cdr'=>'CorelDraw','cdt'=>'CorelDraw Template','cdx'=>'FoxPro Database Index','cfg'=>'Configuration','cgi'=>'Common Gateway Interface','cpp'=>'C++ Program','css'=>'Cascading Style Sheet','csv'=>'Comma Delimited','cur'=>'Windows Cursor Image','dat'=>'Data','db'=>'Table - Paradox','dbc'=>'Visual FoxPro Database','dbf'=>'dBASE Database','dbt'=>'dBASE Database Text','doc'=>'Document file','docx'=>'Document file','drv'=>'Driver','eml'=>'Electronic Mail','enc'=>'Encoded','eps'=>'Encapsulated PostScript','exe'=>'Executable','fax'=>'Fax','fnt'=>'Font','fon'=>'Bitmapped Font','fot'=>'TrueType Font','h'=>'C Header','hlp'=>'Help','it'=>'MOD Music','lib'=>'Library','lst'=>'List','mime'=>'MIME','mme'=>'MIME Encoded','mov'=>'QuickTime Movie','movie'=>'QuickTime movie','mp2'=>'MP2 Audio','mp3'=>'MP3 Audio','mpe'=>'MPEG','mpeg'=>'MPEG Movie','mpg'=>'MPEG Movie','pas'=>'Pascal Program','phps'=>'PHP Source Code','phtml'=>'HTML Document','pjx'=>'Visual FoxPro Project','pps'=>'PowerPoint Slideshow','ppt'=>'PowerPoint Presentation','psd'=>'Photoshop Document','qt'=>'QuickTime Movie','qtm'=>'QuickTime Movie','ra'=>'Real Audio','ram'=>'Real Audio','reg'=>'Registration File','rm'=>'Real Media','s'=>'Assembly Language','sit'=>'StuffIT Compressed','snd'=>'Sound File','swf'=>'Flash Movie','swp'=>'Swap Temporary File','sys'=>'System File','tga'=>'TARGA Graphics','tgz'=>'Tape Archive','tmp'=>'Temporary File','ttf'=>'TrueType Font','uu'=>'Uuencode Compressed','uue'=>'Uuencode File','vbp'=>'Visual Basic Project','vbx'=>'Visual Basic Extension','wab'=>'Windows Address Book','wav'=>'Sound File','xlm'=>'Excel Macro','xls'=>'Excel Worksheet','xlsx'=>'Excel Worksheet','xlt'=>'Excel Template','xm'=>'MOD Music','xxe'=>'Xxencoded File','z'=>'Unix Archive'
												)
								,'ImageType' => array(
													'bm'=>'','bmp'=>'Bitmap','ras'=>'','rast'=>'','fif'=>'','flo'=>'','turbot'=>'','g3'=>'','gif'=>'Graphics Interchange','ief'=>'','iefs'=>'','jfif'=>'','jfif-tbnl'=>'','jpe'=>'','jpeg'=>'JPEG Image','jpg'=>'JPEG Image','jut'=>'','nap'=>'','naplps'=>'','pic'=>'','pict'=>'','jfif'=>'','jpe'=>'','jpeg'=>'JPEG Image','jpg'=>'JPEG Image','png'=>'','x-png'=>'','tif'=>'TIFF Graphics','tiff'=>'TIFF Graphics','mcf'=>'','dwg'=>'AutoCAD Vector','dxf'=>'','svf'=>'','fpx'=>'','fpx'=>'','rf'=>'','rp'=>'','wbmp'=>'','xif'=>'','xbm'=>'','ras'=>'','dwg'=>'AutoCAD Vector','dxf'=>'','svf'=>'','ico'=>'Icon','art'=>'','jps'=>'','nif'=>'','niff'=>'','pcx'=>'','pct'=>'','xpm'=>'','pnm'=>'','pbm'=>'','pgm'=>'','pgm'=>'','ppm'=>'','qif'=>'','qti'=>'','qtif'=>'','rgb'=>'','tif'=>'TIFF Graphics','tiff'=>'TIFF Graphics','bmp'=>'Bitmap','xbm'=>'','xbm'=>'','pm'=>'','xpm'=>'','xwd'=>'','xwd'=>''
													)
							
								);
		
		$this->allowedFileTypes=null;
								
        $this->templateObj= new PS_File_Browser_Interface_Elementary($this->requestUri,$this->requestParams,$this->requestBaseUrl,$this->moduleName,$this->controllerName,$this->actionName,$this->viewObj);
        
        unset($RequestObj,$FrontController);
	}
	
	public function __get($property)
    {
        if(in_array($property, array('templates','displaydot', 'template', 'version', 'allocatedSpace', 'requestUri', 'requestParams', 'requestBaseUrl', 'separator', 'sMessage', 'sError', 'viewObj','moduleName','controllerName','actionName','templateObj','frontControllerObj','baseDir','baseDirSize','aFileTypes','allowedFileTypes'))){
            return $this->$property;
        }else{
            $this->error('ERR05',$property);
        }
    }
    
    public function __set($property, $value)
    {
        if(in_array($property, array('templates','displaydot', 'template', 'version', 'allocatedSpace', 'requestUri', 'requestParams', 'requestBaseUrl', 'separator', 'sMessage', 'sError', 'viewObj','moduleName','controllerName','actionName','templateObj','frontControllerObj','baseDir','baseDirSize','aFileTypes','allowedFileTypes'))){
            return $this->$property = $value;
            echo $this->$property;
        }else{
            $this->error('ERR05',$property);
        }
    }
	
	function dirsize($directory) { 
		$content=opendir($directory);
		$size=0;
		while (($filename=readdir($content))!==false) {
			if ($filename!="." && $filename!="..") { 
				$path=$directory.$this->separator.$filename;
				if (is_dir($path)) 
					$size+=$this->dirsize($path);
				if (is_file($path)) 
					$size+=filesize($path);
			}
		}
		closedir($content);
		return $size;
	}
	
	function bytesize($size) { 
		if (empty($size)) 
			return number_format($size, 2)." KB"; 
		$size=$size/1024;
		if ($size < 1024) 
			return number_format($size, 2)." KB";
		elseif ($size / 1024 < 1024) 
			return number_format($size/1024, 2)." MB";
		elseif ($size / 1024 / 1024 < 1024) 
			return number_format($size / 1024 / 1024, 2)." GB";
		else 
			return number_format($size / 1024 / 1024 / 1024, 2)." TB";
	}
	
	function error($code,$text='') {
		switch($code) {
			case 'ERR01': 
				$error='Invalid interface path. Please make a proper call for the File Browser Class.'; 
				break;
		  	case 'ERR02': 
		  		$error='Valid interface path but the template you have selected does not exist.'; 
		  		break;
		  	case 'ERR03':
		  		$error='Invalid Url Specified. You donot have permissions to access Directory other than your Base Directory.'; 
		  		break;
		  	case 'ERR04':
		  		$error='Please Select files or directories to perform Action.'; 
		  		break;
		  	case 'ERR05':
		  		$error='property '.$text.' does not exist in '.__CLASS__.' class';
		  		break;
		  	case 'ERR06':
		  		$error="Can't Archive file \"".$text."\" .";
		  		break;
		  	case 'ERR07':
		  		$error='Failed can\'t open archive.';
		  		break;
		  	case 'ERR08':
		  		$error="Can't Delete File \"".$text."\" .";
		  		break;
		  	case 'ERR09':
		  		$error="Directory Not Writable, Can't Create file.";
		  		break;
		  	case 'ERR10':
		  		$error=" \"$text\" File already exist.";
		  		break;
		  	case 'ERR11':
		  		$error=" \"$text\" Directory already exist.";
		  		break;
		  	case 'ERR12':
		  		$error="\"$text\" Uploading Error.";
		  		break;
		  	case 'ERR13':
		  		$error="Can't Delete Directory \"".$text."\" .";
		  		break;
		  	case 'ERR14':
		  		$error="Directory name \"".$text."\" is not Valid.";
		  		break;
		  	case 'ERR15':
		  		$error="File name \"".$text."\" is not Valid.";
		  		break;
		  	case 'ERR16':
		  		$error="Please enter \"".$text."\" name to create.";
		  		break;
		  	case 'ERR17':
		  		$error="A \"".$text."\" name can not contain any of following character: \\ / : * ? \" < > |";
		  		break;
		  	case 'ERR18':
		  		$error=$text;
		  		break;
		  	case 'ERR19':
		  		$error=$text." name should contain characters between 1 and 255.";
		  		break;
		  	case 'ERR20':
		  		$error="Parent Dir \"".$text."\" does not exist.";
		  		break;
		  	case 'ERR21':
		  		$error="Dir \"".$text."\" already exist.";
		  		break;
		  	case 'ERR22':
		  		$error="File \"".$text."\" already exist.";
		  		break;
		  	case 'ERR23':
		  		$error="File \"".$text."\" is not a valid File.";
		  		break;
		  	case 'ERR24':
		  		$error="Cannot receive file for some reason.";
		  		break;
		  	case 'ERR25':
		  		$error="Errors in receiving file \"".$text."\"";
		  		break;
		  	case 'ERR26':
		  		$error="Not enough free space available to upload file. Free up to \"".$text."\" of space to upload.";
		  		break;
		  	case 'ERR27':
		  		$error="Permission denied. Restricted or you are currently not allowed to \"".$text."\".";
		  		break;
		  	case 'ERR28':
		  		$error="\"$text\" file type not allowed.";
		  		break;
		}
		if (isset($error)) {
			$return = '';
			$return .= '			<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">';
			$return .= '				<tr>';
			$return .= '					<td align="left" bgcolor="#BF000D"   height="21" nowrap="nowrap" style="padding-left: 05px; padding-right: 10px"><font face="Tahoma" size=1 style="font-size: 8pt; text-decoration: none;" color="#FFFFFF"><b>Error:</b></font></td>';
			$return .= '					<td align="left" bgcolor="#BF000D"   width="100%" height="21" nowrap="nowrap" style="padding-left: 05px; padding-right: 10px"><font face="Tahoma" size=1 style="font-size: 8pt; text-decoration: none;" color="#FFFFFF" width="100%"><b>'.$error.'</b></font></td>';
			$return .= '					<td align="right" bgcolor="#BF000D"  height="21" nowrap="nowrap" style="padding-left: 05px; padding-right: 05px"><font face="Tahoma" size=1 style="font-size: 8pt; text-decoration: none;" color="#FFFFFF"><b>Please fix it.</b></font></td>';
			$return .= '				</tr>';
			$return .= '			</table>'; 
			$this->sError .= $return;
		}
	}
	
	function message($code,$text='') {
		switch($code) {
			case 'MSG01': 
				$message="File archived Successfully: \"".$text."\" .";
				break;
			case 'MSG02': 
				$message="Directory Deleted Successfully: \"".$text."\" .";
				break;
			case 'MSG03': 
				$message="File Deleted Successfully: \"".$text."\" .";
				break;
			case 'MSG04': 
				$message="File Created Successfully: \"$text\" .";
				break;
			case 'MSG05': 
				$message="Directory Created Successfully: \"$text\" .";
				break;
			case 'MSG06': 
				$message="\"$text\" File Successfully Uploaded.";
				break;
		}
		if (isset($message)) {
			$return = '';
			$return .= '			<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">';
			$return .= '				<tr>';
			$return .= '					<td align="left" bgcolor="#008000"   height="21" nowrap="nowrap" style="padding-left: 05px; padding-right: 10px"><font face="Tahoma" size=1 style="font-size: 8pt; text-decoration: none;" color="#FFFFFF"><b>Message:</b></font></td>';
			$return .= '					<td align="left" bgcolor="#008000"   width="100%" height="21" nowrap="nowrap" style="padding-left: 05px; padding-right: 10px"><font face="Tahoma" size=1 style="font-size: 8pt; text-decoration: none;" color="#FFFFFF" width="100%"><b>'.$message.'</b></font></td>';
			$return .= '					<td align="right" bgcolor="#008000"  height="21" nowrap="nowrap" style="padding-left: 05px; padding-right: 05px">&nbsp;</td>';
			$return .= '				</tr>';
			$return .= '			</table>'; 
			$this->sMessage .= $return;
		}
	}
	
	function filetypes($extension=null,$advanced=false,$hidden=false) {
		if ($advanced==true) {
			$filetypes[null]="";
			$ExtChk = $this->chkExtention($extension);
			if($ExtChk!==false) {
				$filetypes[$extension]=$ExtChk;
			}
		}
		if ($hidden==true) 
			return "Hidden File";
		if (!empty($advanced) && isset($filetypes[$extension]) && !empty($filetypes[$extension])) 
			return $filetypes[$extension];
		else 
			return strtoupper($extension).' File';
	}
	
	function row($type,$path,$title,$adtype) {
		unset($filename);
		$properties=pathinfo($title); 
		if (!isset($properties['extension'])) 
			$properties['extension']=null;
		if (substr($title,0,1)=='.') 
			$hidden=true; 
		else 
			$hidden=false;
		switch($title) {
			case '.':  
				$filename['title']='[.]';
		        $filename['path']=$path;
				$filename['type']=(empty($type)?$this->filetypes($properties['extension'],$adtype,$hidden):"Folder");
				$filename['size']=""; //"--- ---";
				$filename['icon']="folder";
				$filename['physicalType']="folder";
				$filename['link']=$this->viewObj->url(array('controller'=>$this->controllerName,'action'=>$this->actionName,'module'=>$this->moduleName,'dir'=>base64_encode(realpath($path))), null, true);
				$filename['physicalLink']=realpath($path);
				$filename['update']=date("F dS, Y - H:i",filemtime($path.$this->separator.$title));
				break;
		  	case '..': 
		  		$filename['title']='[..]';
				$filename['path']=$path;
				$filename['type']=(empty($type)?$this->filetypes($properties['extension'],$adtype,$hidden):"Folder");
				$filename['size']=""; //"--- ---";
				$filename['icon']="folder";
				$filename['physicalType']="folder";
				$filename['link']=$this->viewObj->url(array('controller'=>$this->controllerName,'action'=>$this->actionName,'module'=>$this->moduleName,'dir'=>base64_encode(realpath($path.$this->separator.$title))), null, true);
				$filename['physicalLink']=realpath($path.$this->separator.$title);
				$filename['update']=date("F dS, Y - H:i",filemtime($path.$this->separator.$title));
				break;
		 	default:   
		 		$filename['title']=$title;
				$filename['path']=$path;
				if (empty($type)) {
					$filename['type']=(empty($type)?$this->filetypes($properties['extension'],$adtype,$hidden):"Folder");
					$filename['size']=$this->bytesize(filesize($path.$this->separator.$title));
					$filename['icon']="filename";
					$filename['physicalType']="filename";
					$filename['link']=$this->viewObj->url(array('controller'=>$this->controllerName,'action'=>$this->actionName,'module'=>$this->moduleName,'dir'=>base64_encode(realpath($path)),'download'=>base64_encode(realpath($path.$this->separator.$filename['title']))), null, true);
					$filename['physicalLink']=realpath($path.$this->separator.$filename['title']);
					$filename['update']=date("F dS, Y - H:i",filemtime($path.$this->separator.$title));
				} else {
					$filename['type']=(empty($type)?$this->filetypes($properties['extension'],$adtype,$hidden):"Folder");
					$filename['size']=''; //$this->bytesize(filesize($path.$this->separator.$title)); //$this->bytesize($this->dirsize($path.$this->separator.$title));
					$filename['icon']="folder";
					$filename['physicalType']="folder";
					$filename['link']=$this->viewObj->url(array('controller'=>$this->controllerName,'action'=>$this->actionName,'module'=>$this->moduleName,'dir'=>base64_encode(realpath($path.$this->separator.$filename['title']))), null, true);
					$filename['physicalLink']=realpath($path.$this->separator.$filename['title']);
					$filename['update']=date("F dS, Y - H:i",filemtime($path.$this->separator.$title));
				}
				break;
		}
		$this->templateObj->layout_row($filename);
	}
	
	function file_browser($chrooted=false,$SpaceAllocation=null,$FileTypesAllowed=null,$adtype=true) {
		$Secured=false;
		if(!empty($chrooted) && !($chrooted===false)) {
			if (is_dir($chrooted)) {
				$Secured=true;
				$this->baseDir = realpath($chrooted);
			}
		} else if($chrooted===false) {
			$Secured=true;
			$this->baseDir = realpath('./');
		}
		
		if($Secured) {
			set_time_limit(0);
			$this->baseDirSize = $this->dirsize($this->baseDir);
			$this->allocatedSpace = $SpaceAllocation;
			$this->allowedFileTypes = $FileTypesAllowed;
			
			if (!empty($this->requestParams['download'])) {
				$DownloadUrl = $this->requestParams['download'];
				if($this->chkUrlIntegrity($DownloadUrl)) {
					$this->frontControllerObj->getHelper('layout')->disableLayout();
		    		$this->frontControllerObj->getHelper('viewRenderer')->setNoRender();
		    		$this->downloadFile(base64_decode($DownloadUrl));
		    		exit;
				} else {
					$this->error('ERR03');
				}
			}
			
			if (!empty($this->requestParams['view'])) {
				$ViewUrl = $this->requestParams['view'];
				if($this->chkUrlIntegrity($ViewUrl)) {
					$this->frontControllerObj->getHelper('layout')->disableLayout();
		    		$this->frontControllerObj->getHelper('viewRenderer')->setNoRender();
		    		$this->viewFile(base64_decode($ViewUrl));
		    		exit;
		    	} else {
					$this->error('ERR03');
				}
			}
			
			if (!empty($this->requestParams['delete'])) {
				$AllocatedSpace = $this->chkAllocationSpace();
		    	if($AllocatedSpace===null || $AllocatedSpace=='restricted') {
		    		$this->error('ERR27','delete file');
		    	} else {	    	
					$DeleteUrl = $this->requestParams['delete'];
					if($this->chkUrlIntegrity($DeleteUrl)) {
		    			$this->deleteFiles(array(base64_decode($DeleteUrl)));
		    		} else {
						$this->error('ERR03');
					}
		    	}
			}
			
			if (!empty($this->requestParams['massDelete']) && $this->requestParams['massDelete']=="Delete" && !empty($this->requestParams['chkfiles'])) {
				$AllocatedSpace = $this->chkAllocationSpace();
		    	if($AllocatedSpace===null || $AllocatedSpace=='restricted') {
		    		$this->error('ERR27','delete selected files');
		    	} else {
					$DeleteFilesArr = $this->requestParams['chkfiles'];
					if(is_array($DeleteFilesArr) && count($DeleteFilesArr) > 0) {
						$SecuredDeleteUrls = array();
						for($i=0;$i<count($DeleteFilesArr);$i++) {
							if($this->chkUrlIntegrity($DeleteFilesArr[$i])) {
				    			$SecuredDeleteUrls[] = base64_decode($DeleteFilesArr[$i]);
				    		}
						}
						if(is_array($SecuredDeleteUrls) && count($SecuredDeleteUrls) > 0) {
							$this->deleteFiles($SecuredDeleteUrls);
						} else {
							$this->error('ERR03');
						}
					} else {
						$this->error('ERR04');
					}
		    	}
			}

			if (!empty($this->requestParams['massDownload']) && $this->requestParams['massDownload']=="Download" && !empty($this->requestParams['chkfiles'])) {
				$DownloadFilesArr = $this->requestParams['chkfiles'];
				if(is_array($DownloadFilesArr) && count($DownloadFilesArr) > 0) {
					$SecuredDownloadUrls = array();
					for($i=0;$i<count($DownloadFilesArr);$i++) {
						if($this->chkUrlIntegrity($DownloadFilesArr[$i])) {
			    			$SecuredDownloadUrls[] = base64_decode($DownloadFilesArr[$i]);
			    		}
					}
					if(is_array($SecuredDownloadUrls) && count($SecuredDownloadUrls) > 0) {
						$this->frontControllerObj->getHelper('layout')->disableLayout();
		    			$this->frontControllerObj->getHelper('viewRenderer')->setNoRender();
						$ArchivePath = $this->createArchive($SecuredDownloadUrls);
						$this->downloadFile($ArchivePath);
						exit;
					} else {
						$this->error('ERR03');
					}
				} else {
					$this->error('ERR04');
				}
			}
			
			if (!empty($this->requestParams['createDirectory']) && $this->requestParams['createDirectory']=="Create Directory" && !empty($this->requestParams['txtDFName'])) {
				$DirName = trim($this->requestParams['txtDFName']);
				$parentDir = (!empty($this->requestParams['dir'])) ? $this->requestParams['dir'] : $this->baseDir;
				
				if($this->chkUrlIntegrity($parentDir)) {
					$validDFNameFlg = $this->validateDirFileName(base64_decode($parentDir), $DirName,'directory');
					if($validDFNameFlg) {
	    				$this->createDirectory(base64_decode($parentDir), $DirName);
					} else {
						$this->error('ERR14',$DirName);
					}
	    		} else {
					$this->error('ERR03');
				}
			}
			
			if (!empty($this->requestParams['createFile']) && $this->requestParams['createFile']=="Create File" && !empty($this->requestParams['txtDFName'])) {
				$FileName = trim($this->requestParams['txtDFName']);
				$parentDir = (!empty($this->requestParams['dir'])) ? $this->requestParams['dir'] : $this->baseDir;
				
				if($this->chkUrlIntegrity($parentDir)) {
					$validDFNameFlg = $this->validateDirFileName(base64_decode($parentDir), $FileName,'file');
					if($validDFNameFlg) {
	    				$this->createFile(base64_decode($parentDir), $FileName);
					} else {
						$this->error('ERR15',$FileName);
					}
	    		} else {
					$this->error('ERR03');
				}
			}
			
			if (!empty($this->requestParams['fileUpload']) && $this->requestParams['fileUpload']=="Upload") {
				$parentDir = (!empty($this->requestParams['dir'])) ? $this->requestParams['dir'] : $this->baseDir;
				
				if($this->chkUrlIntegrity($parentDir)) {
					$this->uploadFile(base64_decode($parentDir),'uploadFile');
	    		} else {
					$this->error('ERR03');
				}
			}
						
			foreach(array_map('chr',range(ord('a'),ord('z'))) as $letter) 
				$letters[]=$letter;
			$scans=array('folders'=>array(),'files'=>array());
	
			$this->templateObj->regexFileTypes=$this->createRegexFileTypes();
			$this->templateObj->layout_javascript();
			$this->templateObj->layout_header();
			if (empty($select) && !empty($this->requestParams['select'])) 
				$select=$this->requestParams['select']; 
			else 
				$select=null;
			if (empty($directory) && !empty($this->requestParams['dir'])) 
				$directory=base64_decode($this->requestParams['dir']);
			if (empty($chrooted) OR !is_dir(realpath($chrooted))) 
				$chrooted=realpath('./'); 
			else 
				$chrooted=realpath($chrooted);
				
			$this->baseDir = $chrooted;
			$this->baseDirSize = $this->dirsize($this->baseDir);
			
			if (empty($directory)) 
				$directory=$chrooted;
			$directory=realpath($directory);
			if($this->chkUrlIntegrity(base64_encode($directory))===false)
				$directory=$chrooted;
			if (!empty($select)) 
				$filter=true; 
			else 
				$filter=false;
			if (version_compare("5.0",phpversion())<0) 
				$scan=scandir($directory);
			else {
				$scanning=opendir($directory); 
				while (false!==($filename=readdir($scanning))) 
					$scan[]=$filename; 
			}
			foreach($scan as $scanned) {
				if (is_dir($directory.$this->separator.trim($scanned))) 
					$inarray="folders"; 
				else 
					$inarray="files";
			  	switch($select) {
			  		case null: 
			  			$scans[$inarray][]=trim($scanned); 
			  			break;
			    	case "numbers": 
			    		if (!in_array(strtolower(substr(trim($scanned),0,1)),$letters)) 
			    			$scans[$inarray][]=trim($scanned); 
			    		break;
			    	default: 
			    		if (strtolower(substr(trim($scanned),0,1))==strtolower($select)) 
			    			$scans[$inarray][]=trim($scanned); 
			    		break;
			  	}
			}
			$this->getCurrentDir($directory);
			$CurrDirSize = $this->dirsize($directory);
			$this->getSpaceAvailibilityStat($CurrDirSize);
			$this->templateObj->layout_letters($chrooted,$directory,$select);
			$this->templateObj->layout_headings($directory);
			if (count($scans['folders'])+count($scans['files'])==0) 
				$this->templateObj->layout_nofiles($filter);
			if ($this->displaydot==false) 
				array_shift($scans['folders']);
			foreach($scans['folders'] as $title) 
				$this->row(true,$directory,$title,$adtype);
			foreach($scans['files']   as $title) 
				$this->row(false,$directory,$title,$adtype);
			$this->templateObj->layout_footer($directory,$select,$this->version,$this->baseDir);
			
			$return = $this->templateObj->getLayout();
			
			if(!empty($this->sError))
				$return = $this->sError . $return;
			
			if(!empty($this->sMessage))
				$return = $this->sMessage . $return;
				
			return $return;
		} else {
			return null;
		}
	}
	
	public function validateDirFileName($ParentDir,$DirFileName='',$validationOn='file') {
		$FldValue = trim($DirFileName);
		
		$AllocatedSpace = $this->chkAllocationSpace();
    	if($AllocatedSpace===null || $AllocatedSpace=='restricted') {
    		$this->error('ERR27','create '.$validationOn);
    		return false;
    	}
    	
    	if($FldValue=="Directory/File Name") {
    		$this->error('ERR16', $validationOn);
    		return false;
    	}

        $validFlg=false;
		if ($validationOn=="file" && preg_match('/^[^\\/\:\*\?\"\<\>\|\.]+(\.[^\\/\:\*\?\"\<\>\|\.]+)+$/', $FldValue)) {
			$validFlg=true;
		}
		if ($validationOn=="directory" && preg_match('/^[^\\/\:\*\?\"\<\>\|\.]+(\.[^\\/\:\*\?\"\<\>\|\.]+)*$/', $FldValue)) {
			$validFlg=true;
		}
        if($validFlg==false) {
       		$this->error('ERR17',$validationOn);
    		return false;
    	}
    	
		if ((strlen($FldValue) < 1) || (strlen($FldValue) > 255)) {
			$this->error('ERR19',$validationOn);
    		return false;
		}

        if(is_dir($ParentDir)) {
	        if($validationOn=="directory") {
	        	if(is_dir($ParentDir.$this->separator.$FldValue)) {
					$this->error('ERR21',basename($ParentDir.$this->separator.$FldValue));
					return false;
	        	}
	        }
	        if($validationOn=="file") {
	        	if(file_exists($ParentDir.$this->separator.$FldValue)) {
					$this->error('ERR22',basename($ParentDir.$this->separator.$FldValue));
					return false;
	        	}
	        	
	        	$sExt = strtolower(substr(strrchr($FldValue,'.'),1));
		    	$fileAllowedFlg = $this->chkExtention($sExt,true);
				if($fileAllowedFlg===false) {
		    		$this->error('ERR28','.'.$sExt);
		    		return false;
		    	}
	        }
        } else {
        	$this->error('ERR20',basename($ParentDir));
        	return false;
        }
        
    	return true;
	}
	
	public function chkUrlIntegrity($Url) {
		if (substr(base64_decode($Url),0,strlen($this->baseDir))!=$this->baseDir) {
			return false;
		} else {
			return true;
		}
	}
	
	public function getEnvTmpPath() {
		$return = '';
		
		$return = getenv('TMPDIR');
		if(!empty($return) && is_dir(realpath($return)))
			return $return;
		
		$return = getenv('TMP');
		if(!empty($return) && is_dir(realpath($return)))
			return $return;
			
		$return = getenv('TEMP');
		if(!empty($return) && is_dir(realpath($return)))
			return $return;
		
		$return = '/tmp';
		if(!empty($return) && is_dir(realpath($return)))
			return $return;
		
		return null;
	}
	
	public function createArchive($aFiles,$file='') {
		if(empty($file)) {
			$tmpPath = $this->getEnvTmpPath();
			
			$path = (empty($tmpPath)) ? realpath('./') . $this->separator . 'temp' : $tmpPath;
			$path = realpath($path);
			
			if(!is_dir($path)) {
				@mkdir($path,0777);
				@chmod($path,0777);
			}
			
			$ZipFileFolderName = time();
			$file = $path . $this->separator . $ZipFileFolderName . '.zip';
		} else {
			$filePath = pathinfo($file);
			$ZipFileFolderName = basename($filePath['filename']);
			unset($filePath);
		}

		$oZip = new ZipArchive();
		
		if ($oZip->open($file,ZIPARCHIVE::CREATE) === TRUE) {
			$dir='';
			$oZip->addEmptyDir($ZipFileFolderName);
			
	        if (is_array($aFiles)) {
	            foreach ($aFiles as $aFilesNames){
	            	$WorkingDir=$ZipFileFolderName;
	                if (is_dir($dir.$aFilesNames)) {
	                	if(!empty($WorkingDir))
	                		$WorkingDir .= $this->separator . basename($dir.$aFilesNames);
	                	else
		                	$WorkingDir = basename($dir.$aFilesNames);
		                	
	                	$oZip->addEmptyDir($WorkingDir);
	                    $this->prepare_archive($oZip,$dir.$aFilesNames,$WorkingDir);
	                }else{
	                	if(!empty($WorkingDir))
	                		$WorkingFile = $WorkingDir . $this->separator . basename($dir.$aFilesNames);
	                	else
	                		$WorkingFile = basename($dir.$aFilesNames);
	                		
	                    if (@$oZip->addFile($dir.$aFilesNames, $WorkingFile)) {
	                        $this->message('MSG01', $WorkingFile);
	                    }else{
	                        $this->error('ERR06', $WorkingFile);
	                    }
	                    
	                    unset($WorkingFile);
	                }
	                unset($WorkingDir);
	            }
	        }
			$oZip->close();
		} else {
        	$this->error('ERR07');
        }
        
        return $file;
	}
	
	public function prepare_archive($oZip, $dirname, $WorkingDir) {
		if (is_dir($dirname))
            $dir_handle = opendir($dirname);
        if (!$dir_handle)
            return false;
        while($file = readdir($dir_handle)) {
        	$CurrentWorkingDir=$WorkingDir;
            if ($file != "." && $file != "..") {
                if (!is_dir($dirname.$this->separator.$file)) {
                	if(!empty($CurrentWorkingDir))
                		$WorkingFile = $CurrentWorkingDir . $this->separator . basename($dirname.$this->separator.$file);
                	else
                		$WorkingFile = basename($dirname.$this->separator.$file);
	                		
                    if (@$oZip->addFile($dirname.$this->separator.$file,$WorkingFile)) {
                    	$this->message('MSG01', $WorkingFile);
                    }else{
                        $this->error('ERR06', $WorkingFile);
                    }
                    
                    unset($WorkingFile);
                } else {
                	if(!empty($CurrentWorkingDir))
                		$CurrentWorkingDir .= $this->separator . basename($dirname.$this->separator.$file);
                	else 
                		$CurrentWorkingDir = basename($dirname.$this->separator.$file);
                		
                	$oZip->addEmptyDir($CurrentWorkingDir);
                    $this->prepare_archive($oZip,$dirname.$this->separator.$file,$CurrentWorkingDir);          
                }
            }
            unset($CurrentWorkingDir);
        }
        closedir($dir_handle);

        return true;
	}
	
	public function downloadFile($file){
        header ("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header('Content-Description: File Transfer');
        header('Content-Length: ' . filesize($file));
        header('Content-Disposition: attachment; filename=' . basename($file));
        header('Content-Type: application/octet-stream');
        readfile($file);
    }
    
    public function delete_directory($dirname) {
        if (is_dir($dirname))
            $dir_handle = opendir($dirname);
        if (!$dir_handle)
            return false;
        while($file = readdir($dir_handle)) {
            if ($file != "." && $file != "..") {
                if (!is_dir($dirname.$this->separator.$file))
                    if (@unlink($dirname.$this->separator.$file)) {
                        $this->message('MSG03',basename($dirname.$this->separator.$file));
                    }else{
                        $this->error('ERR08',basename($dirname.$this->separator.$file));
                    }
                else
                    $this->delete_directory($dirname.$this->separator.$file);          
            }
        }
        closedir($dir_handle);
        if (@rmdir($dirname)) {
            $this->message('MSG02',basename($dirname));
        }else{
            $this->error('ERR13',basename($dirname));
        }
        return true;
    }
    public function viewFile($file){
        $sBaseName = basename($file);
        $sExt = strtolower(substr(strrchr($sBaseName,'.'),1));
        if ($sExt == "zip") {
            $oZip = new ZipArchive;
            if ($oZip->open($file) === TRUE) {
            	$iTotalPercent=0;
                $this->templateObj->layout_archiveHeading();
                for ($i=0; $i<$oZip->numFiles;$i++) {
                   $ZipArchiveContent = array();
                   $aZipDtls = $oZip->statIndex($i);
                   if($aZipDtls['size']>0)
                   	$iPercent = 100 - round($aZipDtls['comp_size'] * 100 / $aZipDtls['size']);
                   else
                   	$iPercent = 100 - round($aZipDtls['comp_size'] * 100);
                   $iUncompressedSize = $aZipDtls['size'];
                   $iCompressedSize = $aZipDtls['comp_size'];
                   $iTotalPercent += $iPercent;
                   $ZipArchiveContent['name'] = $aZipDtls['name'];
                   $ZipArchiveContent['iUncompressedSize'] = $this->formatSize($iUncompressedSize);
                   $ZipArchiveContent['iCompressedSize'] = $this->formatSize($iCompressedSize);
                   $ZipArchiveContent['iPercent'] = $iPercent.'%';
                   $ZipArchiveContent['date'] = $this->dateFormat($aZipDtls['mtime']);
                   $this->templateObj->layout_archiveRow($ZipArchiveContent);
                   unset($ZipArchiveContent);
                }
                $ZipArchiveContent = array();
                $ZipArchiveContent['totalSize'] = $this->showFileSize($file, '');
                $ZipArchiveContent['totalFiles'] = $oZip->numFiles;
                $ZipArchiveContent['name'] = basename($file);
                if($oZip->numFiles>0)
	                $ZipArchiveContent['ratio'] = round($iTotalPercent / $oZip->numFiles)."%";
	            else 
	            	$ZipArchiveContent['ratio'] = round($iTotalPercent)."%";
                $this->templateObj->layout_archiveFooter($ZipArchiveContent);
                unset($ZipArchiveContent);
                $oZip->close();
                $archiveContent = $this->templateObj->getLayout();
                header ("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	            header('Content-Description: File View');
	            header('Content-Length: ' . strlen($archiveContent));
	            header('Content-Disposition: inline; filename=' . basename($file));
	            header('Content-Type: text/html');
                echo $archiveContent;
            } else {
                $this->error('ERR07');
            }
        }elseif (key_exists($sExt, $this->aFileTypes['PlainText'])) {
            header ("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header('Content-Description: File View');
            header('Content-Length: ' . filesize($file));
            header('Content-Disposition: inline; filename=' . basename($file));
            header('Content-Type: text/plain');
            readfile($file);
        }elseif(key_exists($sExt, $this->aFileTypes['ImageType'])){
            header ("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header('Content-Description: File View');
            header('Content-Length: ' . filesize($file));
            header('Content-Disposition: inline; filename=' . basename($file));
            header('Content-Type: image/jpg');
            readfile($file);
        }else{
            $this->downloadFile($file);
        }
    }
    private function formatSize($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
    public function dateFormat($iTimestamp) {
        return date("F j, Y, g:i a", $iTimestamp);
    }
    public function showFileSize($file, $dir, $precision = 2) {
        if (filetype($dir.$file) != "dir") {
            return $this->formatSize(filesize($dir.$file));
        }else{
            return "Dir";
        }
    }
    
	public function deleteFiles($aFiles){
		$dir='';
        if (is_array($aFiles)) {
            foreach ($aFiles as $aFilesNames){
                if (is_dir($dir.$aFilesNames)) {
                    $this->delete_directory($dir.$aFilesNames);
                }else{
                    if (@unlink($dir.$aFilesNames)) {
                        $this->message('MSG03',basename($dir.$aFilesNames));
                    }else{
                        $this->error('ERR08',basename($dir.$aFilesNames));
                    }
                }
            }
        }
    }
	public function createFile($dir, $sCreatefile){
		if(!empty($dir))
			$dir .= $this->separator;
        if (!file_exists($dir.$sCreatefile)) {
            if (is_writable($dir)) {
                $handle = fopen($dir.$sCreatefile, "w");
                fclose($handle);
                $this->message('MSG04',basename($dir.$sCreatefile));
            }else{
                $this->error('ERR09');
            }
        }else{
            $this->error('ERR10',basename($dir.$sCreatefile));
        }
    }
	private function writeBackup($sFileName){
        if (!copy($sFileName, $sFileName.".backup")) {
            return false;
        }
        return true;
    }
    public function fileWriter($sFile, $string, $backup = false) {
		if ($backup) {
            $this->writeBackup($sFile);
        }
        $fp = fopen($sFile,"w");
        //Writing to a network stream may end before the whole string is written. Return value of fwrite() is checked
        for ($written = 0; $written < strlen($string); $written += $fwrite) {
            $fwrite = fwrite($fp, substr($string, $written));
            if (!$fwrite) {
                return $fwrite;
            }
        }
        fclose($fp);
        return $written;
    }
	public function createDirectory($dir, $sCreatefile){
		if(!empty($dir))
			$dir .= $this->separator;
        if (!is_dir($dir.$sCreatefile)) {
            @mkdir($dir.$sCreatefile, 0755);
            $this->message('MSG05',basename($dir.$sCreatefile));
        }else{
			$this->error('ERR11',basename($dir.$sCreatefile));
        }
    }
	public function extract($sExtract){
        $zip = new ZipArchive;
        $path_parts = pathinfo($sExtract);
        if ($zip->open($sExtract) === TRUE) {
           $zip->extractTo($path_parts['dirname']);
           $zip->close();
           return true;
        } else {
           return false;
        }
    }
    public function uploadFile($dir, $sFileName = null){
    	if(!empty($dir))
			$dir .= $this->separator;
		
		$FileUploadObj = new Zend_File_Transfer_Adapter_Http;
		$FileUploadObj->setDestination($dir);
		
		if($FileUploadObj->isReceived($sFileName)) {
			$FileInfo = $FileUploadObj->getFileInfo($sFileName);
			$ValidFlg = $this->validateUploadedFile($FileInfo[$sFileName]);
			
			if($ValidFlg) {
				try {
					$FileUploadObj->receive($sFileName);
					$this->message('MSG06',basename($dir.$FileInfo[$sFileName]['name']));
				} catch (Zend_File_Transfer_Exception $e) {
					$this->error('ERR18', $e->getMessage());
					$this->error('ERR12', basename($dir.$FileInfo[$sFileName]['name']));
				}
			} else {
				$this->error('ERR23', basename($dir.$FileInfo[$sFileName]['name']));
			}
		} else {
			$this->error('ERR24');
		}
    }
    
    public function validateUploadedFile($FileInfo) {
    	if(!is_dir($FileInfo['destination'])) {
    		$this->error('ERR20',basename($FileInfo['destination']));
    		return false;
    	}
    	
    	if($FileInfo['error']!=0) {
    		$this->error('ERR25',$FileInfo['name']);
    		return false;
    	}
    	
    	$validFlg=false;
		if (preg_match('/^[^\\/\:\*\?\"\<\>\|\.]+(\.[^\\/\:\*\?\"\<\>\|\.]+)+$/', $FileInfo['name'])) {
			$validFlg=true;
		}
        if($validFlg==false) {
       		$this->error('ERR17','File');
    		return false;
    	}
    	
		if ((strlen($FileInfo['name']) < 1) || (strlen($FileInfo['name']) > 255)) {
			$this->error('ERR19','File');
    		return false;
		}
    	
    	$sExt = strtolower(substr(strrchr($FileInfo['name'],'.'),1));
    	$fileAllowedFlg = $this->chkExtention($sExt,true);
		if($fileAllowedFlg===false) {
    		$this->error('ERR28',$FileInfo['type'].' - .'.$sExt);
    		return false;
    	}
    	
    	if(file_exists($FileInfo['destination'].$this->separator.$FileInfo['name'])) {
    		$this->error('ERR22',$FileInfo['name']);
    		return false;
    	}
    	
    	$AllocatedSpace = $this->chkAllocationSpace();
    	if($AllocatedSpace!=null && $AllocatedSpace!='unlimited' && $AllocatedSpace!='restricted') {
	    	$AvailableFreeSpace = $this->allocatedSpace - $this->baseDirSize;
	    	if($AvailableFreeSpace < $FileInfo['size']){
	    		$this->error('ERR26', $this->bytesize($FileInfo['size'] - $AvailableFreeSpace));
	    		return false;
	    	}
    	}
    	
    	if($AllocatedSpace===null || $AllocatedSpace=='restricted') {
    		$this->error('ERR27','upload file');
    		return false;
    	}
    	
    	return true;
    }
    
    public function createRegexFileTypes() {
		$regexFileType=null;
		if($this->allowedFileTypes!==null) {
    		if(!is_array($this->allowedFileTypes)) {
    			$FileTyp = explode(',',$this->allowedFileTypes);
    		} else {
    			$FileTyp = $this->allowedFileTypes;
    		}
    		if(is_array($FileTyp)) {
    			$regexFileType = '/(';
    			$counter=0;
    			foreach ($FileTyp as $key=>$value) {
					if(is_integer($key)) {
						if(!empty($value)) {
							$regexFileType .= '\.'.$value.'|';
							$counter++;
						}
					} else {
						if(!empty($key)) {
							$regexFileType .= '\.'.$key.'|';
							$counter++;
						}
					}
    			}
    			if(substr($regexFileType,strlen($regexFileType)-1,strlen($regexFileType))=='|')
	    			$regexFileType = substr($regexFileType,0,strlen($regexFileType)-1);
	    			
    			$regexFileType .=')$/i';
    			
    			if($counter==0)
    				$regexFileType=null;
    		}
    	} else {
    		if(is_array($this->aFileTypes)) {
    			$regexFileType = '/(';
    			$counter=0;
		    	foreach ($this->aFileTypes as $Typ=>$Types) {
		    		if(is_array($Types)) {
			    		foreach ($Types as $key=>$value) {
				    		if(!empty($key)) {
								$regexFileType .= '\.'.$key.'|';
								$counter++;
							}
			    		}
		    		}
				}
				if(substr($regexFileType,strlen($regexFileType)-1,strlen($regexFileType))=='|')
	    			$regexFileType = substr($regexFileType,0,strlen($regexFileType)-1);
	    			
    			$regexFileType .=')$/i';
    			
    			if($counter==0)
    				$regexFileType=null;
	    	}
    	}

    	return $regexFileType;
	}
    
    public function chkExtention($extention=null,$validate=false) {
    	$return=false;
    	if($validate && $this->allowedFileTypes!==null) {
    		if(!is_array($this->allowedFileTypes)) {
    			$FileTyp = explode(',',$this->allowedFileTypes);
    		} else {
    			$FileTyp = $this->allowedFileTypes;
    		}
    		if(is_array($FileTyp)) {
				if(in_array($extention,$FileTyp)) {
					return $extention;
				}
				if(key_exists($extention,$FileTyp)) {
					return $FileTyp[$extention];
				}
    		}
    		return $return;
    	}
    	if(is_array($this->aFileTypes)) {
	    	foreach ($this->aFileTypes as $key=>$value) {
				if(key_exists($extention,$value)) {
					$return=$value[$extention];
					break;
				}
			}
    	}
		return $return;
    }
    
    public function getCurrentDir($dir){
    	$aBasePath = explode($this->separator, $this->baseDir);
    	$ibcount = (count($aBasePath));
        $aCurrentPath = explode($this->separator, $dir);
        $iCount = (count($aCurrentPath));
        $sFullPath='';
        $this->templateObj->layout_currentDirHead();
        for ($i = 0; $i < $iCount; ++$i) {
            $sFullPath .= $aCurrentPath[$i].$this->separator;
            if($i >= $ibcount-1) {
	            $linkUrl = $this->viewObj->url(array('controller'=>$this->controllerName,'action'=>$this->actionName,'module'=>$this->moduleName,'dir'=>base64_encode(realpath($sFullPath))), null, true);
    	        $this->templateObj->layout_currentDir($linkUrl,$aCurrentPath[$i],$this->separator);
        	    unset($linkUrl);
            }
        }
        $this->templateObj->layout_currentDirFoot();
    }
    
    public function getSpaceAvailibilityStat($CurrDirSize=0) {   	
    	$AllocatedSpace = $this->chkAllocationSpace();
    	
    	if($AllocatedSpace!=null && $AllocatedSpace!='unlimited' && $AllocatedSpace!='restricted') {
	    	$AvailableFreeSpace = $this->allocatedSpace - $this->baseDirSize;
	    	
	    	if($AvailableFreeSpace < 104857600)
	    		$FreeSpaceAlert = 2;
	    	if($AvailableFreeSpace < 629145600)
	    		$FreeSpaceAlert = 1;
	    	else 
	    		$FreeSpaceAlert = 0;
	    		
    		$AvailableFreeSpace = $this->bytesize($AvailableFreeSpace);
    		
    	} else {
    		$AvailableFreeSpace = ucfirst($AllocatedSpace);
    		$FreeSpaceAlert = 0;
    	}
    	
    	$TotalUsedSpace = $this->bytesize($this->baseDirSize);
    	$CurrDirSpace = $this->bytesize($CurrDirSize);
    	
    	$this->templateObj->layout_spaceStats($AllocatedSpace,$TotalUsedSpace,$AvailableFreeSpace,$CurrDirSpace,$FreeSpaceAlert);
    }
    
    public function chkAllocationSpace() {
    	if($this->allocatedSpace == 0){
    		return 'unlimited';
    	}
    	if($this->allocatedSpace == null) {
    		return 'restricted';
    	}
    	if(is_numeric($this->allocatedSpace)) {
    		return $this->bytesize($this->allocatedSpace);
    	}
    	return null;
    }
}