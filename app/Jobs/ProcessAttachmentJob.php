<?php

namespace App\Jobs;

use App\Models\Attachment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Symfony\Component\Process\Process;
use WebPConvert\WebPConvert;
use Exception;

class ProcessAttachmentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $check = false;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public Attachment $attachment)
    {
        //
    }

    private function setTrue()
    {
        $this->attachment->is_proccessed = true;
        $this->attachment->save();
        $this->check = true;
    }

    private function recordSize($originalPath = null, $compressedPath = null)
    {
        $this->attachment->original_size = filesize($originalPath ?? $this->attachment->file_path);
        $this->attachment->compressed_size = filesize($compressedPath ?? $this->attachment->temp_file_path);
        $this->attachment->save();
    }

    private function setFalse()
    {
        $this->check = true;
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $attachment = $this->attachment;

        $mediaExtensions = [
            "3g2",
            "3gp",
            "aaf",
            "asf",
            "avchd",
            "avi",
            "drc",
            "flv",
            "m2v",
            "m4p",
            "m4v",
            "mkv",
            "mng",
            "mov",
            "mp2",
            "mp4",
            "mpe",
            "mpeg",
            "mpg",
            "mpv",
            "mxf",
            "nsv",
            "ogg",
            "ogv",
            "qt",
            "rm",
            "rmvb",
            "roq",
            "svi",
            "vob",
            "webm",
            "wmv",
            "yuv",
            "mp3",
            "flac",
            "wav"
        ];

        $imgExtensions = [
            "ase",
            "art",
            "bmp",
            "blp",
            "cd5",
            "cit",
            "cpt",
            "cr2",
            "cut",
            "dds",
            "dib",
            "djvu",
            "egt",
            "exif",
            "gif",
            "gpl",
            "grf",
            "icns",
            "ico",
            "iff",
            "jng",
            "jpeg",
            "jpg",
            "jfif",
            "jp2",
            "jps",
            "lbm",
            "max",
            "miff",
            "mng",
            "msp",
            "nitf",
            "ota",
            "pbm",
            "pc1",
            "pc2",
            "pc3",
            "pcf",
            "pcx",
            "pdn",
            "pgm",
            "PI1",
            "PI2",
            "PI3",
            "pict",
            "pct",
            "pnm",
            "pns",
            "ppm",
            "psb",
            "psd",
            "pdd",
            "psp",
            "px",
            "pxm",
            "pxr",
            "qfx",
            "raw",
            "rle",
            "sct",
            "sgi",
            "rgb",
            "int",
            "bw",
            "tga",
            "tiff",
            "tif",
            "vtf",
            "xbm",
            "xcf",
            "xpm",
            "3dv",
            "amf",
            "ai",
            "awg",
            "cgm",
            "cdr",
            "cmx",
            "dxf",
            "e2d",
            "egt",
            "eps",
            "fs",
            "gbr",
            "odg",
            "svg",
            "stl",
            "vrml",
            "x3d",
            "sxd",
            "v2d",
            "vnd",
            "wmf",
            "emf",
            "art",
            "xar",
            "png",
            "webp",
            "jxr",
            "hdp",
            "wdp",
            "cur",
            "ecw",
            "iff",
            "lbm",
            "liff",
            "nrrd",
            "pam",
            "pcx",
            "pgf",
            "sgi",
            "rgb",
            "rgba",
            "bw",
            "int",
            "inta",
            "sid",
            "ras",
            "sun",
            "tga"
        ];

        $docsExtension = ['pdf'];

        if (in_array($attachment->ext, $mediaExtensions)) {


            $process = new Process(['ffmpeg', "-i", $attachment->file_path, "-vf", "scale=-1:360", "-preset", "veryslow", "$attachment->temp_file_path"]);

            $process->mustRun();

            if (!$process->isSuccessful()) {
                return;
            }
            $this->recordSize();
            rename($attachment->temp_file_path, $attachment->file_path);

            $this->setTrue();
        }

        if (in_array($attachment->ext, $docsExtension)) {
            if ($attachment->ext == "pdf") {
                $process = new Process([
                    'gs',
                    "-sDEVICE=pdfwrite",
                    "-dCompatibilityLevel=1.4",
                    "-dPDFSETTINGS=/printer",
                    "-dNOPAUSE",
                    "-dQUIET",
                    "-dBATCH",
                    "-sOutputFile=$attachment->temp_file_path",
                    $attachment->file_path,
                ]);
            }

            $process->mustRun();

            if (!$process->isSuccessful()) {
                return;
            }
            $this->recordSize();
            rename($attachment->temp_file_path, $attachment->file_path);
            $this->setTrue();
        }

        if (in_array($attachment->ext, $imgExtensions)) {

            try {
                $compressedPath = str_replace($attachment->ext, "webp", $attachment->temp_file_path);
                WebPConvert::convert($attachment->file_path, $compressedPath);
                $this->recordSize(compressedPath: $compressedPath);
                rename($compressedPath, $attachment->file_path);
                $this->setTrue();
            } catch (Exception $e) {
                $optimizerChain = OptimizerChainFactory::create();
                $optimizerChain->optimize($attachment->file_path, $attachment->temp_file_path);
                $this->recordSize(compressedPath: $attachment->temp_file_path);
                rename($attachment->temp_file_path, $attachment->file_path);
                $this->setTrue();
            }
        };




        return 0;
    }

    public function getCheck(): bool
    {
        return $this->check;
    }

    public function failed(Exception $exception)
    {
        // print($exception->getMessage());
    }
}
