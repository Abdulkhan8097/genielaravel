<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKfintechMasterDistributorDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kfintech_master_distributor_details', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->string('abm_agent', 100)->nullable()->comment('agent code(ARN)');
            $table->integer('arn')->nullable()->comment('ARN code');
            $table->string('abm_broker',100)->nullable()->comment('abm broker code');
            $table->integer('abm_region')->nullable()->comment('abm_region');
            $table->integer('abm_chkdgt')->nullable()->comment('abm_chkdgt');
            $table->string('abm_name',100)->nullable()->comment('ARN Name');
            $table->text('abm_add1')->nullable()->comment('Address1');
            $table->text('abm_add2')->nullable()->comment('Address2');
            $table->text('abm_add3')->nullable()->comment('Address3');
            $table->text('abm_add4')->nullable()->comment('Address4');
            $table->integer('abm_pin')->nullable()->comment('Postal PIN');
            $table->string('abm_phone',50)->nullable()->comment('Resi.phone');
            $table->string('abm_fax',50)->nullable()->comment('fax');
            $table->string('abm_type',10)->nullable()->comment('abm_type');
            $table->string('abm_bank',100)->nullable()->comment('abm_bank');
            $table->string('abm_active',10)->nullable()->comment('abm_active');
            $table->string('abm_email',50)->nullable()->comment('Email id');
            $table->string('abm_contactnm',50)->nullable()->comment('abm_contactnm');
            $table->dateTime('abm_creatdt')->nullable()->comment('abm_creatdt');
            $table->dateTime('abm_entdt')->nullable()->comment('Entry Date');
            $table->string('abm_entby',100)->nullable()->comment('Entry By');
            $table->string('abm_cntperson',100)->nullable()->comment('Contact Name');
            $table->string('abm_email2',100)->nullable()->comment('Email id2');
            $table->string('abm_resphone',100)->nullable()->comment('Resi.phone');
            $table->string('abm_mobilephone',20)->nullable()->comment('Mobile Number');
            $table->string('abm_email3',100)->nullable()->comment('Email id3');
            $table->string('abm_bnkacno',50)->nullable()->comment('Bank Acno');
            $table->string('abm_actype',20)->nullable()->comment('Bank Acno type');
            $table->dateTime('abm_dob')->nullable()->comment('DOB');
            $table->string('abm_status',100)->nullable()->comment('Status');
            $table->string('abm_pangir',20)->nullable()->comment('PAN');
            $table->string('abm_amfi',20)->nullable()->comment('abm_amfi');
            $table->string('abm_circle',50)->nullable()->comment('abm_circle');
            $table->string('abm_bnkname',100)->nullable()->comment('Bank Name');
            $table->string('abm_bnkadd1',100)->nullable()->comment('Bank Add1');
            $table->string('abm_bnkadd2',100)->nullable()->comment('Bank Add2');
            $table->string('abm_bnkbranch',200)->nullable()->comment('Bank Branch');
            $table->integer('abm_bnkpin')->nullable()->comment('Bank Pin');
            $table->string('abm_category',100)->nullable()->comment('Agent category');
            $table->integer('abm_ihno')->nullable()->comment('abm_ihno');
            $table->string('abm_refno',100)->nullable()->comment('Reference Numner');
            $table->string('abm_ceoname',100)->nullable()->comment('CEO Name'); 
            $table->string('abm_ceomailid',100)->nullable()->comment('CEO Email id'); 
            $table->string('abm_cntmailid',100)->nullable()->comment('Contact person Email id');
            $table->string('abm_bnkcity',100)->nullable()->comment('Bank city'); 
            $table->string('abm_emailfeed1',100)->nullable()->comment('abm_emailfeed1'); 
            $table->string('abm_emailfeed2',100)->nullable()->comment('abm_emailfeed2'); 
            $table->string('abm_emailfeed3',100)->nullable()->comment('abm_emailfeed2'); 
            $table->string('abm_state',100)->nullable()->comment('state'); 
            $table->string('abm_bnkstate',100)->nullable()->comment('bank state'); 
            $table->string('abm_waradd1',100)->nullable()->comment('Warrant Address'); 
            $table->string('abm_waradd2',100)->nullable()->comment('Warrant address2'); 
            $table->string('abm_waradd3',100)->nullable()->comment('Warrant address 3'); 
            $table->string('abm_warcity',100)->nullable()->comment('Warrant city'); 
            $table->string('abm_warstate',100)->nullable()->comment('Warrant state'); 
            $table->integer('abm_warpin')->nullable()->comment('Warrant Pin'); 
            $table->string('abm_bnkadd3',100)->nullable()->comment('Bank address 3'); 
            $table->string('abm_oldagent',100)->nullable()->comment('Old agent code'); 
            $table->string('abm_piticode',100)->nullable()->comment(''); 
            $table->string('abm_empanelstat',100)->nullable()->comment('Empalnel Status'); 
            $table->dateTime('abm_regdate')->nullable()->comment('Reg date'); 
            $table->dateTime('abm_validitydate')->nullable()->comment('Validity date'); 
            $table->string('abm_dcb',10)->nullable()->comment('abm_dcb'); 
            $table->string('abm_branch',100)->nullable()->comment('abm_branch'); 
            $table->string('abm_Micr',100)->nullable()->comment('MICR Code'); 
            $table->string('abm_mapin',100)->nullable()->comment('abm_mapin'); 
            $table->string('abm_agentbranch',100)->nullable()->comment('Agent Branch'); 
            $table->dateTime('abm_mergedt')->nullable()->comment('Merged Date'); 
            $table->dateTime('abm_deathdate')->nullable()->comment('Death date'); 
            $table->dateTime('abm_deregdt')->nullable()->comment('abm_deregdt'); 
            $table->string('abm_deregreason',100)->nullable()->comment('abm_deregreason'); 
            $table->string('abm_nationality',100)->nullable()->comment('Nationality'); 
            $table->string('abm_newflag',100)->nullable()->comment('abm_newflag'); 
            $table->string('abm_newrefno',100)->nullable()->comment('abm_newrefno'); 
            $table->string('abm_zone',100)->nullable()->comment('Zone'); 
            $table->string('abm_Swiftcode',100)->nullable()->comment('Swiftcode'); 
            $table->string('abm_docrecdstat',100)->nullable()->comment('abm_docrecdstat'); 
            $table->string('abm_docremarks',100)->nullable()->comment('abm_docremarks'); 
            $table->string('abm_offusestat',100)->nullable()->comment('abm_offusestat'); 
            $table->string('abm_offuseremar',100)->nullable()->comment('abm_offuseremar'); 
            $table->string('abm_offpanstatus',100)->nullable()->comment('abm_offpanstatus'); 
            $table->string('ab_docuseflag',100)->nullable()->comment('ab_docuseflag'); 
            $table->string('ab_officeuseflag',100)->nullable()->comment('ab_officeuseflag'); 
            $table->string('ab_panflag',100)->nullable()->comment('ab_panflag'); 
            $table->dateTime('abm_empaneldate')->nullable()->comment('Empanel date'); 
            $table->string('abm_Designation',100)->nullable()->comment('Designation'); 
            $table->string('abm_modeofpay',100)->nullable()->comment('Mode of payment'); 
            $table->string('abm_vip',10)->nullable()->comment('VIP flag'); 
            $table->dateTime('abm_tranchargeeffectdt')->nullable()->comment('Transaction charges effected date');
            $table->string('abm_Tranchargeopt',100)->nullable()->comment('Transaction opt in flag'); 
            $table->string('abm_remarks',255)->nullable()->comment('Remarks'); 
            $table->string('abm_mobile3',100)->nullable()->comment('Mobile 3'); 
            $table->string('abm_mobile4',100)->nullable()->comment('Mobile4'); 
            $table->string('abm_mobile5',100)->nullable()->comment('Mobile 5'); 
            $table->string('abm_email4',100)->nullable()->comment('Email 4'); 
            $table->string('abm_email5',100)->nullable()->comment('Email 5'); 
            $table->string('abm_self_cer_provided',100)->nullable()->comment('Self certified Provided flag'); 
            $table->string('abm_CAMS_CDMS_BATCH_NO',100)->nullable()->comment('CAMS batch no'); 
            $table->string('abm_CDMSRemarks',255)->nullable()->comment('CDMS remarks'); 
            $table->string('abm_CDMSFlag',100)->nullable()->comment('CDMS FLAG'); 
            $table->dateTime('abm_UpdatedDate')->nullable()->comment('Updated date'); 
            $table->string('abm_country',100)->nullable()->comment('Country'); 
            $table->string('abm_fax2',100)->nullable()->comment('fax2'); 
            $table->string('abm_mobilephone2',100)->nullable()->comment('Mobile 2'); 
            $table->string('abm_cdms_tax_status',100)->nullable()->comment('CDMS tax status'); 
            $table->integer('cams_SL_NO')->nullable()->comment('CDMS slno'); 
            $table->string('cams_BROKER_COD',100)->nullable()->comment('CAMS Broker code'); 
            $table->string('cams_ARN_CODE',100)->nullable()->comment('CAMS ARN code'); 
            $table->string('cams_CONTACT_NA',100)->nullable()->comment('CAMS contact'); 
            $table->string('cams_BROKER_NAM',100)->nullable()->comment('cams Broker name'); 
            $table->string('cams_BROKER_CAT',100)->nullable()->comment('CAMS broker Category'); 
            $table->string('cams_EMP_STATUS',100)->nullable()->comment('CAMS broker Status'); 
            $table->string('cams_ADDRESS1',100)->nullable()->comment('CDMS address1'); 
            $table->string('cams_ADDRESS2',100)->nullable()->comment('CDMS address2'); 
            $table->string('cams_ADDRESS3',100)->nullable()->comment('CDMS address3'); 
            $table->string('cams_CITY',100)->nullable()->comment('CDMS city'); 
            $table->string('cams_PINCODE',100)->nullable()->comment('CDMS PINcode'); 
            $table->string('cams_STATE',100)->nullable()->comment('CDMS state'); 
            $table->string('cams_BANK_NAME',100)->nullable()->comment('CDMS bank Name'); 
            $table->string('cams_BANK_BRANC',100)->nullable()->comment('CDMS bank branch'); 
            $table->string('cams_BANK_CITY',100)->nullable()->comment('CDMS BANK city'); 
            $table->string('cams_AC_NO',100)->nullable()->comment('CDMS acno'); 
            $table->string('cams_AC_TYPE',100)->nullable()->comment('CDMS avtype'); 
            $table->string('cams_IFSC_CODE',100)->nullable()->comment('CDMS IFSC code'); 
            $table->integer('cams_ECS_NO')->nullable()->comment('CDMS ECS no'); 
            $table->string('cams_PAYOUT_MEC',100)->nullable()->comment('CDMS Payput flag'); 
            $table->string('cams_CHANNEL_FL',100)->nullable()->comment('CDMS channel flag'); 
            $table->string('cams_NOMI_RELA',100)->nullable()->comment('CDMS Nm-relation'); 
            $table->string('cams_NOMI_NAME',100)->nullable()->comment('CDMS NOM Name'); 
            $table->string('cams_NOMI_ADD1',100)->nullable()->comment('CDMS Nom Add1'); 
            $table->string('cams_NOMI_ADD2',100)->nullable()->comment('CDMS Nom Add2'); 
            $table->string('cams_NOMI_ADD3',100)->nullable()->comment('CDMS Nom Add3'); 
            $table->string('cams_NOMI_CITY',100)->nullable()->comment('CDMS Nom City'); 
            $table->string('cams_NOMI_PIN',100)->nullable()->comment('CDMS Nom PIN'); 
            $table->string('cams_SUSPEND_FL',100)->nullable()->comment('CDMS suspended Flag'); 
            $table->string('cams_UPF_ENTI',100)->nullable()->comment('CDMS UPF_ent'); 
            $table->string('cams_TF_ENTI',100)->nullable()->comment('CDMS TF_ent'); 
            $table->string('cams_BROKER_PAN',100)->nullable()->comment('CDMS Agent PAN'); 
            $table->string('cams_KYD_STATUS',100)->nullable()->comment('CDMS KYD status'); 
            $table->dateTime('cams_KYD_RECD_D')->nullable()->comment('CDMS KYD RECD'); 
            $table->dateTime('cams_ARN_VALID_')->nullable()->comment('CDMS ARN valid'); 
            $table->dateTime('cams_ARN_EXPIRY')->nullable()->comment('CDMS ARN expiry'); 
            $table->dateTime('cams_BROK_EXPI_')->nullable()->comment('CDMS Brok Expi'); 
            $table->dateTime('cams_BROK_CEAS_')->nullable()->comment(''); 
            $table->string('cams_BROK_CEAS0',100)->nullable()->comment(''); 
            $table->dateTime('cams_EMP_date')->nullable()->comment('CDMS EmP date'); 
            $table->string('cams_EMAIL1',100)->nullable()->comment('CDMS Email1'); 
            $table->string('cams_EMAIL2',100)->nullable()->comment('CDMS Email2'); 
            $table->string('cams_EMAIL3',100)->nullable()->comment('CDMS Email3'); 
            $table->string('cams_EMAIL4',100)->nullable()->comment('CDMS Email4'); 
            $table->string('cams_EMAIL5',100)->nullable()->comment('CDMS Emal5'); 
            $table->string('cams_PHONE_OFF',100)->nullable()->comment('CDMS Phone off'); 
            $table->string('cams_PHONE_RES',100)->nullable()->comment('CDMS Phone Res'); 
            $table->string('cams_FAX_OFF',100)->nullable()->comment('CDMS FAX'); 
            $table->string('cams_FAX_RES',100)->nullable()->comment('CDMS fax'); 
            $table->string('cams_MOBILE_NO',100)->nullable()->comment('CDMS mobile no'); 
            $table->string('cams_MOBILE_NO2',100)->nullable()->comment('CDMS mobile2'); 
            $table->string('cams_MOBILE_NO3',100)->nullable()->comment('CSM Mobile3'); 
            $table->string('cams_MOBILE_NO4',100)->nullable()->comment('CDMS mobile 4'); 
            $table->string('cams_MOBILE_NO5',100)->nullable()->comment('CDMS mobile 5'); 
            $table->dateTime('cams_BRKDOB')->nullable()->comment('CDMS DOB'); 
            $table->dateTime('cams_NOMDOB')->nullable()->comment('NOM DOB'); 
            $table->dateTime('abm_empaneldt')->nullable()->comment('Empanel date'); 
            $table->string('abm_website',100)->nullable()->comment('Website'); 
            $table->string('abm_education',100)->nullable()->comment('Education'); 
            $table->dateTime('abm_dtofrecpt')->nullable()->comment(''); 
            $table->string('abm_rmbranch',100)->nullable()->comment('RM branch'); 
            $table->string('abm_finprodoffr',100)->nullable()->comment(''); 
            $table->string('abm_gcode',100)->nullable()->comment(''); 
            $table->string('abm_GSTRegNo',100)->nullable()->comment('GST reg no'); 
            $table->string('abm_GSTApplNo',100)->nullable()->comment('GST Applno'); 
            $table->dateTime('abm_GSTRegDate')->nullable()->comment('GST reg date'); 
            $table->dateTime('abm_GSTRegEntDt')->nullable()->comment('GST reg ent dt'); 
            $table->string('abm_GSTRegStatus',100)->nullable()->comment('GST reg status'); 
            $table->string('abm_GSTProvRegNo',100)->nullable()->comment('GST reg. no'); 
            $table->string('abm_GSTStateCode',100)->nullable()->comment('GST state code'); 
            $table->string('abm_GSTPanNumber',100)->nullable()->comment('GST PAN'); 
            $table->dateTime('abm_empdate')->nullable()->comment(''); 
            $table->string('abm_ifsccode',100)->nullable()->comment('IFSC code'); 
            $table->dateTime('abm_ARNCncldt')->nullable()->comment(''); 
            $table->dateTime('abm_ARNCnclby')->nullable()->comment(''); 
            $table->string('abm_subcat',100)->nullable()->comment('Sub category'); 
            $table->string('abm_e_mail',100)->nullable()->comment('Email'); 
            $table->string('abm_web',100)->nullable()->comment('Website'); 
            $table->string('abm_e_mail2',50)->nullable()->comment('Email 2'); 
            $table->string('abm_Agencycode',50)->nullable()->comment('abm_Agencycode'); 
            $table->string('abm_UFCCode',50)->nullable()->comment('abm_UFCCode'); 
            $table->string('abm_UFCName',50)->nullable()->comment('abm_UFCName'); 
            $table->dateTime('abm_dot')->nullable()->comment(''); 
            $table->string('abm_role',100)->nullable()->comment(''); 
            $table->string('abm_district',100)->nullable()->comment('District'); 
            $table->string('abm_cardrefno',100)->nullable()->comment('abm_cardrefno'); 
            $table->string('abm_cardno',100)->nullable()->comment('abm_cardno'); 
            $table->string('abm_cardissdt',100)->nullable()->comment('abm_cardissdt'); 
            $table->string('abm_docMon',50)->nullable()->comment('abm_docMon'); 
            $table->string('abm_paymentmode',100)->nullable()->comment('Payment Mode'); 
            $table->text('abm_bankadd3')->nullable()->comment('bank address 3'); 
            $table->text('abm_rmcode')->nullable()->comment('RM code'); 
            $table->string('abm_NRIactype',50)->nullable()->comment('NRI account type'); 
            $table->string('abm_NRIbnkacno',50)->nullable()->comment('NRI bank Ac no'); 
            $table->string('abm_NRIbnkname',100)->nullable()->comment('NRI bank Name'); 
            $table->text('abm_NRIbnkadd1')->nullable()->comment('NRI address1'); 
            $table->text('abm_NRIbnkadd2')->nullable()->comment('NRI address2'); 
            $table->text('abm_NRIbnkadd3')->nullable()->comment('NRI addres3'); 
            $table->string('abm_NRIbnkcity',100)->nullable()->comment('NRI bank city'); 
            $table->string('abm_NRIbnkpin',20)->nullable()->comment('NRI bank PIN'); 
            $table->string('abm_NRIbnkbranch',100)->nullable()->comment('NRI bank branch'); 
            $table->string('abm_InwardDate',100)->nullable()->comment('Inward date'); 
            $table->string('abm_nribankpaymode',100)->nullable()->comment('NRI bank pay mode'); 
            $table->string('abm_nricountry',100)->nullable()->comment('NRI country'); 
            $table->dateTime('abm_dor')->nullable()->comment(''); 
            $table->dateTime('abm_orgentdt')->nullable()->comment('Entdate'); 
            $table->string('abm_ufcappntd',100)->nullable()->comment(''); 
            $table->string('abm_changeprofflag',100)->nullable()->comment('Change Profile flag'); 
            $table->tinyInteger('status')->default(1)->nullable()->comment('Status: 0=Inactive, 1=Active');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('abm_agent');
            $table->index('arn');
            $table->index('abm_email');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kfintech_master_distributor_details');
    }
}
