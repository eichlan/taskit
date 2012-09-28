<?php

class FileController extends Controller
{
	private $sizes = array(
		'miniIcon' => 16,
		'icon' => 32,
		'avatar' => 64,
		'thumbnail' => 128,
		'original' => true,
	);

	public static function resetCache( $cat, $id )
	{
		foreach( glob('protected/files/'.$cat.'/cache/*-'.$id) as $fname )
		{
			unlink( $fname );
		}
	}

	public function actionIndex( $cat, $id, $size='original', $reset=null )
	{
		if( preg_match('@[\\\\./]@', $cat ) > 0 ||
			preg_match('@([\\\\/]|\.\.|^\.)@', $id ) > 0 ||
			!isset($this->sizes[$size]) )
			throw new CHttpException(404, "File not found");
		
		$filePath = 'protected/files/'.$cat.'/'.$id;
		$mime = null;
		switch( $cat )
		{
		case 'avatar':
			$id = (int)$id;
			if( !file_exists( 'protected/files/avatar' ) )
				mkdir( 'protected/files/avatar' );
			if( !file_exists( $filePath ) )
			{
				if( User::model()->findByPk( $id ) === NULL )
					throw new CHttpException(404, "File not found");
				$ap = new AvatarPicker();
				$ap->selectRandom( $filePath );
			}
			$mime = 'image/png';
			break;

		case 'attachment':
			$fa = FileAttachment::model()->findByPk( $id );
			if( $fa === null )
				throw new CHttpException(404, "File not found");
			$mime = $fa->mimetype;
			break;

		default:
			if( !file_exists( $filePath ) )
				throw new CHttpException(404, "File not found");
			$fi = finfo_open(FILEINFO_MIME);
			$mime = finfo_file( $fi, $filePath );
			break;
		}

		if( $reset !== NULL )
		{
			FileController::resetCache( $cat, $id );
		}

		if( $size != 'original' )
		{
			if( !file_exists( 'protected/files/'.$cat.'/cache' ) )
				mkdir('protected/files/'.$cat.'/cache');
			$cachePath = 'protected/files/'.$cat.'/cache/'.$size.'-'.$id;
			if( !file_exists( $cachePath ) )
			{
				if( $this->rescale( $filePath, $cachePath, $this->sizes[$size] ) )
					$filePath = $cachePath;
			}
			else
				$filePath = $cachePath;
		}

		header('Content-Type: ' . $mime );
		readfile( $filePath );
	}

	private function rescale( $src, $dst, $maxSize )
	{
		$img = imagecreatefromstring( file_get_contents( $src ) );

		$sw = imagesx( $img );
		$sh = imagesy( $img );

		if( $sw <$maxSize && $sh < $maxSize )
		{
			imagedestroy( $img );
			copy($src,$dst);
			return false;
		}

		if( $sw > $sh )
		{
			$dw = $maxSize;
			$dh = (int)((double)$sh/(double)$sw*$maxSize+0.5);
		}
		else
		{
			$dh = $maxSize;
			$dw = (int)((double)$sw/(double)$sh*$maxSize+0.5);
		}
		
		imagesavealpha( $img, true );

		$new = imagecreatetruecolor( $dw, $dh );
		imagecolortransparent($new,
			imagecolorallocatealpha($new, 0, 0, 0, 127)
		);
		imagealphablending($new, false);
		imagesavealpha($new, true);

		imagecopyresampled( $new, $img, 0, 0, 0, 0,
			$dw, $dh, $sw, $sh );
		imagedestroy( $img );

		imagepng( $new, $dst );
		imagedestroy( $new );

		return true;
	}
}
