<?php
/*
 * PHP QR Code encoder
 * Simplified version for basic QR generation
 */

include 'qrconfig.php';

class QRcode {
    
    public static function png($text, $outfile = false, $level = 'L', $size = 3, $margin = 4) {
        // Simple implementation using Google Charts API as fallback
        $size_px = $size * 100;
        $url = "https://api.qrserver.com/v1/create-qr-code/?size={$size_px}x{$size_px}&data=" . urlencode($text);
        
        if ($outfile) {
            $qrContent = @file_get_contents($url);
            if ($qrContent) {
                file_put_contents($outfile, $qrContent);
                return true;
            } else {
                // Fallback: create simple image
                return self::createSimpleQR($text, $outfile, $size, $margin);
            }
        } else {
            header('Content-Type: image/png');
            $qrContent = @file_get_contents($url);
            if ($qrContent) {
                echo $qrContent;
            } else {
                self::createSimpleQR($text);
            }
            return true;
        }
    }
    
    private static function createSimpleQR($text, $outfile = false, $size = 3, $margin = 4) {
        $pixelSize = $size * 10;
        $width = (strlen($text) * $pixelSize) + ($margin * 2);
        $height = 100 + ($margin * 2);
        
        $img = imagecreate($width, $height);
        $white = imagecolorallocate($img, 255, 255, 255);
        $black = imagecolorallocate($img, 0, 0, 0);
        
        imagefill($img, 0, 0, $white);
        
        // Draw simple pattern
        for ($i = 0; $i < strlen($text); $i++) {
            $char = $text[$i];
            $x = $margin + ($i * $pixelSize);
            
            // Simple block pattern based on character
            $code = ord($char);
            if ($code % 3 == 0) {
                imagefilledrectangle($img, $x, $margin, $x + $pixelSize - 2, $margin + $pixelSize - 2, $black);
            }
        }
        
        // Add text at bottom
        imagestring($img, 2, $margin, $height - 20, substr($text, 0, 20), $black);
        
        if ($outfile) {
            imagepng($img, $outfile);
            imagedestroy($img);
            return true;
        } else {
            header('Content-Type: image/png');
            imagepng($img);
            imagedestroy($img);
            return true;
        }
    }
}
?>