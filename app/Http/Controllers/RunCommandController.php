<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RunCommandController extends Controller
{
	public function command(Request $request){

		echo '<style>
		body,a { color: FFFFFF; background-color: 000000;}
		.red {color: red;}
		.green {color: green;}
		.blue {color: blue;}
		.Cyan {color: Cyan;}
		</style>';

		$commands1 = [
			'read_arn_data',
			'update_composer',
			'migrate',
			'assignusertoarn_map',
			'db_seed',
			'publish_SampleCSV',
			'publish_shell',
			'map_pin',
			'check_hrms'
		];

		$links = [
			'<a href="?read_arn_data">read_arn_data</a>',
			'<a href="?update_composer">update_composer</a>',
			'<a href="?migrate">migrate</a>',
			'<a href="?assignusertoarn_map">assignusertoarn:map</a>',
			'<a href="?db_seed">db:seed</a>',
			'<a href="?publish_SampleCSV">publish:SampleCSV</a>',
			'<a href="?publish_shell">publish:shell</a>',
			'<a href="?map_pin">map:pin</a>',
			'<a href="?check_hrms">check:hrms</a>',
		];

		$commands2 = array_keys($request->all());
		$command = array_values(array_intersect($commands1,$commands2));
		if(count($command)){
			$command = $command[0];
			$this->{$command}();
		}else{
			echo implode("<br/>",$links);
		}
	}

	public function update_composer(){

		if(!is_writable('storage')){
			echo "storage folder is not writable.";
			return false;
		}

		shell_exec('cd '.base_path().'/storage ; curl -sS https://getcomposer.org/installer | '.PHP_BINDIR.'/php ; '.'cd '.base_path().' ; '.PHP_BINDIR.'/php ./storage/composer.phar update  > ./public/storage/logs/composer_logs.txt 2>&1 & echo $!');
		
		$file = url('/storage/logs/composer_logs.txt');

		echo '<script src="https://drm.rankmf.com/js/jquery.min.js"></script>
		Updating composer dependencies
		<pre id="terminal"></pre>
		<script>
		$(document).ready(function(){
			$("#terminal").load("'.$file.'?"+Math.random());
			window.setInterval(function () {
				$("#terminal").load("'.$file.'?"+Math.random(),function(){
					$(this).html($(this).html().replaceAll("[31m", "<span class=\"blue\">"));
					$(this).html($(this).html().replaceAll("[32m", "<span class=\"green\">"));
					$(this).html($(this).html().replaceAll("[33m", "<span class=\"Cyan\">"));
					$(this).html($(this).html().replaceAll("[36m", "<span class=\"red\">"));
					$(this).html($(this).html().replaceAll("[39m", "</span>"));
					$(this).html($(this).html().replaceAll("[0m", "</span>"));
				});
			}, 1000);
		});
		</script>';
	}

    public function read_arn_data(){

		if(!is_writable('storage/logs')){
			echo "storage/logs folder is not writable.";
			return false;
		}

		if(!$this->is_running()){
			shell_exec('cd '.base_path().';sh ./vendor/shell_scripts/read_arn_data_from_partners_mongodb.sh  > /dev/null 2>&1 & echo $!');
			echo "Command Running with ".$this->getpids()." Process id\n";
		}else{
			echo "Command Already Running with ".$this->getpids()." Process id\n";
		}
		
		$file = url('/storage/logs/read_arn_data_from_partners_mongodb_'.date('Y-m-d').'.txt');

		echo '<script src="https://drm.rankmf.com/js/jquery.min.js"></script>
		<pre id="terminal"></pre>
		<script>
		$(document).ready(function(){
			$("#terminal").load("'.$file.'?"+Math.random());
			window.setInterval(function () {
				$("#terminal").load("'.$file.'?"+Math.random());
			}, 1000);
		});
		</script>';
	}

    public function migrate(){

		shell_exec('cd '.base_path().'/storage ; curl -sS https://getcomposer.org/installer | '.PHP_BINDIR.'/php ; '.'cd '.base_path().' ; '.PHP_BINDIR.'/php artisan migrate  > ./public/storage/logs/composer_logs.txt 2>&1 & echo $!');
		
		$file = url('/storage/logs/composer_logs.txt');

		echo '<script src="https://drm.rankmf.com/js/jquery.min.js"></script>
		Updating composer dependencies
		<pre id="terminal"></pre>
		<script>
		$(document).ready(function(){
			$("#terminal").load("'.$file.'?"+Math.random());
			window.setInterval(function () {
				$("#terminal").load("'.$file.'?"+Math.random(),function(){
					$(this).html($(this).html().replaceAll("[31m", "<span class=\"blue\">"));
					$(this).html($(this).html().replaceAll("[32m", "<span class=\"green\">"));
					$(this).html($(this).html().replaceAll("[33m", "<span class=\"Cyan\">"));
					$(this).html($(this).html().replaceAll("[36m", "<span class=\"red\">"));
					$(this).html($(this).html().replaceAll("[39m", "</span>"));
					$(this).html($(this).html().replaceAll("[0m", "</span>"));
				});
			}, 1000);
		});
		</script>';
	}

    public function assignusertoarn_map(){

		shell_exec('cd '.base_path().'/storage ; curl -sS https://getcomposer.org/installer | '.PHP_BINDIR.'/php ; '.'cd '.base_path().' ; '.PHP_BINDIR.'/php artisan assignusertoarn:map  > ./public/storage/logs/composer_logs.txt 2>&1 & echo $!');
		
		$file = url('/storage/logs/composer_logs.txt');

		echo '<script src="https://drm.rankmf.com/js/jquery.min.js"></script>
		Updating composer dependencies
		<pre id="terminal"></pre>
		<script>
		$(document).ready(function(){
			$("#terminal").load("'.$file.'?"+Math.random());
			window.setInterval(function () {
				$("#terminal").load("'.$file.'?"+Math.random(),function(){
					$(this).html($(this).html().replaceAll("[31m", "<span class=\"blue\">"));
					$(this).html($(this).html().replaceAll("[32m", "<span class=\"green\">"));
					$(this).html($(this).html().replaceAll("[33m", "<span class=\"Cyan\">"));
					$(this).html($(this).html().replaceAll("[36m", "<span class=\"red\">"));
					$(this).html($(this).html().replaceAll("[39m", "</span>"));
					$(this).html($(this).html().replaceAll("[0m", "</span>"));
				});
			}, 1000);
		});
		</script>';
	}

    public function db_seed(){

		shell_exec('cd '.base_path().'/storage ; curl -sS https://getcomposer.org/installer | '.PHP_BINDIR.'/php ; '.'cd '.base_path().' ; '.PHP_BINDIR.'/php artisan db:seed  > ./public/storage/logs/composer_logs.txt 2>&1 & echo $!');
		
		$file = url('/storage/logs/composer_logs.txt');

		echo '<script src="https://drm.rankmf.com/js/jquery.min.js"></script>
		Updating composer dependencies
		<pre id="terminal"></pre>
		<script>
		$(document).ready(function(){
			$("#terminal").load("'.$file.'?"+Math.random());
			window.setInterval(function () {
				$("#terminal").load("'.$file.'?"+Math.random(),function(){
					$(this).html($(this).html().replaceAll("[31m", "<span class=\"blue\">"));
					$(this).html($(this).html().replaceAll("[32m", "<span class=\"green\">"));
					$(this).html($(this).html().replaceAll("[33m", "<span class=\"Cyan\">"));
					$(this).html($(this).html().replaceAll("[36m", "<span class=\"red\">"));
					$(this).html($(this).html().replaceAll("[39m", "</span>"));
					$(this).html($(this).html().replaceAll("[0m", "</span>"));
				});
			}, 1000);
		});
		</script>';
	}

    public function publish_SampleCSV(){

		shell_exec('cd '.base_path().'/storage ; curl -sS https://getcomposer.org/installer | '.PHP_BINDIR.'/php ; '.'cd '.base_path().' ; '.PHP_BINDIR.'/php artisan publish:SampleCSV  > ./public/storage/logs/composer_logs.txt 2>&1 & echo $!');
		
		$file = url('/storage/logs/composer_logs.txt');

		echo '<script src="https://drm.rankmf.com/js/jquery.min.js"></script>
		Updating composer dependencies
		<pre id="terminal"></pre>
		<script>
		$(document).ready(function(){
			$("#terminal").load("'.$file.'?"+Math.random());
			window.setInterval(function () {
				$("#terminal").load("'.$file.'?"+Math.random(),function(){
					$(this).html($(this).html().replaceAll("[31m", "<span class=\"blue\">"));
					$(this).html($(this).html().replaceAll("[32m", "<span class=\"green\">"));
					$(this).html($(this).html().replaceAll("[33m", "<span class=\"Cyan\">"));
					$(this).html($(this).html().replaceAll("[36m", "<span class=\"red\">"));
					$(this).html($(this).html().replaceAll("[39m", "</span>"));
					$(this).html($(this).html().replaceAll("[0m", "</span>"));
				});
			}, 1000);
		});
		</script>';
	}

    public function publish_shell(){

		shell_exec('cd '.base_path().'/storage ; curl -sS https://getcomposer.org/installer | '.PHP_BINDIR.'/php ; '.'cd '.base_path().' ; '.PHP_BINDIR.'/php artisan publish:shell  > ./public/storage/logs/composer_logs.txt 2>&1 & echo $!');
		
		$file = url('/storage/logs/composer_logs.txt');

		echo '<script src="https://drm.rankmf.com/js/jquery.min.js"></script>
		Updating composer dependencies
		<pre id="terminal"></pre>
		<script>
		$(document).ready(function(){
			$("#terminal").load("'.$file.'?"+Math.random());
			window.setInterval(function () {
				$("#terminal").load("'.$file.'?"+Math.random(),function(){
					$(this).html($(this).html().replaceAll("[31m", "<span class=\"blue\">"));
					$(this).html($(this).html().replaceAll("[32m", "<span class=\"green\">"));
					$(this).html($(this).html().replaceAll("[33m", "<span class=\"Cyan\">"));
					$(this).html($(this).html().replaceAll("[36m", "<span class=\"red\">"));
					$(this).html($(this).html().replaceAll("[39m", "</span>"));
					$(this).html($(this).html().replaceAll("[0m", "</span>"));
				});
			}, 1000);
		});
		</script>';
	}

    public function check_hrms(){

		shell_exec('cd '.base_path().'/storage ; curl -sS https://getcomposer.org/installer | '.PHP_BINDIR.'/php ; '.'cd '.base_path().' ; '.PHP_BINDIR.'/php artisan check:hrms  > ./public/storage/logs/composer_logs.txt 2>&1 & echo $!');
		
		$file = url('/storage/logs/composer_logs.txt');

		echo '<script src="https://drm.rankmf.com/js/jquery.min.js"></script>
		Updating composer dependencies
		<pre id="terminal"></pre>
		<script>
		$(document).ready(function(){
			$("#terminal").load("'.$file.'?"+Math.random());
			window.setInterval(function () {
				$("#terminal").load("'.$file.'?"+Math.random(),function(){
					$(this).html($(this).html().replaceAll("[31m", "<span class=\"blue\">"));
					$(this).html($(this).html().replaceAll("[32m", "<span class=\"green\">"));
					$(this).html($(this).html().replaceAll("[33m", "<span class=\"Cyan\">"));
					$(this).html($(this).html().replaceAll("[36m", "<span class=\"red\">"));
					$(this).html($(this).html().replaceAll("[39m", "</span>"));
					$(this).html($(this).html().replaceAll("[0m", "</span>"));
				});
			}, 1000);
		});
		</script>';
	}

    public function map_pin(){

		shell_exec('cd '.base_path().'/storage ; curl -sS https://getcomposer.org/installer | '.PHP_BINDIR.'/php ; '.'cd '.base_path().' ; '.PHP_BINDIR.'/php artisan map:pin  > ./public/storage/logs/composer_logs.txt 2>&1 & echo $!');
		
		$file = url('/storage/logs/composer_logs.txt');

		echo '<script src="https://drm.rankmf.com/js/jquery.min.js"></script>
		Updating composer dependencies
		<pre id="terminal"></pre>
		<script>
		$(document).ready(function(){
			$("#terminal").load("'.$file.'?"+Math.random());
			window.setInterval(function () {
				$("#terminal").load("'.$file.'?"+Math.random(),function(){
					$(this).html($(this).html().replaceAll("[31m", "<span class=\"blue\">"));
					$(this).html($(this).html().replaceAll("[32m", "<span class=\"green\">"));
					$(this).html($(this).html().replaceAll("[33m", "<span class=\"Cyan\">"));
					$(this).html($(this).html().replaceAll("[36m", "<span class=\"red\">"));
					$(this).html($(this).html().replaceAll("[39m", "</span>"));
					$(this).html($(this).html().replaceAll("[0m", "</span>"));
				});
			}, 1000);
		});
		</script>';
	}
	
	public function getpids(){
		return implode(' ', $this->task());
	}
	
	public function is_running(){
		$task = $this->task();
		return !empty($task[0]);
	}
	
	public function task(){
		return explode(' ',trim(preg_replace('/\s+/', ' ', shell_exec("ps aux | grep 'read_arn_data_from_partners_mongodb' | grep -v grep  | awk '{print $2}'"))));
	}
}
