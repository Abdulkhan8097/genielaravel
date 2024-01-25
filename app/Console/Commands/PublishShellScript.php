<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PublishShellScript extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'publish:shell';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'it will publish shell_script';

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
		echo "\033[31m Installing Shell Script's \033[0m\n";
		echo "\033[31m\n██████╗░░█████╗░███╗░░██╗██╗░░██╗███╗░░░███╗███████╗  ██████╗░██████╗░███╗░░░███╗\n██╔══██╗██╔══██╗████╗░██║██║░██╔╝████╗░████║██╔════╝  ██╔══██╗██╔══██╗████╗░████║\n██████╔╝███████║██╔██╗██║█████═╝░██╔████╔██║█████╗░░  ██║░░██║██████╔╝██╔████╔██║\n██╔══██╗██╔══██║██║╚████║██╔═██╗░██║╚██╔╝██║██╔══╝░░  ██║░░██║██╔══██╗██║╚██╔╝██║\n██║░░██║██║░░██║██║░╚███║██║░╚██╗██║░╚═╝░██║██║░░░░░  ██████╔╝██║░░██║██║░╚═╝░██║\n╚═╝░░╚═╝╚═╝░░╚═╝╚═╝░░╚══╝╚═╝░░╚═╝╚═╝░░░░░╚═╝╚═╝░░░░░  ╚═════╝░╚═╝░░╚═╝╚═╝░░░░░╚═╝\033[0m\n\n";
		
		if(is_link(base_path().'/vendor/shell_scripts')){
			echo "\033[32m Soft link Exist \033[0m\n";
		}elseif(is_dir(base_path().'/vendor/shell_scripts')){
			$this->recursive_copy('shell_scripts','vendor/shell_scripts');
		}else{
			echo "\033[32m Creating Soft link \033[0m\n";
			echo base_path().'/shell_scripts '.base_path().'/vendor/shell_scripts'."\n";
			exec('ln -s '.base_path().'/shell_scripts '.base_path().'/vendor/shell_scripts');
		}
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
