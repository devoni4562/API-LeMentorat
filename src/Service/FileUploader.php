<?php
    namespace App\Service;

    use Symfony\Component\HttpFoundation\File\Exception\FileException;
    use Symfony\Component\HttpFoundation\File\UploadedFile;

    class FileUploader
    {
        /**
         * @throws \Exception
         */
        public function upload(UploadedFile $file, string $uploadDirectory, string $title): string
        {

            $fileName = str_replace(' ', '_', $title).'.'.$file->guessExtension();

            try {
                $file->move($uploadDirectory, $fileName);
            }catch (FileException $e){
                throw new \Exception('error moving uploaded file');
            }

            return $fileName;
        }
    }