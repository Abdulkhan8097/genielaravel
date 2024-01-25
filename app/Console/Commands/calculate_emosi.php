<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Libraries\PhpMailer;
use DB;
class calculate_emosi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emosi:calculate {--bond_symbol= : Government security bond symbol} {--index_symbol= : NSE index symbol} {--from_date= : date FROM which BEER value needs to be calculated} {--to_date= : date TO which BEER value needs to be calculated} {--calculate_for_all_dates= : want to calculate values for available dates then send this parameter as 1 in that case --from_date & --to_date value will be ignored} {--enable_query_log= : want to enable query log then send this parameter as 1} {--send_email= : To send an email then send this parameter as 1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate EMOSI based on MEDIAN BEER & MEDIAN DEVIATION of past 1750 records';

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
        $flag_enable_query_log = false;
        if($this->option('enable_query_log') !== null && !empty($this->option('enable_query_log')) && ($this->option('enable_query_log') == 1)){
            $flag_enable_query_log = 1;
        }
        $flag_calculate_for_all_dates = 0;
        if($this->option('calculate_for_all_dates') !== null && !empty($this->option('calculate_for_all_dates')) && ($this->option('calculate_for_all_dates') == 1)){
            $flag_calculate_for_all_dates = 1;
        }
        $flag_to_send_email = 0;
        if($this->option('send_email') !== null && !empty($this->option('send_email')) && ($this->option('send_email') == 1)){
            $flag_to_send_email = 1;
        }

        $to_mail = getSettingsTableValue('EMOSI_DATA_EMAIL_NOTIFY_TO');
        $expload_to_mail = array();
        if(isset($to_mail) && !empty($to_mail)){
            $to_mail = explode(',',$to_mail);
            foreach($to_mail as $v){
                $expload_to_mail[] = array($v);
            }
        }
        //checking holidays and sending EMOSI not Calculated mail
            /*
        $check_holidays = DB::connection('invdb')->table('mf_holidays')->where(['date'=> $this->option('from_date'),'date'=>$this->option('to_date')])->first();
        if($flag_to_send_email && isset($check_holidays) && !empty($check_holidays)){
            // SENDING AN EMAIL WITH Holiday Message
            $research_mail = getSettingsTableValue('EMOSI_DATA_EMAIL_NOTIFY_TO_RESEARCH_TEAM');
            $research_mail = explode(',',$research_mail);
            foreach($research_mail as $research){
                $expload_to_mail[] = array($research);
            }
            if(isset($expload_to_mail) && is_array($expload_to_mail) && !empty($expload_to_mail)){
                $mailer = new PhpMailer();
                $params = [];
                $template = "SAMCOMF-GENERAL-NOTIFICATION";
                $params['templateName'] = $template;
                $params['channel']      = $template;
                $params['from_email']   = "alerts@samcomf.com";
                $params['to']           = $expload_to_mail;
                $params['merge_vars'] = array('MAIL_BODY' => 'EMOSI IS NOT CALCULATED DUE TO HOLIDAY');
                $params['subject'] = '['. date('d M Y H:i:s') . ']: EMOSI IS NOT CALCULATED DUE TO HOLIDAY';
                $email_send = $mailer->mandrill_send($params);
                return false;
            }
        }
        */
        $retrieved_data = \App\Models\EmosiHistoryModel::emosi_history_insert_records(array(
                                                                'bond_symbol' => $this->option('bond_symbol'),
                                                                'index_symbol' => $this->option('index_symbol'),
                                                                'from_date' => $this->option('from_date'),
                                                                'to_date' => $this->option('to_date'),
                                                                'calculate_for_all_dates' => $flag_calculate_for_all_dates,
                                                                'enable_query_log' => $flag_enable_query_log
                                                                )
                                                            );
        $arr_record_to_show_as_list = array();
        if(isset($retrieved_data) && is_array($retrieved_data) && !empty($retrieved_data) && isset($retrieved_data['response']) && !empty($retrieved_data['response']) && is_array($retrieved_data['response']) && $flag_to_send_email){
            $median_deviation_records = $retrieved_data['response']['median_deviation_records'];
            $median_beer_records = $retrieved_data['response']['median_beer_records'];
            foreach($retrieved_data['response']['arr_calculated_emosi_records'] as $key => $value){
                if(isset($median_beer_records[$value['record_date']]) && isset($median_deviation_records[$value['record_date']])){
                    $arr_record_to_show_as_list[] = array('record_date' => $value['record_date'],
                                                            'bond_symbol' =>$median_beer_records[$value['record_date']]['bond_symbol'],
                                                            'g_sec_yield' => $median_beer_records[$value['record_date']]['g_sec_yield'],
                                                            'index_symbol'=> $median_beer_records[$value['record_date']]['index_symbol'],
                                                            'pe' => $median_beer_records[$value['record_date']]['pe'],
                                                            'index_value' => $median_deviation_records[$value['record_date']]['index_value'],
                                                            'median_beer' =>$median_beer_records[$value['record_date']]['median_beer'],
                                                            'ma_1750' => $median_deviation_records[$value['record_date']]['ma_1750'],
                                                            'deviation_1750' => $median_deviation_records[$value['record_date']]['deviation_1750'],
                                                            'emosi_median_deviation_from_ma_1750' => $median_deviation_records[$value['record_date']]['emosi_median_deviation_from_ma_1750'],
                                                            'emosi_value' =>$value['emosi_value'],
                                                            'rounded_emosi' => $value['rounded_emosi']
                                                        );
                }

            }

            $table = "<table border=1 width='100%' style='border-collapse: collapse;' cellpadding='5'>";
            $table .= "<tr> <th colspan=14>CALCULATED EMOSI Records (Calculatd Date: ".date('d/m/Y', strtotime($this->option('from_date')))." - ".date('d/m/Y', strtotime($this->option('to_date'))).")</th></tr>";
            $table .= "<tr>";
            $table .= "<th>Record Date</th>";
            $table .= "<th>Bond Symbol</th>";
            $table .= "<th>G Sec Yield</th>";
            $table .= "<th>Index Symbol</th>";
            $table .= "<th>PE</th>";
            $table .= "<th>Index Value</th>";
            $table .= "<th>Median Beer</th>";
            $table .= "<th>Moving Average 1750</th>";
            $table .= "<th>Deviation 1750</th>";
            $table .= "<th>MOSI Median Deviation from MA 1750</th>";
            $table .= "<th>EMOSI Value</th>";
            $table .= "<th>ROUNDED Emosi</th>";
            $table .= "</tr>";


            foreach($arr_record_to_show_as_list as $_key => $_value){
                $table .= "<tr>";
                $table .= "<td>".$_value['record_date']."</td>";
                $table .= "<td>".$_value['bond_symbol']."</td>";
                $table .= "<td>".$_value['g_sec_yield']."</td>";
                $table .= "<td>".$_value['index_symbol']."</td>";
                $table .= "<td>".$_value['pe']."</td>";
                $table .= "<td>".$_value['index_value']."</td>";
                $table .= "<td>".$_value['median_beer']."</td>";
                $table .= "<td>".$_value['ma_1750']."</td>";
                $table .= "<td>".$_value['deviation_1750']."</td>";
                $table .= "<td>".$_value['emosi_median_deviation_from_ma_1750']."</td>";
                $table .= "<td>".$_value['emosi_value']."</td>";
                $table .= "<td>".$_value['rounded_emosi']."</td>";

                $table .= "</tr>";
            }
            $table .= "</table>";

            if($flag_to_send_email){
                if(in_array(date('H'), array(20)) !== FALSE && date('i') <= 30){
                    $research_mail = getSettingsTableValue('EMOSI_DATA_EMAIL_NOTIFY_TO_RESEARCH_TEAM');
                    $research_mail = explode(',',$research_mail);
                    foreach($research_mail as $research){
                        $expload_to_mail[] = array($research);
                    }
                }
            }

            $mailer = new PhpMailer();
            $params = [];
            $template = "SAMCOMF-GENERAL-NOTIFICATION";
            $params['templateName'] = $template;
            $params['channel']      = $template;
            $params['from_email']   = "alerts@samcomf.com";
            $params['to']           = $expload_to_mail;
            $params['merge_vars'] = array('MAIL_BODY' => $table);
            $params['subject'] = '['. date('d M Y H:i:s') . ']: CALCULATED EMOSI';
            $email_send = $mailer->mandrill_send($params);
        }
        // $this->line(print_r($retrieved_data, true));
        if(isset($retrieved_data['display_messages']) && is_array($retrieved_data['display_messages']) && count($retrieved_data['display_messages']) > 0){
            foreach($retrieved_data['display_messages'] as $log_message){
                $this->line($log_message);
                $this->newLine();
            }
            unset($log_message);
        }
        else{
            $this->line('The command execution is successful!');
        }
        unset($retrieved_data);
    }
}
