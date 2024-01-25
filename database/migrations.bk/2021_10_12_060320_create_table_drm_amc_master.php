<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableDrmAmcMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drm_amc_master', function (Blueprint $table) {
            $table->id();
            $table->string('amc_code',255)->comment('AMC code');
            $table->string('amc_name',255)->comment('AMC name');
            $table->dateTime('created_at')->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
         });

        // Insert some stuff
        $insert_data = [
            ['amc_code'=>'400001', 'amc_name'=> 'BNP Paribas Mutual Fund'],
            ['amc_code'=>'400002', 'amc_name'=> 'PineBridge Mutual Fund'],
            ['amc_code'=>'400004', 'amc_name'=> 'Aditya Birla Sun Life Mutual Fund'],
            ['amc_code'=>'400005', 'amc_name'=> 'Baroda Mutual Fund'],
            ['amc_code'=>'400006', 'amc_name'=> 'Canara Robeco Mutual Fund'],
            ['amc_code'=>'400007', 'amc_name'=> 'L&T Mutual Fund'],
            ['amc_code'=>'400008', 'amc_name'=> 'Deutsche Mutual Fund'],
            ['amc_code'=>'400009', 'amc_name'=> 'DSP Mutual Fund'],
            ['amc_code'=>'400010', 'amc_name'=> 'Quant Mutual Fund'],
            ['amc_code'=>'400012', 'amc_name'=> 'Franklin Templeton Mutual Fund'],
            ['amc_code'=>'400013', 'amc_name'=> 'HDFC Mutual Fund'],
            ['amc_code'=>'400014', 'amc_name'=> 'HSBC Mutual Fund'],
            ['amc_code'=>'400015', 'amc_name'=> 'ICICI Prudential Mutual Fund'],
            ['amc_code'=>'400016', 'amc_name'=> 'ING Vysya Mutual Fund'],
            ['amc_code'=>'400017', 'amc_name'=> 'JM Financial Mutual Fund'],
            ['amc_code'=>'400018', 'amc_name'=> 'JPMorgan Mutual Fund'],
            ['amc_code'=>'400019', 'amc_name'=> 'Kotak Mahindra Mutual Fund'],
            ['amc_code'=>'400020', 'amc_name'=> 'LIC Mutual Fund'],
            ['amc_code'=>'400021', 'amc_name'=> 'Invesco Mutual Fund'],
            ['amc_code'=>'400023', 'amc_name'=> 'Principal Mutual Fund'],
            ['amc_code'=>'400024', 'amc_name'=> 'Quantum Mutual Fund'],
            ['amc_code'=>'400025', 'amc_name'=> 'Nippon India Mutual Fund'],
            ['amc_code'=>'400026', 'amc_name'=> 'Sahara Mutual Fund'],
            ['amc_code'=>'400027', 'amc_name'=> 'SBI Mutual Fund'],
            ['amc_code'=>'400028', 'amc_name'=> 'IDFC Mutual Fund'],
            ['amc_code'=>'400029', 'amc_name'=> 'Sundaram Mutual Fund'],
            ['amc_code'=>'400030', 'amc_name'=> 'Tata Mutual Fund'],
            ['amc_code'=>'400031', 'amc_name'=> 'Taurus Mutual Fund'],
            ['amc_code'=>'400032', 'amc_name'=> 'UTI Mutual Fund'],
            ['amc_code'=>'400033', 'amc_name'=> 'Mirae Asset Mutual Fund'],
            ['amc_code'=>'400034', 'amc_name'=> 'BOI AXA Mutual Fund'],
            ['amc_code'=>'400035', 'amc_name'=> 'Edelweiss Mutual Fund'],
            ['amc_code'=>'400037', 'amc_name'=> 'Goldman Sachs Mutual Fund'],
            ['amc_code'=>'400040', 'amc_name'=> 'Axis Mutual Fund'],
            ['amc_code'=>'400041', 'amc_name'=> 'Navi Mutual Fund'],
            ['amc_code'=>'400042', 'amc_name'=> 'Motilal Oswal Mutual Fund'],
            ['amc_code'=>'400043', 'amc_name'=> 'IDBI Mutual Fund'],
            ['amc_code'=>'400044', 'amc_name'=> 'PGIM India Mutual Fund'],
            ['amc_code'=>'400045', 'amc_name'=> 'Union Mutual Fund'],
            ['amc_code'=>'400047', 'amc_name'=> 'IIFL Mutual Fund'],
            ['amc_code'=>'400048', 'amc_name'=> 'Indiabulls Mutual Fund'],
            ['amc_code'=>'400049', 'amc_name'=> 'PPFAS Mutual Fund'],
            ['amc_code'=>'400050', 'amc_name'=> 'SREI Mutual Fund (IDF)'],
            ['amc_code'=>'400051', 'amc_name'=> 'IL&FS Mutual Fund (IDF)'],
            ['amc_code'=>'400052', 'amc_name'=> 'Shriram Mutual Fund'],
            ['amc_code'=>'400053', 'amc_name'=> 'IIFCL Mutual Fund'],
            ['amc_code'=>'400054', 'amc_name'=> 'Mahindra Manulife Mutual Fund'],
            ['amc_code'=>'400055', 'amc_name'=> 'YES Mutual Fund'],
            ['amc_code'=>'400056', 'amc_name'=> 'ITI Mutual Fund'],
            ['amc_code'=>'400057', 'amc_name'=> 'Trust Mutual Fund'],
            ['amc_code'=>'400058', 'amc_name'=> 'NJ Mutual Fund']
        ];
        DB::table('drm_amc_master')->insert($insert_data);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('drm_amc_master');
    }
}
