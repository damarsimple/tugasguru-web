<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\TestCase;
use App\Actions\Attachment\Upload;
use App\Models\Attachment;
use Illuminate\Support\Str;
use App\Jobs\ProcessAttachmentJob;
use App\Models\User;
use GuzzleHttp\Client;

class AttachmentProcessTest extends TestCase
{
    public function test_can_compress_pdf()
    {
        
        $pdfUrl = "research.nhm.org/pdfs/10840/10840-001.pdf";

        $client = new Client();

        $attachment = new Attachment();

        $attachment->name = Str::uuid() . "." . "pdf";

        $attachment->mime = "application/pdf";
   
        User::first()->attachments()->save($attachment);

        $client->get($pdfUrl, ['sink' => $attachment->file_path]);

        $this->assertTrue((new ProcessAttachmentJob($attachment))->handle());
    }

    public function test_can_compress_image()
    {
        
        $imgUrl = "https://images.pexels.com/photos/2246476/pexels-photo-2246476.jpeg?cs=srgb&dl=pexels-maxime-francis-2246476.jpg&fm=jpg";

        $client = new Client();

        $attachment = new Attachment();

        $attachment->name = Str::uuid() . "." . "jpg";

        $attachment->mime = "image/jpg";
   
        User::first()->attachments()->save($attachment);

        $client->get($imgUrl, ['sink' => $attachment->file_path]);

        $this->assertTrue((new ProcessAttachmentJob($attachment))->handle());
    }

    public function test_can_compress_media()
    {
        
        $mediaUrl = "https://vod-progressive.akamaized.net/exp=1620780914~acl=%2Fvimeo-prod-skyfire-std-us%2F01%2F3527%2F17%2F442638213%2F1937941809.mp4~hmac=3f9f4f523374f518156eebb407c4fd1a96d2ac76579310276d107dffc9357dae/vimeo-prod-skyfire-std-us/01/3527/17/442638213/1937941809.mp4?download=1&filename=production+ID%3A4980005.mp4";

        $client = new Client();

        $attachment = new Attachment();

        $attachment->name = Str::uuid() . "." . "mp4";

        $attachment->mime = "video/mp4";
   
        User::first()->attachments()->save($attachment);

        $client->get($mediaUrl, ['sink' => $attachment->file_path]);

        $this->assertTrue((new ProcessAttachmentJob($attachment))->handle());
    }

    public function test_can_compress_media_sound()
    {
        
        $mediaUrl = "https://upload.wikimedia.org/wikipedia/commons/5/57/ESA-Astronaut-Paolo-Nespoli_Voice-intro-ENG.flac";

        $client = new Client();

        $attachment = new Attachment();

        $attachment->name = Str::uuid() . "." . "mp4";

        $attachment->mime = "video/mp4";
   
        User::first()->attachments()->save($attachment);

        $client->get($mediaUrl, ['sink' => $attachment->file_path]);

        $this->assertTrue((new ProcessAttachmentJob($attachment))->handle());
    }
}
