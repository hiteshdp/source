<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Therapy;

class TherapyAccessCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'therapy:checkaccess';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        // Get therapy name
        $getTherapy = Therapy::select('therapy', 'canonicalName', 'id', 'apiID')->where('apiID', '!=', '0')->orderBy('id', 'DESC')->whereNull('deleted_at')->get()->toArray();

        $error = [];
        $success = [];

        foreach ($getTherapy as $therapy) {
            $canonicalName = $therapy['canonicalName'];
            $id = $therapy['id'];

            $url = 'http://wellkasa-github.local/therapy/'.$canonicalName;
          
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);


            if ($httpCode != 200) {
                $error [] = "therapy id: ".$id." Request URL: ".$canonicalName;
            } else {
                $success [] = "therapy id: ".$id." Request URL: ".$canonicalName;
            }

            curl_close($curl);
        }


        if (!empty($error)) {
            $path = storage_path() . '/error_therapy_access_check.log';
            $data = $error;
        } else {
            $path = storage_path() . '/success_therapy_access_check.log';
            $data = $success;
        }


        file_put_contents(
            $path, 
            implode(PHP_EOL, $data) . PHP_EOL, 
            FILE_APPEND | LOCK_EX
        );
    

    }
}
