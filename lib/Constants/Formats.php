<?php

namespace OCA\AutomaticMediaEncoder\Constants;

class Formats
{
    public static function all()
    {
        return [
            [
                'extension' => '3gp',
                'decode' => true,
                'encode' => true,
                'label' => 'QuickTime / MOV',
            ],
            [
                'extension' => 'ac3',
                'decode' => true,
                'encode' => true,
                'label' => 'raw AC-3',
            ],
            [
                'extension' => 'avi',
                'decode' => true,
                'encode' => true,
                'label' => 'AVI (Audio Video Interleaved)',
            ],
            [
                'extension' => 'flac',
                'decode' => true,
                'encode' => true,
                'label' => 'raw FLAC',
            ],
            [
                'extension' => 'gif',
                'decode' => true,
                'encode' => true,
                'label' => 'CompuServe Graphics Interchange Format (GIF)',
            ],
            [
                'extension' => 'h264',
                'decode' => true,
                'encode' => true,
                'label' => 'raw H.264 video',
            ],
            [
                'extension' => 'hevc',
                'decode' => true,
                'encode' => true,
                'label' => 'raw HEVC video',
            ],
            [
                'extension' => 'm4a',
                'decode' => true,
                'encode' => false,
                'label' => 'QuickTime / MOV',
            ],
            [
                'extension' => 'm4v',
                'decode' => true,
                'encode' => true,
                'label' => 'raw MPEG-4 video',
            ],
            [
                'extension' => 'mov',
                'decode' => true,
                'encode' => true,
                'label' => 'QuickTime / MOV',
            ],
            [
                'extension' => 'mp3',
                'decode' => true,
                'encode' => true,
                'label' => 'MP3 (MPEG audio layer 3)',
            ],
            [
                'extension' => 'mp4',
                'decode' => true,
                'encode' => true,
                'label' => 'MP4 (MPEG-4 Part 14)',
            ],
            [
                'extension' => 'oga',
                'decode' => false,
                'encode' => true,
                'label' => 'Ogg Audio',
            ],
            [
                'extension' => 'ogg',
                'decode' => true,
                'encode' => true,
                'label' => 'Ogg',
            ],
            [
                'extension' => 'ogv',
                'decode' => false,
                'encode' => true,
                'label' => 'Ogg Video',
            ],
            [
                'extension' => 'opengl',
                'decode' => false,
                'encode' => true,
                'label' => 'OpenGL output',
            ],
            [
                'extension' => 'wav',
                'decode' => true,
                'encode' => true,
                'label' => 'WAV / WAVE (Waveform Audio)',
            ],
            [
                'extension' => 'webm',
                'decode' => true,
                'encode' => true,
                'label' => 'WebM',
            ],
        ];
    }
}
