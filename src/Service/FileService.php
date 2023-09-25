<?php

    namespace App\Service;

    use Exception;
    use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
    use Symfony\Component\Filesystem\Filesystem;
    use Symfony\Component\HttpFoundation\File\Exception\FileException;
    use Symfony\Component\HttpFoundation\File\UploadedFile;

    class FileService
    {
        /**
         * @throws Exception
         */
        public function upload(UploadedFile $file, string $uploadDirectory, string $title): string
        {

            $fileName = $title;

            try {

                $file->move($uploadDirectory, $fileName);
            } catch (FileException $e) {
                throw new Exception('error moving uploaded file');
            }

            return $fileName;
        }

        public function deleteDirectory(string $directory)
        {
            $fileSystem = new Filesystem();

            $directoryToDelete = $directory;
            try {
                $fileSystem->remove($directoryToDelete);
            } catch (IOExceptionInterface $exception) {
                echo $exception;
            }
        }
    }