<?php
class PS_File_Transfer_Adapter_Http extends Zend_File_Transfer_Adapter_Http
{

    /**
     * Send the file to the client (Download)
     *
     * @param  string|array $options Options for the file(s) to send
     * @return void
     * @throws Zend_File_Transfer_Exception Not implemented
     */
    public function send($options = null)
    {
        require_once 'Zend/File/Transfer/Exception.php';
        throw new Zend_File_Transfer_Exception('Method not implemented');
    }

    /**
     * Receive the file from the client (Upload)
     *
     * @param  string|array $files (Optional) Files to receive
     * @return bool
     */
    public function receive($files = null)
    {
        return parent::receive($files);
    }

    /**
     * Checks if the file was already sent
     *
     * @param  string|array $file Files to check
     * @return bool
     * @throws Zend_File_Transfer_Exception Not implemented
     */
    public function isSent($file = null)
    {
        require_once 'Zend/File/Transfer/Exception.php';
        throw new Zend_File_Transfer_Exception('Method not implemented');
    }


   
   
    /**
     * Change filename of transferred file
     *
     * @param  string $file
     * @param  string $newFileName New file name that should be changed with uploaded file name
     * @return bool
     */
    public function setFileName($file, $newFileName)
    {
        $file = (string) $file;
        if (!array_key_exists($file, $this->_files)) {
             return false;
        }

        $this->_files[$file]['name'] = $newFileName;
        return true;
    }

    /**
     * Change filename of transferred file
     *
     * @param  string $file Get extension of uploaded file
     * @return string|null
     */
    public function getFileExtension($file)
    {
        $file = (string) $file;
        if (!array_key_exists($file, $this->_files)) {
             return null;
        }

        $fileNameSlices = pathinfo($this->getFileName($file));
        if ($fileNameSlices['extension'] != '') {
        	return strtolower($fileNameSlices['extension']);
        }
        return null;
    }

	
	
}