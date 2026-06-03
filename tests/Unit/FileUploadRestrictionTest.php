<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Requests\CreateTempFileRequest;
use Illuminate\Support\Facades\Validator;

class FileUploadRestrictionTest extends TestCase
{
    public function test_only_pdf_and_mp4_files_are_allowed()
    {
        $request = new CreateTempFileRequest();
        $rules = $request->rules();

        // 1. Valid PDF file name
        $validator1 = Validator::make([
            'filename' => 'document.pdf',
        ], [
            'filename' => $rules['filename']
        ]);
        $this->assertTrue($validator1->passes());

        // 2. Valid MP4 file name
        $validator2 = Validator::make([
            'filename' => 'video.mp4',
        ], [
            'filename' => $rules['filename']
        ]);
        $this->assertTrue($validator2->passes());

        // 3. Case insensitivity check
        $validator3 = Validator::make([
            'filename' => 'video.MP4',
        ], [
            'filename' => $rules['filename']
        ]);
        $this->assertTrue($validator3->passes());

        // 4. Invalid DOCX file name
        $validator4 = Validator::make([
            'filename' => 'document.docx',
        ], [
            'filename' => $rules['filename']
        ]);
        $this->assertFalse($validator4->passes());
        $this->assertEquals(
            'File format is not supported. (Supported files: PDF or MP4)',
            $validator4->errors()->first('filename')
        );
    }
}
