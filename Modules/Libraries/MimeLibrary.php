<?php
/**
* Biblioteca de Mime Types
*
* @package	Library
* @author 	Lucas/Postali
*/

	class MimeLibrary extends _Core 
	{
		CONST GENERIC =	'application/octet-stream';

		//Texto
		CONST TXT 	= 	'text/plain';
		CONST DOC 	= 	'application/msword';
		CONST DOCX 	=	'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
		CONST XLS 	= 	'application/vnd.ms-excel';
		CONST XLS2 	= 	'application/excel';
		CONST XLS3 	= 	'application/x-excel';
		CONST XLS4 	= 	'application/x-msexcel';
		CONST XLSX 	= 	'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
		CONST PPT 	=	'application/vnd.ms-powerpoint';
		CONST PPTX 	=	'application/vnd.openxmlformats-officedocument.presentationml.presentation';
		CONST PDF 	=	'application/pdf';
		CONST RTF 	=	'application/rtf';

		//Vídeo
		CONST AVI 	= 	'video/avi';
		CONST AVI2 	= 	'application/x-troff-msvideo';
		CONST AVI3 	= 	'video/msvideo';
		CONST AVI4 	= 	'video/x-msvideo';
		CONST AVS 	= 	'video/avs-video';
		CONST MOV 	=	'video/quicktime';
		CONST MOV2 	=	'video/quicktime';
		CONST MPEG 	=	'video/mpeg';
		CONST MP2 	=	'video/mpeg';
		CONST MP22 	=	'video/x-mpeg';
		CONST MP23 	=	'video/x-mpeq2a';

		//Imagem
		CONST BPM	=  	'image/bmp';
		CONST BPM2	=  	'image/x-windows-bmp';
		CONST ICO 	=	'image/x-icon';
		CONST GIF 	=	'image/gif';
		CONST JPEG 	=	'image/jpeg';
		CONST JPEG2	=	'image/pjpeg';
		CONST PNG 	=	'image/png';
		CONST TIFF 	=	'image/tiff';
		CONST TIFF2	=	'image/x-tiff';
		CONST SVG	=	'image/svg+xml';

		static public function Image ()
		{
			return [self::BPM,self::BPM2,self::ICO,self::GIF,self::JPEG,self::JPEG2,self::PNG,self::TIFF,self::TIFF2,self::SVG];
		}

		static public function WebImage ()
		{
			return [self::BPM,self::BPM2,self::GIF,self::JPEG,self::JPEG2,self::PNG];
		}

		//Música
		CONST MP3	=  	'audio/mpeg3';
		CONST MP32	=  	'audio/x-mpeg-3';
		CONST WAV	=  	'audio/wav';
		CONST WAV2	=  	'audio/x-wav';

		//Compactado
		CONST TGZ 	=	'application/gnutar';
		CONST TGZ2 	=	'application/x-compressed';
		CONST ZIP 	=	'application/zip';
		CONST ZIP2 	=	'application/x-zip-compressed';
		CONST ZIP3 	=	'application/x-compressed';
		CONST ZIP4 	=	'multipart/x-zip';
		CONST RAR 	=	'application/x-rar-compressed';
		CONST TAR 	=	'application/x-tar';

		//Internet
		CONST CSS 	=	'text/css';
		CONST CSS2 	=	'application/x-pointplus';
		CONST HTML 	= 	'text/html';
		CONST JS 	= 	'text/javascript';
		CONST JS2 	= 	'application/x-javascript';
		CONST JS3 	= 	'application/javascript';
		CONST JS4 	= 	'application/ecmascript';
		CONST JS5 	= 	'text/ecmascript';
		CONST XML 	=	'text/xml';
		CONST XML2 	=	'application/xml';
		CONST JSON 	=	'application/json';

	}

?>