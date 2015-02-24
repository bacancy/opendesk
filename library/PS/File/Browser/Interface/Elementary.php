<?php
class PS_File_Browser_Interface_Elementary { 
	protected $interface;
	protected $layout;
	
	protected $requestUri;
  	protected $requestParams;
  	protected $requestBaseUrl;
  	
  	protected $moduleName;
  	protected $controllerName;
  	protected $actionName;
  	
  	protected $viewObj;
  	
  	protected $regexFileTypes;
  	
  	public $separator;

	public function __construct($requestUri,$requestParams,$requestBaseUrl,$moduleName,$controllerName,$actionName,$ViewObj,$interfaces=null,$layouts='') {
		$this->requestUri = $requestUri;
    	$this->requestParams = $requestParams;
    	$this->requestBaseUrl = $requestBaseUrl;
    	
    	$this->moduleName = $moduleName;
    	$this->controllerName = $controllerName;
    	$this->actionName = $actionName;
    	
    	$this->viewObj = $ViewObj;
    	
    	$this->interface = $interfaces;
		$this->layout = $layouts;
		
		$this->regexFileTypes=null;
		
		if (strtoupper(substr(PHP_OS, 0, 3) == 'WIN')) {
            $this->separator = "\\";
        } else { 
            $this->separator = "/";
        }
	}
	
	public function __get($property)
    {
        if(in_array($property, array('interface','layout', 'requestUri', 'requestParams', 'requestBaseUrl', 'viewObj','moduleName','controllerName','actionName','separator','regexFileTypes'))){
            return $this->$property;
        }else{
            $this->layout .= 'property '.$property.' does not exist in '.__CLASS__.' class';
        }
    }
    
    public function __set($property, $value)
    {
        if(in_array($property, array('interface','layout', 'requestUri', 'requestParams', 'requestBaseUrl', 'viewObj','moduleName','controllerName','actionName','separator','regexFileTypes'))){
            return $this->$property = $value;
            echo $this->$property;
        }else{
            $this->layout .= 'property '.$property.' does not exist in '.__CLASS__.' class';
        }
    }

    function layout_javascript() {
    	$return = '';

    	$return .= '<script type="text/javascript">';
    	
    	$return .= '    var filtered = "";';
    	
    	if($this->regexFileTypes!==null)
	    	$return .= '    var validFileTypes='.$this->regexFileTypes.';';
	    else 
	    	$return .= '    var validFileTypes="";';
    	
		$return .= '    function filter (begriff) {';
		$return .= '        var suche = trim(begriff.value.toLowerCase());';
		$return .= '        var table = document.getElementById("filetable");';
		$return .= '        if(filtered=="") {';
		$return .= '			filtered = document.getElementById("filtered").innerHTML;';
		$return .= '        }';
		$return .= '        var ele;';
		$return .= '        for(var r = 1; r < table.rows.length; r++) {';
		$return .= '            ele = table.rows[r].cells[2].innerHTML.replace(/<[^>]+>/g,"");';
		$return .= '            if(ele.toLowerCase().indexOf(suche)>=0 )';
		$return .= '                table.rows[r].style.display = "";';
		$return .= '            else table.rows[r].style.display = "none";';
		$return .= '        }';
		$return .= '        if(filtered=="None. Showing all files.") {';
		$return .= '        	document.getElementById("filtered").innerHTML = "Title matching \'" + suche + "\'";';
		$return .= '        } else {';
		$return .= '        	document.getElementById("filtered").innerHTML = filtered + " and Title matching \'" + suche + "\'";';
		$return .= '        }';
		$return .= '        if(trim(suche)=="") {';
		$return .= '        	document.getElementById("filtered").innerHTML = filtered;';
		$return .= '        }';
		$return .= '    }';
		
		$return .= '    function selectAll(obj) {';
		$return .= '        var oFileList = obj.elements["chkfiles[]"];';
		$return .= '        for(i=0; i < oFileList.length; ++i) {';
		$return .= '            if(obj.selall.checked == true)';
		$return .= '                oFileList[i].checked = true;';
		$return .= '            else';
		$return .= '                oFileList[i].checked = false;';
		$return .= '        }';
		$return .= '    }';

		$return .= '    function dis() {';
		$return .= '    }';
		
		$return .= '    function nameValidation(frmObj,type) {';
		$return .= '    	var FldValue = trim(frmObj.txtDFName.value);';
		$return .= '    	frmObj.txtDFName.value = FldValue;';
		$return .= '    	if(FldValue=="Directory/File Name") {';
		$return .= '    		alert("Please enter " + type + " name to create.");';
		$return .= '    		frmObj.txtDFName.focus();';
		$return .= '    		return false;';
		$return .= '    	}';
		$return .= '        var validFlg=false;';
		$return .= '		if (type=="file" && /^[^\\\/\:\*\?\"\<\>\|\.]+(\.[^\\\/\:\*\?\"\<\>\|\.]+)+$/.test(FldValue)) {';
		$return .= '			validFlg=true;';
		$return .= '		}';
		$return .= '		if (type=="directory" && /^[^\\\/\:\*\?\"\<\>\|\.]+(\.[^\\\/\:\*\?\"\<\>\|\.]+)*$/.test(FldValue)) {';
		$return .= '			validFlg=true;';
		$return .= '		}';
		$return .= '        if(validFlg==false) {';
		$return .= '        	alert(type + " name \"" + FldValue + "\" is not valid.\n A " + type + " name can not contain any of following character:\n \\\ / : * ? \" < > |");';
		$return .= '    		frmObj.txtDFName.focus();';
		$return .= '    		return false;';
		$return .= '    	}';
		$return .= '		if ((FldValue.length < 1) || (FldValue.length > 255)) {';
		$return .= '        	alert(type + " name should contain characters between 1 and 255.");';
		$return .= '    		frmObj.txtDFName.focus();';
		$return .= '    		return false;';
		$return .= '		}';
		$return .= ' 		if(type=="file" && validFileTypes!="" && !validFileTypes.test(FldValue)) {
								alert("File Type not Supported.");
								frmObj.txtDFName.focus();
								return false;
							}';
		$return .= '        var suche = FldValue;';
		$return .= '        var table = document.getElementById("filetable");';
		$return .= '        var ele;';
		$return .= '        var existFlg=false;';
		$return .= '        var eleType;';
		$return .= '        for(var r = 1; r < table.rows.length; r++) {';
		$return .= '        	eleType="";';
		$return .= '            eleType = table.rows[r].cells[1].innerHTML.replace(/<[^>]+>/g,"");';
		$return .= '            if(type=="directory" && eleType=="Folder") {';
		$return .= '            	ele = table.rows[r].cells[2].innerHTML.replace(/<[^>]+>/g,"");';
		$return .= '            	if(ele==suche) {';
		$return .= '    				existFlg=true;';
		$return .= '    				break;';
		$return .= '            	}';
		$return .= '            }';
		$return .= '            if(type=="file" && eleType!="Folder") {';
		$return .= '            	ele = table.rows[r].cells[2].innerHTML.replace(/<[^>]+>/g,"");';
		$return .= '            	if(ele==suche) {';
		$return .= '    				existFlg=true;';
		$return .= '    				break;';
		$return .= '            	}';
		$return .= '            }';
		$return .= '        }';
		$return .= '        if(existFlg==true) {';
		$return .= '        	alert(type + " name \"" + FldValue + "\" already exists.");';
		$return .= '    		frmObj.txtDFName.focus();';
		$return .= '    		return false;';
		$return .= '    	}';
		$return .= '    	return true;';
		$return .= '    }';
		
		$return .= '	function chkSelectionFile(frmObj) {';
		$return .= '		if(trim(frmObj.uploadFile.value)=="") {';
		$return .= '			alert("Please Select file to upload to current Directory.");';
		$return .= '    		frmObj.uploadFile.focus();';
		$return .= '    		return false;';
		$return .= '    	}';
		$return .= '    	var FldPath = trim(frmObj.uploadFile.value);';
		$return .= '		var FldValue="";';
		$return .= ' 		if (FldPath) {
						        var startIndex = (FldPath.indexOf("\\\") >= 0 ? FldPath.lastIndexOf("\\\") : FldPath.lastIndexOf("/"));
						        var filename = FldPath.substring(startIndex);
						        if (filename.indexOf("\\\") === 0 || filename.indexOf("/") === 0) {
				                	filename = filename.substring(1);
						        }
						        FldValue = filename;
							}';
		$return .= '       	var validFlg=false;';
		$return .= '		if (/^[^\\\/\:\*\?\"\<\>\|\.]+(\.[^\\\/\:\*\?\"\<\>\|\.]+)+$/.test(FldValue)) {';
		$return .= '			validFlg=true;';
		$return .= '		}';
		$return .= '        if(validFlg==false) {';
		$return .= '        	alert("File name \"" + FldValue + "\" is not valid.\n A file name can not contain any of following character:\n \\\ / : * ? \" < > |");';
		$return .= '    		frmObj.uploadFile.focus();';
		$return .= '    		return false;';
		$return .= '    	}';
		$return .= '		if ((FldValue.length < 1) || (FldValue.length > 255)) {';
		$return .= '        	alert("File name should contain characters between 1 and 255.");';
		$return .= '    		frmObj.uploadFile.focus();';
		$return .= '    		return false;';
		$return .= '		}';
		$return .= ' 		if(validFileTypes!="" && !validFileTypes.test(FldValue)) {
								alert("File Type not Supported.");
								frmObj.uploadFile.focus();
								return false;
							}';
		$return .= '        var suche = FldValue;';
		$return .= '        var table = document.getElementById("filetable");';
		$return .= '        var ele;';
		$return .= '        var existFlg=false;';
		$return .= '        var eleType;';
		$return .= '        for(var r = 1; r < table.rows.length; r++) {';
		$return .= '        	eleType="";';
		$return .= '            eleType = table.rows[r].cells[1].innerHTML.replace(/<[^>]+>/g,"");';
		$return .= '            if(eleType!="Folder") {';
		$return .= '            	ele = table.rows[r].cells[2].innerHTML.replace(/<[^>]+>/g,"");';
		$return .= '            	if(ele==suche) {';
		$return .= '    				existFlg=true;';
		$return .= '    				break;';
		$return .= '            	}';
		$return .= '            }';
		$return .= '        }';
		$return .= '        if(existFlg==true) {';
		$return .= '        	alert("File name \"" + FldValue + "\" already exists.");';
		$return .= '    		frmObj.uploadFile.focus();';
		$return .= '    		return false;';
		$return .= '    	}';
		$return .= '		return true;';
		$return .= '	}';

		$return .= '	function trim(str) {';
		$return .= '		var	str = str.replace(/^\s\s*/, "");';
		$return .= '		var ws = /\s/;';
		$return .= '		var i = str.length;';
		$return .= '		while (ws.test(str.charAt(--i)));';
		$return .= '		return str.slice(0, i + 1);';
		$return .= '	}';
				
		$return .= '    function chkSelection(frmObj) {';
		$return .= '        var oFileList = frmObj.elements["chkfiles[]"];';
		$return .= '        var chkFlg = false;';
		$return .= '        for(i=0; i < oFileList.length; ++i) {';
		$return .= '            if(oFileList[i].checked == true)';
		$return .= '                 chkFlg = true;';
		$return .= '        }';
		$return .= '        if(chkFlg == false)';
		$return .= '        	alert("Please Select Files to perform Action.");';
		$return .= '        return chkFlg;';
		$return .= '    }';
		
		$return .= '    function performPreDelete(frmObj,alertStr) {';
		$return .= '		var chkFlg = false;';
		$return .= '		chkFlg = chkSelection(frmObj);';
		$return .= '        if(chkFlg == true)';
		$return .= '			chkFlg = confirmDelete(alertStr);';
		$return .= '        return chkFlg;';
		$return .= '    }';
		
		$return .= '    function confirmDelete(fileName) {';
		$return .= '    	if(confirm("Are you sure you want to Delete " + fileName + "?"))';
		$return .= '    		return true;';
		$return .= '    	';
		$return .= '    	return false;';
		$return .= '    }';
		
		$return .= '</script>';
		
		$this->layout .= $return;
    }
    
	function layout_header() {
		$return = '';
		$return .= '<center>';
		$return .= '<font face="Tahoma" size="2" style="font-size: 10pt">';
		$return .= '<table align="center" bgcolor="#FFFFFF" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">';
		$return .= '	<tr>';
		$return .= '		<td valign="top"><br/>';
		
		$this->layout .= $return;
	}
	
	function layout_footer($directory,$select,$version,$BaseDir) {
		$CurDirArr = explode($this->separator,$directory);
		$return = '';
		
		$return .= '			</table>';
		$return .= '			<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%"><tr>';
		$return .= '			<td align="left" bgcolor="#2E2E2E" nowrap="nowrap"><font face="Tahoma" size=1 style="font-size: 8pt; text-decoration: none;" color="#808080"><b></b></font> <input type="text" name="txtDFName" maxlength="255" title="Input Directory/File name to create" onKeypress="event.cancelBubble=true;" onfocus="javascript: if(this.value==\'Directory/File Name\')this.value=\'\';" onblur="javascript: if(this.value==\'\') this.value=\'Directory/File Name\';" value="Directory/File Name" style="font-size: 8pt; color:#808080; background-color:#F3F0ED;"><input title="Create Directory" type="Submit" name="createDirectory" value="Create Directory" onclick="return nameValidation(this.form,\'directory\');" style="font-size: 8pt; color:#808080; background-color:#2E2E2E;  border:none; cursor:pointer;"><input title="Create File" type="Submit" name="createFile" value="Create File" onclick="return nameValidation(this.form,\'file\');" style="font-size: 8pt; color:#808080; background-color:#2E2E2E;  border:none; cursor:pointer;"></td>';
		$return .= '			<td align="right" bgcolor="#2E2E2E" nowrap="nowrap"><font face="Tahoma" size=1 style="font-size: 8pt; text-decoration: none;" color="#808080"><b>With Selected:</b></font> <input title="Download selected files and directories as one zip file"  id="but_Zip" type="Submit" name="massDownload" value="Download" onclick="return chkSelection(this.form);" style="font-size: 8pt; color:#808080; background-color:#2E2E2E;  border:none; cursor:pointer;"><input title="Delete selected files and directories."  type="Submit" onclick="return performPreDelete(this.form,\'selected files\');" name="massDelete" value="Delete" style="font-size: 8pt; color:#808080; background-color:#2E2E2E; border:none; cursor:pointer;"></td>';
		$return .= '			</tr></table>';
		$return .= '			</form>';
		$return .= '			<table align="center" bgcolor="#2E2E2E" border=0 cellpadding=0 cellspacing=0 height=57 width=100%>';
		$return .= '				<tr>';
		$return .= '					<td align=left nowrap="nowrap" style="padding-left: 10px"><font face="Tahoma" size=1 style="font-size: 8pt" color="#808080"><b>Current Directory:</b><br>'.$CurDirArr[count($CurDirArr)-1].'</td>';
		if (!empty($select))
			$return .= '					<td align=left nowrap="nowrap" style="padding-left: 50px; padding-right: 50px"><font face="Tahoma" size=1 style="font-size: 8pt" color="#808080"><b>Filter:</b><br><span id="filtered">Showing Title that start with \''.ucfirst($select).'\'</span></td>';
		else
		$return .= '					<td align=left nowrap="nowrap" style="padding-left: 50px; padding-right: 50px"><font face="Tahoma" size=1 style="font-size: 8pt" color="#808080"><b>Filter:</b><br><span id="filtered">None. Showing all files.</span></td>';
		//$return .= '					<td align=right nowrap="nowrap" style="padding-right: 10px" width="100%"><font face="Tahoma" size=1 style="font-size: 8pt" color="#808080"><b>Developed by</b><br>Salman <br></td>';
		$return .= '					<td align=right nowrap="nowrap" style="padding-right: 10px" width="100%"><br></td>';
		$return .= '				</tr>';
		$return .= '			</table>';
		$return .= '			<br/>';
		$return .= '		</td>';
		$return .= '	</tr>';
		$return .= '</table>';
		$return .= '</font>';
		$return .= '</center>';
		
		$this->layout .= $return;
	}
	
	function layout_archiveFooter($ZipContent) {
		$return = '';
        $return .= '<tr><td align=center bgcolor="#2E2E2E" colspan="8" nowrap="nowrap" style="padding-left: 10px"><font face="Tahoma" size=1 style="font-size: 8pt" color="#808080"><b>'.$ZipContent['totalSize'].' in '.$ZipContent['totalFiles'].' files in '.$ZipContent['name'].'. Compression ratio: '.$ZipContent['ratio'].'</b></font></td></tr>';
        $return .= '</table>';
        
        $this->layout .= $return;
	}
	
	function layout_nofiles($filter) {
		$return = '';
		$return .= '	<tr>';
		$return .= '		<td align="center" bgcolor="#F3F0ED" width="10"  height="21" nowrap="nowrap" style="">&nbsp;</td>';
		$return .= '		<td align="center" bgcolor="#F3F0ED" colspan="5" nowrap="nowrap" style="padding-left: 05px; padding-right: 05px"><font face="Tahoma" size="2" style="font-size: 8pt" color="#343434"><b>No files'; 
		if ($filter) 
			$return .= ' match the filter you have selected.'; 
		else 
			$return .= '.'; 
		$return .= '</b></font></td>';
		$return .= '		<td align="center" bgcolor="#F3F0ED" width="10"  height="21" nowrap="nowrap" style="">&nbsp;</td>';
		$return .= '	</tr>';
		
		$this->layout .= $return;
	}
	
	function layout_headings($directory) {
		$return = '';
		
		$return .= '			<form action="'.$this->viewObj->url(array('controller'=>$this->controllerName,'action'=>$this->actionName,'module'=>$this->moduleName), null, true).'" method="Post" name="filelist" class="border" enctype="multipart/form-data"><input type="hidden" name="dir" value="'.base64_encode($directory).'" />';
		$return .= '			<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td align="left" bgcolor="#2E2E2E" nowrap="nowrap"><font face="Tahoma" size=1 style="font-size: 8pt; text-decoration: none;" color="#808080;"><b></b></font><input name="uploadFile" title="Select File to Upload" onKeypress="event.cancelBubble=true;" type="file" style="font-size: 8pt; color:#808080; background-color:#F3F0ED;"><input title="Upload selected file to the current working directory" type="Submit" name="fileUpload" value="Upload" onclick="return chkSelectionFile(this.form);" style="font-size: 8pt; color:#808080; background-color:#2E2E2E;  border:none; cursor:pointer;"/></td><td align="right" bgcolor="#2E2E2E" nowrap="nowrap"><font face="Tahoma" size=1 style="font-size: 8pt; text-decoration: none;" color="#808080;"><b></b></font><input name="filt" title="Input text to filter by Title" onKeypress="event.cancelBubble=true;" onkeyup="filter(this)" onfocus="javascript: if(this.value==\'Filter by Title\')this.value=\'\';" onblur="javascript: if(this.value==\'\') this.value=\'Filter by Title\';" type="text" value="Filter by Title" style="font-size: 8pt; color:#808080; background-color:#F3F0ED;"></td></tr></table>';
		$return .= '			<table id="filetable" class="filelisting" align="center" border="0" cellpadding="0" cellspacing="0" width="100%">';
		$return .= '				<tr>';
		$return .= '					<td align="center" bgcolor="#2E2E2E" width="10"  height="21" nowrap="nowrap" style=""><input type="checkbox" id="selall" name="selall" title="Select/Deselect All" onClick="selectAll(this.form)"></td>';
		$return .= '					<td align="right" bgcolor="#2E2E2E"  height="21" nowrap="nowrap" style="padding-left: 05px; padding-right: 10px"><font face="Tahoma" size=1 style="font-size: 8pt; text-decoration: none;" color="#808080"><b>Type</b></font></td>';
		$return .= '					<td align="left" bgcolor="#2E2E2E"   width="100%" height="21" nowrap="nowrap" style="padding-left: 05px; padding-right: 10px"><font face="Tahoma" size=1 style="font-size: 8pt; text-decoration: none;" color="#808080"><b>Title</b></font></td>';
		$return .= '					<td align="right" bgcolor="#2E2E2E"  height="21" nowrap="nowrap" style="padding-left: 05px; padding-right: 10px"><font face="Tahoma" size=1 style="font-size: 8pt; text-decoration: none;" color="#808080"><b>Size</b></font></td>';
		$return .= '					<td align="right" bgcolor="#2E2E2E"  height="21" nowrap="nowrap" style="padding-left: 05px; padding-right: 05px"><font face="Tahoma" size=1 style="font-size: 8pt; text-decoration: none;" color="#808080"><b>Date Modified</b></font></td>';
		$return .= '					<td align="right" bgcolor="#2E2E2E"  height="21" nowrap="nowrap" style="padding-left: 05px; padding-right: 05px"><font face="Tahoma" size=1 style="font-size: 8pt; text-decoration: none;" color="#808080"><b>Action</b></font></td>';
		$return .= '					<td align="center" bgcolor="#2E2E2E" width="10"  height="21" nowrap="nowrap" style="">&nbsp;</td>';
		$return .= '				</tr>';
		
		$this->layout .= $return;
	}
	
	function layout_archiveHeading() {
		$return = '';
		
		$return .= '			<table id="filetable" class="filelisting" align="center" border="0" cellpadding="0" cellspacing="0" width="100%">';
		$return .= '				<tr>';
		$return .= '					<td align="center" bgcolor="#2E2E2E" width="10"  height="21" nowrap="nowrap" style="">&nbsp;</td>';
		$return .= '					<td align="right" bgcolor="#2E2E2E"  height="21" nowrap="nowrap" style="padding-left: 05px; padding-right: 10px"><font face="Tahoma" size=1 style="font-size: 8pt; text-decoration: none;" color="#808080"><b>Compr. ratio</b></font></td>';
		$return .= '					<td align="left" bgcolor="#2E2E2E"   width="100%" height="21" nowrap="nowrap" style="padding-left: 05px; padding-right: 10px"><font face="Tahoma" size=1 style="font-size: 8pt; text-decoration: none;" color="#808080"><b>Name</b></font></td>';
		$return .= '					<td align="right" bgcolor="#2E2E2E"  height="21" nowrap="nowrap" style="padding-left: 05px; padding-right: 10px"><font face="Tahoma" size=1 style="font-size: 8pt; text-decoration: none;" color="#808080"><b>Uncompressed size</b></font></td>';
		$return .= '					<td align="right" bgcolor="#2E2E2E"  height="21" nowrap="nowrap" style="padding-left: 05px; padding-right: 05px"><font face="Tahoma" size=1 style="font-size: 8pt; text-decoration: none;" color="#808080"><b>Compressed size</b></font></td>';
		$return .= '					<td align="right" bgcolor="#2E2E2E"  height="21" nowrap="nowrap" style="padding-left: 05px; padding-right: 05px"><font face="Tahoma" size=1 style="font-size: 8pt; text-decoration: none;" color="#808080"><b>Date</b></font></td>';
		$return .= '					<td align="center" bgcolor="#2E2E2E" width="10"  height="21" nowrap="nowrap" style="">&nbsp;</td>';
		$return .= '				</tr>';
		
		$this->layout .= $return;
	}
	
	function layout_letters($chrooted,$directory,$select) {
		$return = '';
		$defaultParam = array('controller'=>$this->controllerName,'action'=>$this->actionName,'module'=>$this->moduleName);
		$UrlParamArr = $defaultParam;
		
		foreach(array_map('chr',range(ord('a'),ord('z'))) as $letter) 
			$letters[]=$letter;
		$return .= '			<table align="center" border=0 cellspacing=0 cellpadding=0 height=21 width="100%">';
		$return .= '				<tr>';
		$return .= '					<td align=center bgcolor="#'; 
		if ($select=="numbers") 
			$return .= "BF000D"; 
		else 
			$return .= "2E2E2E"; 
			
		$return .= '" onmouseover="this.style.cursor = \'hand\'" title="Filter file title starting with numbers" onmouseup="location.href=\''; 
		if ($chrooted!=$directory) 
			$UrlParamArr = array_merge($UrlParamArr,array('dir'=>base64_encode(realpath($directory))));
		$UrlParamArr = array_merge($UrlParamArr,array('select'=>'numbers'));
		
		$return .= $this->viewObj->url($UrlParamArr, null, true).'\'" width=22><a href="'; 
		
		$UrlParamArr = $defaultParam;
		
		if ($chrooted!=$directory) 
			$UrlParamArr = array_merge($UrlParamArr,array('dir'=>base64_encode(realpath($directory))));
		$UrlParamArr = array_merge($UrlParamArr,array('select'=>'numbers'));
		
		$return .= $this->viewObj->url($UrlParamArr, null, true).'" title="Filter file title starting with numbers" style="text-decoration: none"><font face="Tahoma" style="font-size: 8pt" size=1 color=white><b>#</b></a></td><td align="center" bgcolor="#FFFFFF" width="2"></td>';
		
		$UrlParamArr = $defaultParam;
		
		for ($X=0; $X<count($letters); $X++) { 
			$return .= '					<td align="center" bgcolor="#'; 
			if ($select==$letters[$X]) 
				$return .= 'BF000D'; 
			else 
				$return .= '2E2E2E'; 
				
			$return .= '" onmouseover="this.style.cursor = \'hand\'" title="Filter file title starting with \''.$letters[$X].'\'" onmouseup="location.href=\''; 
			if ($chrooted!=$directory) 
				$UrlParamArr = array_merge($UrlParamArr,array('dir'=>base64_encode(realpath($directory))));
			$UrlParamArr = array_merge($UrlParamArr,array('select'=>$letters[$X]));
			
			$return .= $this->viewObj->url($UrlParamArr, null, true).'\'" width="22"><a href="'; 
			
			$UrlParamArr = $defaultParam;
			
			if ($chrooted!=$directory) 
				$UrlParamArr = array_merge($UrlParamArr,array('dir'=>base64_encode(realpath($directory))));
			$UrlParamArr = array_merge($UrlParamArr,array('select'=>$letters[$X]));
			 
			$return .= $this->viewObj->url($UrlParamArr, null, true).'" title="Filter file title starting with \''.$letters[$X].'\'" style="text-decoration: none"><font face="Tahoma" style="font-size: 8pt" size=1 color=white><b>'.ucfirst($letters[$X]).'</b></a></td><td align="center" bgcolor="#FFFFFF" width="2"></td>'; 
			
			$UrlParamArr = $defaultParam;
		}
		$return .= '					<td align=center bgcolor="#'; 
		if ($select=="all" OR $select=="") 
			$return .= 'BF000D'; 
		else 
			$return .= '2E2E2E'; 
			
		$return .= '" onmouseover="this.style.cursor = \'hand\'" title="Clear Filter" onmouseup="location.href=\''; 
		if ($chrooted!=$directory) 
			$UrlParamArr = array_merge($UrlParamArr,array('dir'=>base64_encode(realpath($directory))));
		$return .= $this->viewObj->url($UrlParamArr, null, true).'" width="132"><a href="'; 
		
		$UrlParamArr = $defaultParam;
		
		if ($chrooted!=$directory) 
			$UrlParamArr = array_merge($UrlParamArr,array('dir'=>base64_encode(realpath($directory))));
			
		$return .= $this->viewObj->url($UrlParamArr, null, true).'" title="Clear Filter" style="text-decoration: none"><font face="Tahoma" style="font-size: 8pt" size=1 color=white><b>All</b></a></td>';
		
		$UrlParamArr = $defaultParam;
		
		$return .= '				</tr>';
		$return .= '			</table>';
		
		$this->layout .= $return;
	}
	
	function layout_row($filename) {
		$return = '';
		$return .= '	<tr>';
		if ($filename['title']!='[.]' && $filename['title']!='[..]') {
		$return .= '		<td align="center" bgcolor="#F3F0ED" width="10"  height="21" nowrap="nowrap" style=""><input type="checkbox" id="chkfiles[]" name="chkfiles[]" title="Select/Deselect '.$filename['title'].'" value="'.base64_encode($filename['physicalLink']).'"/></td>';
		} else {
		$return .= '		<td align="center" bgcolor="#F3F0ED" width="10"  height="21" nowrap="nowrap" style="">&nbsp;</td>';
		}
		$return .= '		<td align="right" bgcolor="#F3F0ED"  height="21" nowrap="nowrap" style="padding-left: 05px; padding-right: 10px"><font face="Tahoma" size="2" style="font-size: 8pt" color="#AEAAA6"><b>'.$filename['type'].'</b></font></td>';
		$return .= '		<td align="left" bgcolor="#F3F0ED"   height="21" nowrap="nowrap" style="padding-left: 05px; padding-right: 05px"><a href="'.$filename['link'].'" title="Open '.$filename['title'].'" style="text-decoration: none"><font face="Tahoma" size="2" style="font-size: 8pt" color="#343434"><b>'.$filename['title'].'</b></font></a></td>';
		$return .= '		<td align="right" bgcolor="#F3F0ED"  height="21" nowrap="nowrap" style="padding-left: 05px; padding-right: 10px"><font face="Tahoma" size="2" style="font-size: 8pt" color="#AEAAA6"><b>'.$filename['size'].'</b></font></td>';
		$return .= '		<td align="right" bgcolor="#F3F0ED"  height="21" nowrap="nowrap" style="padding-left: 05px; padding-right: 05px"><font face="Tahoma" size="1" style="font-size: 8pt" color="#AEAAA6"><b>'.$filename['update'].'</b></font></td>';
		$return .= '		<td align="right" bgcolor="#F3F0ED"  height="21" nowrap="nowrap" style="padding-left: 05px; padding-right: 05px"><font face="Tahoma" size="1" style="font-size: 8pt" color="#AEAAA6"><b>' . $this->showAction($filename) . '</b></font></td>';
		$return .= '		<td align="center" bgcolor="#F3F0ED" width="10"  height="21" nowrap="nowrap" style="">&nbsp;</td>';
		$return .= '	</tr>';
		
		$this->layout .= $return;
	}
	
	function layout_archiveRow($ZipContent) {
		$return = '';		
		$return .= '	<tr>';
		$return .= '		<td align="center" bgcolor="#F3F0ED" width="10"  height="21" nowrap="nowrap" style="">&nbsp;</td>';
		$return .= '		<td align="right" bgcolor="#F3F0ED"  height="21" nowrap="nowrap" style="padding-left: 05px; padding-right: 10px"><font face="Tahoma" size="2" style="font-size: 8pt" color="#AEAAA6"><b>'.$ZipContent['iPercent'].'</b></font></td>';
		$return .= '		<td align="left" bgcolor="#F3F0ED"   height="21" nowrap="nowrap" style="padding-left: 05px; padding-right: 05px"><font face="Tahoma" size="2" style="font-size: 8pt" color="#343434"><b>'.$ZipContent['name'].'</b></font></td>';
		$return .= '		<td align="right" bgcolor="#F3F0ED"  height="21" nowrap="nowrap" style="padding-left: 05px; padding-right: 10px"><font face="Tahoma" size="2" style="font-size: 8pt" color="#AEAAA6"><b>'.$ZipContent['iUncompressedSize'].'</b></font></td>';
		$return .= '		<td align="right" bgcolor="#F3F0ED"  height="21" nowrap="nowrap" style="padding-left: 05px; padding-right: 05px"><font face="Tahoma" size="1" style="font-size: 8pt" color="#AEAAA6"><b>'.$ZipContent['iCompressedSize'].'</b></font></td>';
		$return .= '		<td align="right" bgcolor="#F3F0ED"  height="21" nowrap="nowrap" style="padding-left: 05px; padding-right: 05px"><font face="Tahoma" size="1" style="font-size: 8pt" color="#AEAAA6"><b>' . $ZipContent['date'] . '</b></font></td>';
		$return .= '		<td align="center" bgcolor="#F3F0ED" width="10"  height="21" nowrap="nowrap" style="">&nbsp;</td>';
		$return .= '	</tr>';
		
		$this->layout .= $return;
	}
	
	function layout_currentDirHead() {
		$return = '';
		$return .= '		<table align="center" border=0 cellspacing=0 cellpadding=0 height=21 width="100%"><tr><td align="left" bgcolor="#2E2E2E" colspan="8" height="21" nowrap="nowrap" style="padding-left: 05px; padding-right: 05px">';
		$this->layout .= $return;
	}
	
	function layout_currentDir($linkUrl, $aCurrentPath, $separator) {
		$return = '';
		$return .= '		<a href="'.$linkUrl.'" style="text-decoration: none"><font face="Tahoma" size="2" style="font-size: 8pt" color="#FFFFFF"><b>'.$aCurrentPath.'</b></font></a><font face="Tahoma" size="2" style="font-size: 8pt" color="#FFFFFF"><b>'.$separator.'</b></fonr>';
		$this->layout .= $return;
	}
	
	function layout_currentDirFoot() {
		$return = '';
		$return .= '		</td></tr></table>';
		$this->layout .= $return;
	}
	
	function layout_spaceStats($AllocatedSpace,$TotalUsedSpace,$AvailableFreeSpace,$CurrDirSpace,$FreeSpaceAlert) {
		if($FreeSpaceAlert==0) {
			$AlertBgColor = '#008000';
		} else if($FreeSpaceAlert==1) {
			$AlertBgColor = '#FFA500';
		} else if($FreeSpaceAlert==2) {
			$AlertBgColor = '#BF000D';
		}
		
		$return = '';
		$return .= '		<table align="center" border=0 cellspacing=0 cellpadding=0 height=21 width="100%"><tr>';
		
		$return .= '					<td align="center" bgcolor="#2E2E2E" colspan="2" height="21" nowrap="nowrap" style="padding-left: 05px; padding-right: 05px"><font face="Tahoma" size="2" style="font-size: 8pt" color="#FFFFFF"><b>Space Allocated: '.$AllocatedSpace.'</b></fonr></td>';
		$return .= '					<td align="center" bgcolor="#2E2E2E" colspan="2" height="21" nowrap="nowrap" style="padding-left: 05px; padding-right: 05px"><font face="Tahoma" size="2" style="font-size: 8pt" color="#FFFFFF"><b>Space Utilized: '.$TotalUsedSpace.'</b></fonr></td>';
		$return .= '					<td align="center" bgcolor="#2E2E2E" colspan="2" height="21" nowrap="nowrap" style="padding-left: 05px; padding-right: 05px"><font face="Tahoma" size="2" style="font-size: 8pt" color="#FFFFFF"><b>Current Directory Space: '.$CurrDirSpace.'</b></fonr></td>';
		$return .= '					<td align="center" bgcolor="'.$AlertBgColor.'" colspan="2" height="21" nowrap="nowrap" style="padding-left: 05px; padding-right: 05px"><font face="Tahoma" size="2" style="font-size: 8pt" color="#FFFFFF"><b>Space Available: '.$AvailableFreeSpace.'</b></fonr></td>';
		
		$return .= '		</tr></table>';
		$this->layout .= $return;
	}
	
	function showAction($file, $dir = ""){
		$return = '';
        if ($file['physicalType']=='filename' && filetype($dir.$file['physicalLink']) != "dir") {
        	$DownloadUrl = $this->viewObj->url(array('controller'=>$this->controllerName,'action'=>$this->actionName,'module'=>$this->moduleName,'dir'=>base64_encode($file['path']),'download'=>base64_encode($dir.$file['physicalLink'])), null, true);
            $ViewUrl = $this->viewObj->url(array('controller'=>$this->controllerName,'action'=>$this->actionName,'module'=>$this->moduleName,'dir'=>base64_encode($file['path']),'view'=>base64_encode($dir.$file['physicalLink'])), null, true);
            $return .= "<a onmousedown=\"dis()\" href=\"$DownloadUrl\" title=\"Download ".$file['title']."\">Download</a> | <a onmousedown=\"dis()\" href=\"$ViewUrl\" title=\"View ".$file['title']."\" target=\"_blank\">View</a> | ";
        }
        
        if ($file['title']!='[.]' && $file['title']!='[..]') {
	        $DeleteUrl = $this->viewObj->url(array('controller'=>$this->controllerName,'action'=>$this->actionName,'module'=>$this->moduleName,'dir'=>base64_encode($file['path']),'delete'=>base64_encode($dir.$file['physicalLink'])), null, true);
    	    $return .= "<a onmousedown=\"dis()\" href=\"$DeleteUrl\" title=\"Delete ".$file['title']."\" onclick=\"return confirmDelete('".$file['title']."');\">Delete</a>";
        }
        
        return $return;
    }
	
	function getLayout() {
		return $this->layout;
	}
}