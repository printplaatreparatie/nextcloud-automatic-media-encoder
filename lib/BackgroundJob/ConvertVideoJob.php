<?php

namespace OCA\AutomaticMediaEncoder\BackgroundJob;

use OCP\Files\File;

class ConvertVideojob extends BackgroundJob
{
    public function run($arguments)
    {
        parent::run($arguments);

        try {
            ['user_id' => $userId, 'source_file_id' => $sourceFileId, 'rule' => $rule] = $arguments;

            $sourceFiles = $this->rootFolder->getUserFolder($userId)->getById($sourceFileId);
            $sourceFile = $sourceFiles instanceof File ? $sourceFiles : reset($sourceFiles);
            
            // the following statement may create a temporary file
            $sourcePath = $sourceFile->getStorage()->getLocalFile($sourceFile->getPath()); 
            
            $outputPath = str_replace($sourcePath, ".{$rule['from_format']['extension']}", ".{$rule['to_format']['extension']}"); // may be a temporary directory
            
            $this->runProcess("ffmpeg -i $sourcePath -c $outputPath");

            $destinationStream = $sourceFile->getParent()->newFile(basename($outputPath))->fopen('w'); // may be a temporary file

            stream_copy_to_stream(fopen($outputPath, 'r'), $destinationStream); // write temporary file to source file's parent directory

            if (is_resource($destinationStream)) {
                fclose($destinationStream);
            }

            switch ($rule['postEncodeRule']) {
                case 'delete':
                    $this->deleteVideo($sourceFile, $rule);
                    break;
                case 'move':
                    $this->moveVideo($sourceFile, $rule);
                    break;
                default: // including 'keep'
                    break;
            }
        } catch (\Throwable $e) {
            $this->handleError($e);
        }
    }

    private function deleteVideo($video, $rule)
    {
        // delete file in nextcloud (hopefully triggers physical delete)
    }

    private function moveVideo($video, $rule)
    {
        // move $video to $rule['moveMediaDirectory'];
    }

    private function writeUserFile($userFolder, $filename, $content)
    {
        if (!$userFolder->nodeExists($filename)) {
            $userFolder->touch($filename);
        }
        $file = $userFolder->get($filename);
        $file->putContent($content);
    }
}
