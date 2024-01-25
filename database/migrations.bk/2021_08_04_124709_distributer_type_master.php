<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DistributerTypeMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('distributer_type_master', function (Blueprint $table) {
            $table->id();
            $table->string('mfd_type',100)->comment('mfd type name');
            $table->tinyInteger('type')->comment('1=individual, 2= Non individual');
            $table->tinyInteger('doc_br')->comment('Board Resolution (BR)');
            $table->tinyInteger('doc_asl')->comment('Authorzied Signatory (ASL)');
            $table->dateTime('created_at')->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
        });

        // Insert some stuff
        $insert_data = [
            ['mfd_type'=>'Company/Body Corporate', 'type'=> 2,'doc_br'=>1,'doc_asl'=>1],
            ['mfd_type'=>'Individual', 'type'=> 1, 'doc_br'=>0,'doc_asl'=>0],
            ['mfd_type'=>'HUF', 'type'=> 2,'doc_br'=>0,'doc_asl'=>0],
            ['mfd_type'=>'LLP', 'type'=> 2,'doc_br'=>1,'doc_asl'=>1],
            ['mfd_type'=>'NBFC', 'type'=> 2, 'doc_br'=>1,'doc_asl'=>1],
            ['mfd_type'=>'Partnership Firm', 'type'=> 2, 'doc_br'=>1,'doc_asl'=>1],
            ['mfd_type'=>'Private/Foriegn Bank/Co-operative Bank', 'type'=> 2, 'doc_br'=>1,'doc_asl'=>1],
            ['mfd_type'=>'PSU Bank', 'type'=> 2, 'doc_br'=>1,'doc_asl'=>1],
            ['mfd_type'=>'Society', 'type'=> 2, 'doc_br'=>1,'doc_asl'=>1],
            ['mfd_type'=>'Sole Proprietorship', 'type'=> 1, 'doc_br'=>0,'doc_asl'=>0],
            ['mfd_type'=>'Trust', 'type'=> 2, 'doc_br'=>1,'doc_asl'=>1]
        ];
        DB::table('distributer_type_master')->insert($insert_data);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('distributer_type_master');
    }
}
