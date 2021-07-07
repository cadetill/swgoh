<?php
/**
* http://alvarotrigo.com
*  
* This class allows to print text over a given image.
* It needs from a TrueType font format (ttf).
* 
* The resulting image will be show in png format.
* 
* @author alvarotrigolopez 
* @see http://www.php.net/manual/es/ref.image.php
*/
class textPainter{
	private $img;
	private $textColor;
	private $position = array();
	private $startPosition = array();
	
	private $imagePath;
	private $text;
	private $fontFile;
	private $fontSize;
	private $format;
	
	
	/**
	* Class Constructor 
	* 
	* @param string $imagePath background image path
	* @param string $text text to print
	* @param string $fontFile the .ttf font file (TrueType)
	* @param integer $fontSize font size
	* 
	* @access public
	*/
	public function __construct($imagePath, $text, $fontFile, $fontSize){
		$this->imagePath = $imagePath;
		$this->text = $text;
		$this->fontFile = $fontFile;
		$this->fontSize = $fontSize;
		
		$this->setFormat();
		$this->setQuality();
		$this->createImage();
		$this->setTextColor();
		$this->setPosition();
	}
	
	/**
	* Sets the text color using the RGB color scale.
	* 
	* @param integer $R red quantity
	* @param integer $G gren quantity
	* @param integer $B blue quantity
	* 
	* @access public
	*/
	public function setTextColor($R=230, $G=240, $B=230){
		$this->textColor = imagecolorallocate ($this->img, $R, $G, $B);
	}
	
	/**
	* Shows the resulting image (background image + text)
	* On the same format as the original background image.
	* 
	* @access public
	*/
	public function show(){
		//show thumb
		header("Content-type: image/".$this->format);	
		//creates the text over the background image
		imagettftext($this->img, $this->fontSize, 0, $this->startPosition["x"], $this->startPosition["y"], $this->textColor, $this->fontFile, $this->text);

		switch ($this->format){
			case "JPEG":
				imagejpeg($this->img,NULL,$this->jpegQuality);
				break;
			case "PNG":
				imagepng($this->img);
				break;
			case "GIF":
				imagegif($this->img);
				break;
			case "WBMP":
				imagewbmp($this->img);
				break;
			default:
				imagepng($this->img);
		}
        }
        
        public function writeText() {
          imagettftext($this->img, $this->fontSize, 0, $this->startPosition["x"], $this->startPosition["y"], $this->textColor, $this->fontFile, $this->text);
        }
        
        /*
         * Added by cadetill
         * method to save the generated image to a file
         * @param string $dir folder where generate the image
         * @param string $prefix prefix for the name
         */
        public function saveImage($dir, $prefix) {
          imagettftext($this->img, $this->fontSize, 0, $this->startPosition["x"], $this->startPosition["y"], $this->textColor, $this->fontFile, $this->text);
          
          $tempName = tempnam($dir, $prefix);
          
          switch ($this->format){
	    case "JPEG":
              imagejpeg($this->img, $tempName, $this->jpegQuality);
              break;
            case "PNG":
              imagepng($this->img, $tempName);
              break;
            case "GIF":
              imagegif($this->img, $tempName);
              break;
            case "WBMP":
              imagewbmp($this->img, $tempName);
              break;
            default:
              imagepng($this->img, $tempName);
              break;
          }
          return $tempName;
        }
	
	/**
	* Sets the quality of the resulting JPEG image.
	* Default: 85
	* @param integer $value quality
	* @access public
	*/
	public function setQuality($value=85){
		$this->jpegQuality = $value;
	}
	
	/**
	* Calculates the X and Y coordinates for the desired position 
	* of the text. 
	* @param string $x x position: left, center, right or custom 
	* @param string $y y position: top, center, bottom or custom
	* @access public
	*/
	public function setPosition($x="center", $y="center"){
		$this->position["x"] = $x;
		$this->position["y"] = $y;
		
		$dimensions = $this->getTextDimensions();
		
		if($x=="left"){
			$this->startPosition["x"] = 0;
		}
		else if($x=="center"){
			$this->startPosition["x"] = imagesx($this->img)/2 - $dimensions["width"]/2;
		}
		else if($x=="right"){
			$this->startPosition["x"] = imagesx($this->img) - $dimensions["width"];
		}
		//custom
		else{
			$this->startPosition["x"] = $x;
		}
		
		if($y=="top"){
			$this->startPosition["y"] = 0 + $dimensions["heigh"];
		}
		else if($y=="center"){
			$this->startPosition["y"]  = imagesy($this->img)/2 + $dimensions["heigh"]/2;
		}
		else if($y=="bottom"){
			$this->startPosition["y"]  = imagesy($this->img);
		}
		//custom
		else{
			$this->startPosition["y"] = $y;
		}
	
	}
	
	/**
	* Determines the format of the background image and 
	* sets it for the final image result.
	* Supported formats: jpeg, jpg, png, gif, wbmp
	* @access private
	*/
	private function setFormat(){
		$this->format = preg_replace("/.*\.(.*)/","\\1",$this->imagePath);
		$this->format = strtoupper($this->format);
		
		if($this->format=="JPG" || $this->format=="JPEG"){
			$this->format="JPEG";
		}
		else if($this->format=="PNG"){
			$this->format="PNG";
		}
		else if ($this->format=="GIF"){
			$this->format="GIF";
		}
		else if ($this->format=="WBMP"){
			$this->format="WBMP";
		}else{
			echo "Not Supported File";
			exit();
		}
	}
	
	/**
	* Create a new image to work with from the given background 
	* image.
	* Supported formats: jpeg, jpg, png, gif, wbmp
	* @access private
	*/
	private function createImage(){
		if($this->format=="JPEG"){
			$this->img = imagecreatefromjpeg($this->imagePath);
		}
		else if($this->format=="PNG"){
			$this->img = imagecreatefrompng($this->imagePath);
		}
		else if ($this->format=="GIF"){
			$this->img = imagecreatefromgif($this->imagePath);
		}
		else if ($this->format="WBMP"){
			$this->img = imagecreatefromwbmp($this->imagePath);
		}else{
			echo "Not Supported File";
			exit();
		}
                
          // added by cadetill --> do transparent backgraund
          imagesavealpha($this->img, true);
          $alpha = imagecolorallocatealpha($this->img, 0, 0, 0, 127);
          imagefill($this->img, 0, 0, $alpha);
	}
	
	/**
	* Sets the font file for the text.
	* 
	* @param string $fontFile the .ttf font file (TrueType)
	* @access public
	*/
	public function setFontFile($fontFile){
		$this->fontFile = $fontFile;
		
		//recalculate the text position depending on the new font file
		$this->setPosition($this->position["x"], $this->position["y"]);
	}
	
	/**
	* Sets the font size for the text.
	* 
	* @param integer $fontSize 
	* @access public
	*/
	public function setFontSize($fontSize){
		$this->fontSize = $fontSize;
		
		//recalculate the text position depending on the new font size
		$this->setPosition($this->position["x"], $this->position["y"]);
	}
        
        public function setText($text) {
          $this->text = $text;
        }
	
	/**
	* It returns the dimensions of the text to print with the given 
	* size and font.
	* 
	* @return array containing the width and height (width,heigh) of the text to print.
	* @access public
	*/
	private function getTextDimensions(){
		$dimensions = imagettfbbox($this->fontSize, 0, $this->fontFile, $this->text);
	
		$minX = min(array($dimensions[0],$dimensions[2],$dimensions[4],$dimensions[6]));
		$maxX = max(array($dimensions[0],$dimensions[2],$dimensions[4],$dimensions[6]));
		
		$minY = min(array($dimensions[1],$dimensions[3],$dimensions[5],$dimensions[7]));
		$maxY = max(array($dimensions[1],$dimensions[3],$dimensions[5],$dimensions[7]));
		
		return array(
			'width' => $maxX - $minX,
			'heigh' => $maxY - $minY
		);
	}  
}