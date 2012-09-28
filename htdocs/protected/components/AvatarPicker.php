<?php

class AvatarPicker
{
	private $index;
	private $root;

	public function __construct( $indexPath="protected/avatarlib/index.json" )
	{
		$this->root = dirname( $indexPath );
		$this->index = json_decode( file_get_contents( $indexPath ), true );
	}

	public function select( $file, $x, $y, $output )
	{
		$img = $this->loadImage( $file );
		$this->extractImage( $img, $file, $x, $y, $output );
		imagedestroy( $img );
	}

	public function selectRandom( $output )
	{
		$file = array_rand( $this->index );
		$img = $this->loadImage( $file );
		$curw = imagesx( $img );
		$curh = imagesy( $img );

		$xw = ($curw-$this->index[$file]['margins']*2+
			$this->index[$file]['spacing'])/
			($this->index[$file]['width']+$this->index[$file]['spacing']);
		$xh = ($curh-$this->index[$file]['margins']*2+
			$this->index[$file]['spacing'])/
			($this->index[$file]['height']+$this->index[$file]['spacing']);

		$this->extractImage( $img, $file,
			mt_rand( 0, $xw-1 ), mt_rand( 0, $xh-1 ),
			$output );
		imagedestroy( $img );
	}

	private function loadImage( $file )
	{
		$img = imagecreatefromstring(
			file_get_contents( $this->root . '/' . $file )
		);

		imagesavealpha( $img, true );
		return $img;
	}

	private function extractImage( $img, $key, $x, $y, $output )
	{
		$dw = $this->index[$key]['width'];
		$dh = $this->index[$key]['height'];
		$new = imagecreatetruecolor( $dw, $dh );

		imagecolortransparent($new,
			imagecolorallocatealpha($new, 0, 0, 0, 127)
		);
		imagealphablending($new, false);
		imagesavealpha($new, true);

		imagecopy( $new, $img, 0, 0,
			$x*($this->index[$key]['width']+$this->index[$key]['spacing'])+
				$this->index[$key]['margins'],
			$y*($this->index[$key]['height']+$this->index[$key]['spacing'])+
				$this->index[$key]['margins'],
			$dw, $dh );

		imagepng( $new, $output );
		imagedestroy( $new );
	}
}

