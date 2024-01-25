<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PublishSampleCSV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'publish:SampleCSV';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'it will publish sample csv';

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

    /**
     * Author: Ganesh Kandu
     * Created:
     * Modified:
     * Modified by:
     */

    public function handle()
    {
		echo "\033[31m\n██████╗░░█████╗░███╗░░██╗██╗░░██╗███╗░░░███╗███████╗  ██████╗░██████╗░███╗░░░███╗\n██╔══██╗██╔══██╗████╗░██║██║░██╔╝████╗░████║██╔════╝  ██╔══██╗██╔══██╗████╗░████║\n██████╔╝███████║██╔██╗██║█████═╝░██╔████╔██║█████╗░░  ██║░░██║██████╔╝██╔████╔██║\n██╔══██╗██╔══██║██║╚████║██╔═██╗░██║╚██╔╝██║██╔══╝░░  ██║░░██║██╔══██╗██║╚██╔╝██║\n██║░░██║██║░░██║██║░╚███║██║░╚██╗██║░╚═╝░██║██║░░░░░  ██████╔╝██║░░██║██║░╚═╝░██║\n╚═╝░░╚═╝╚═╝░░╚═╝╚═╝░░╚══╝╚═╝░░╚═╝╚═╝░░░░░╚═╝╚═╝░░░░░  ╚═════╝░╚═╝░░╚═╝╚═╝░░░░░╚═╝\033[0m\n\n";
		echo "\033[31m Installing sample csv's \033[0m\n";
        $this->recursive_copy('public/storage','storage/app/public');
    }

	function recursive_copy($src,$dst) {
		$dir = opendir($src);
		@mkdir($dst);
		while(( $file = readdir($dir)) ) {
			if (( $file != '.' ) && ( $file != '..' )) {
				if ( is_dir($src . '/' . $file) ) {
					$this->recursive_copy($src .'/'. $file, $dst .'/'. $file);
				}
				else {
					echo "\033[32m Coping: \033[0m \033[36m $file to $dst/ \033[0m\n";
					copy($src .'/'. $file,$dst .'/'. $file);
					echo "\033[32m Copied: \033[0m \033[36m $file to $dst/ \033[0m\n";
				}
			}
		}
		closedir($dir);
	}
}
