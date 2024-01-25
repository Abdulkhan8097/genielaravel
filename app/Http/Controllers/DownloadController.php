<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\EmosiNseIndexPePbDivYield;
use \App\Models\EmosiBondDataHistory;
use \App\Models\QuoteDataIndexHistory;
use App\Http\Controllers\EmosiData;
use \App\Models\EmosiHistoryModel;
use App\Libraries\PhpMailer;
use DB;

class DownloadController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    //Nse stock details download
    public function index(Request $request){
        if($request->isMethod('post')){
            if(isset($request['download_nse']) && !empty($request['download_nse'])){
                /**
                    Get NSE Free Float Market Cap Data, example URL:
                    a) Symbol 3MINDIA = https://www1.nseindia.com/live_market/dynaContent/live_watch/get_quote/GetQuote.jsp?symbol=3MINDIA&illiquid=0&smeFlag=0&itpFlag=0
                    b) Symbol RTNINDIA = https://www1.nseindia.com/live_market/dynaContent/live_watch/get_quote/GetQuote.jsp?symbol=RTNINDIA&illiquid=0&smeFlag=0&itpFlag=0
                    c) Symbol TRITURBINE = https://www1.nseindia.com/live_market/dynaContent/live_watch/get_quote/GetQuote.jsp?symbol=TRITURBINE&illiquid=0&smeFlag=0&itpFlag=0
                 */

                header('X-Accel-Buffering: no');
                header('Content-Encoding: none');
                $string_length = 4096;
                $string_repeat = str_repeat("d", (4096 * 1));
                $stock_details = array();
                $stock = array("360ONE","3MINDIA","ABB","ACC","AIAENG","APLAPOLLO","AUBANK","AARTIDRUGS","AAVAS","ABBOTINDIA","ADANIENT","ADANIGREEN","ADANIPORTS","ATGL","ADANITRANS","AWL","ABCAPITAL","ABFRL","ABSLAMC","AEGISCHEM","AETHER","AFFLE","AJANTPHARM","APLLTD","ALKEM","ALKYLAMINE","ALOKINDS","AMARAJABAT","AMBER","AMBUJACEM","ANGELONE","ANURAS","APOLLOHOSP","APOLLOTYRE","APTUS","ASAHIINDIA","ASHOKLEY","ASIANPAINT","ASTERDM","ASTRAZEN","ASTRAL","ATUL","AUROPHARMA","AVANTIFEED","DMART","AXISBANK","BASF","BEML","BSE","BAJAJ-AUTO","BAJAJELEC","BAJFINANCE","BAJAJFINSV","BAJAJHLDNG","BALAMINES","BALKRISIND","BALRAMCHIN","BANDHANBNK","BANKBARODA","BANKINDIA","MAHABANK","BATAINDIA","BAYERCROP","BERGEPAINT","BDL","BEL","BHARATFORG","BHEL","BPCL","BHARATRAS","BHARTIARTL","BIOCON","BIRLACORPN","BSOFT","BLUEDART","BLUESTARCO","BBTC","BORORENEW","BOSCHLTD","BRIGADE","BCG","BRITANNIA","MAPMYINDIA","CCL","CESC","CGPOWER","CRISIL","CSBBANK","CAMPUS","CANFINHOME","CANBK","CAPLIPOINT","CGCL","CARBORUNIV","CASTROLIND","CEATLTD","CENTRALBK","CDSL","CENTURYPLY","CENTURYTEX","CERA","CHALET","CHAMBLFERT","CHEMPLASTS","CHOLAHLDNG","CHOLAFIN","CIPLA","CUB","CLEAN","COALINDIA","COCHINSHIP","COFORGE","COLPAL","CAMS","CONCOR","COROMANDEL","CREDITACC","CROMPTON","CUMMINSIND","CYIENT","DCMSHRIRAM","DLF","DABUR","DALBHARAT","DEEPAKFERT","DEEPAKNTR","DELHIVERY","DELTACORP","DEVYANI","DHANI","DBL","DIVISLAB","DIXON","LALPATHLAB","DRREDDY","EIDPARRY","EIHOTEL","EPL","EASEMYTRIP","EDELWEISS","EICHERMOT","ELGIEQUIP","EMAMILTD","ENDURANCE","ENGINERSIN","EQUITASBNK","ESCORTS","EXIDEIND","FDC","NYKAA","FEDERALBNK","FACT","FINEORG","FINCABLES","FINPIPE","FSL","FORTIS","GRINFRA","GAIL","GMMPFAUDLR","GMRINFRA","GALAXYSURF","GARFIBRES","GICRE","GLAND","GLAXO","GLENMARK","GOCOLORS","GODFRYPHLP","GODREJAGRO","GODREJCP","GODREJIND","GODREJPROP","GRANULES","GRAPHITE","GRASIM","GESHIP","GREENPANEL","GRINDWELL","GUJALKALI","GAEL","FLUOROCHEM","GUJGASLTD","GNFC","GPPL","GSFC","GSPL","HEG","HCLTECH","HDFCAMC","HDFCBANK","HDFCLIFE","HFCL","HLEGLAS","HAPPSTMNDS","HATSUN","HAVELLS","HEROMOTOCO","HIKAL","HINDALCO","HGS","HAL","HINDCOPPER","HINDPETRO","HINDUNILVR","HINDZINC","POWERINDIA","HOMEFIRST","HONAUT","HUDCO","HDFC","ICICIBANK","ICICIGI","ICICIPRULI","ISEC","IDBI","IDFCFIRSTB","IDFC","IFBIND","IIFL","IRB","ITC","ITI","INDIACEM","IBULHSGFIN","IBREALEST","INDIAMART","INDIANB","IEX","INDHOTEL","IOC","IOB","IRCTC","IRFC","INDIGOPNTS","INDOCO","IGL","INDUSTOWER","INDUSINDBK","INFIBEAM","NAUKRI","INFY","INTELLECT","INDIGO","IPCALAB","JBCHEPHARM","JKCEMENT","JBMA","JKLAKSHMI","JKPAPER","JMFINANCIL","JSWENERGY","JSWSTEEL","JAMNAAUTO","JSL","JINDALSTEL","JUBLFOOD","JUBLINGREA","JUBLPHARMA","JUSTDIAL","JYOTHYLAB","KPRMILL","KEI","KNRCON","KPITTECH","KRBL","KAJARIACER","KALPATPOWR","KALYANKJIL","KANSAINER","KARURVYSYA","KEC","KOTAKBANK","KIMS","L&TFH","LTTS","LICHSGFIN","LTIM","LAXMIMACH","LT","LATENTVIEW","LAURUSLABS","LXCHEM","LEMONTREE","LICI","LINDEINDIA","LUPIN","LUXIND","MMTC","MOIL","MRF","MTARTECH","LODHA","MGL","M&MFIN","M&M","MAHINDCIE","MHRIL","MAHLIFE","MAHLOG","MANAPPURAM","MRPL","MARICO","MARUTI","MASTEK","MFSL","MAXHEALTH","MAZDOCK","MEDPLUS","METROBRAND","METROPOLIS","MSUMI","MOTILALOFS","MPHASIS","MCX","MUTHOOTFIN","NATCOPHARM","NBCC","NCC","NHPC","NLCINDIA","NOCIL","NTPC","NH","NATIONALUM","NAVINFLUOR","NAZARA","NESTLEIND","NETWORK18","NAM-INDIA","NUVOCO","OBEROIRLTY","ONGC","OIL","OLECTRA","PAYTM","OFSS","ORIENTELEC","POLICYBZR","PCBL","PIIND","PNBHOUSING","PNCINFRA","PVR","PAGEIND","PATANJALI","PERSISTENT","PETRONET","PFIZER","PHOENIXLTD","PIDILITIND","PPLPHARMA","POLYMED","POLYCAB","POLYPLEX","POONAWALLA","PFC","POWERGRID","PRAJIND","PRESTIGE","PRINCEPIPE","PRSMJOHNSN","PRIVISCL","PGHL","PGHH","PNB","QUESS","RBLBANK","RECLTD","RHIM","RITES","RADICO","RVNL","RAIN","RAINBOW","RAJESHEXPO","RALLIS","RCF","RATNAMANI","RTNINDIA","RAYMOND","REDINGTON","RELAXO","RELIANCE","RBA","ROSSARI","ROUTE","SBICARD","SBILIFE","SIS","SJVN","SKFINDIA","SRF","MOTHERSON","SANOFI","SAPPHIRE","SCHAEFFLER","SHARDACROP","SFL","SHILPAMED","SHOPERSTOP","SHREECEM","RENUKA","SHRIRAMFIN","SHYAMMETL","SIEMENS","SOBHA","SOLARINDS","SONACOMS","SONATSOFTW","STARHEALTH","SBIN","SAIL","SWSOLAR","STLTECH","SUDARSCHEM","SUMICHEM","SPARC","SUNPHARMA","SUNTV","SUNDARMFIN","SUNDRMFAST","SUNTECK","SUPRAJIT","SUPREMEIND","SUVENPHAR","SUZLON","SWANENERGY","SYMPHONY","SYNGENE","TCIEXP","TCNSBRANDS","TTKPRESTIG","TV18BRDCST","TVSMOTOR","TANLA","TATACHEM","TATACOFFEE","TATACOMM","TCS","TATACONSUM","TATAELXSI","TATAINVEST","TATAMTRDVR","TATAMOTORS","TATAPOWER","TATASTEEL","TTML","TEAMLEASE","TECHM","TEJASNET","NIACL","RAMCOCEM","THERMAX","THYROCARE","TIMKEN","TITAN","TORNTPHARM","TORNTPOWER","TCI","TRENT","TRIDENT","TRIVENI","TRITURBINE","TIINDIA","UCOBANK","UFLEX","UNOMINDA","UPL","UTIAMC","ULTRACEMCO","UNIONBANK","UBL","MCDOWELL-N","VGUARD","VMART","VIPIND","VAIBHAVGBL","VTL","VARROC","VBL","MANYAVAR","VEDL","VIJAYA","VINATIORGA","IDEA","VOLTAS","WELCORP","WELSPUNIND","WESTLIFE","WHIRLPOOL","WIPRO","WOCKPHARMA","YESBANK","ZFCVINDIA","ZEEL","ZENSARTECH","ZOMATO","ZYDUSLIFE","ZYDUSWELL","ECLERX");
                
                $total = count($stock);
                $sum_of_free_float = 0;
                $i = 0;
                echo json_encode(array('progress' => 0, 'count' => $i, 'total' => $total, 'dummy_data' => $string_repeat,'a'=>'a'));
                flush();
                ob_flush();
                foreach($stock as $key => $value){
                    $i++;
                    $status = exec(get_server_document_root(true).'/vendor/shell_scripts/python_script/nseenv/bin/python '. get_server_document_root(true) . '/vendor/shell_scripts/python_script/nse_final.py "'.$value.'"', $return_var, $exit_code);
                    $status = json_decode($status, true);
                    // // y($return_var, 'return_var');
                    // // y($exit_code, 'exit_code');
                    // x($status, 'status');
                    if(isset($status['cm_ffm']) && !empty($status['cm_ffm']) && floatval($status['cm_ffm']) > 0){
                        $sum_of_free_float += floatval($status['cm_ffm']);
                    }
                    $stock_details[] = $status;
                    if(($key+1) != count($stock)){
                        echo json_encode(array('progress' => (($i/$total)*100), 'count' => $i, 'total' => $total, 'dummy_data' => $string_repeat,'b'=>'b'));
                        flush();
                        ob_flush();
                    }
                }
                $export_csv_headers = array('symbol' => 'Symbol',
                                            'companyName' => 'Company Name',
                                            'cm_ffm' => 'Free Float',
                                            'weights' => 'Weights',
                                            'isin' => 'ISIN',
                                            'series' => 'Series',
                                            'secdate' => 'LastUpdated',
                                            );
                $output_arr = array();
                $output_arr[] = array_values($export_csv_headers);
                foreach($stock_details as $key => $value){
                    $row = array();
                    foreach($export_csv_headers as $field_name_key => $field_name_value){
                        $row[$field_name_key] = '';
                        switch($field_name_key){
                            case 'weights':
                                if(!empty($sum_of_free_float)){
                                    $row[$field_name_key] = ($value['cm_ffm']??0) / $sum_of_free_float;
                                }
                                else{
                                    $row[$field_name_key] = 0;
                                }
                                break;
                            default:
                                if(isset($value[$field_name_key])){
                                    $row[$field_name_key] = $value[$field_name_key];
                                }
                        }
                    }
                    $output_arr[] = $row;
                    unset($field_name_key, $field_name_value);
                }
                unset($key, $value, $csv_headers, $export_csv_headers);
                // x($output_arr);

                // Open a file in write mode ('w')
                $file_name = 'nse_master_data_'. date('Ymd_His').'.csv';
                $fp = fopen(get_server_document_root(true).'/storage/app/public/NSEdata/'. $file_name, 'w');
                // Loop through file pointer and a line
                foreach ($output_arr as $fields) {
                    fputcsv($fp, $fields);
                }

                fclose($fp);
                
                echo json_encode(array('progress' => 100, 'count' => $i, 'total' => $total, 'dummy_data' => $string_repeat,'c'=>'c', 'filename' => $file_name));
                flush();
                ob_flush();
            }
        }
        else{
            $data['log_response'] = 0;
            if($request->input('log') !== null){
                $data['log_response'] = 1;
            }
            return view("downloads/nse_stock_download", $data);
        }
    }

    public function nse_historical_indices($symbol='', $start_date='',$end_date=''){
        $err_flag = 0;
        $output_arr= array();
        $err_msg = array();
        if(empty($start_date) || strtotime($start_date) === FALSE){
            $err_flag = 1;
            $err_msg[] = "Start date is required!";
        }
        if(empty($end_date) || strtotime($end_date) === FALSE){
            $err_flag = 1;
            $err_msg[] = "End date is required!";
        }
        if(!isset($symbol) || empty($symbol)){
            $err_flag = 1;
            $err_msg[] = "Symbol is required!";
        }

        if($err_flag == 0){
            $script_directory = get_server_document_root(true);
            if(empty($script_directory)){
                $script_directory = getcwd();
            }
            $status = exec($script_directory.'/vendor/shell_scripts/python_script/nseenv/bin/python '. $script_directory . '/vendor/shell_scripts/python_script/nse_historical.py "'.urldecode($symbol).'" "'.$start_date.'" "'.$end_date.'"', $return_var, $exit_code);
            if($exit_code != 0){
                $err_flag = 1;
                $err_msg[] = "Error!.Please check with the paramerter passing in the URL";
            }
            else{
                $status = json_decode($status, true);
            }
        }

        if($err_flag == 0){
            if(isset($status) && is_array($status) && !empty($status)){
                foreach($status as $key => $value){
                    $flag_is_it_existing_record = false;
                    $convert_unix_timestamps_to_date = date('Y-m-d', strtotime($key));
                    $symbol = strtolower($symbol);
                    $symbol = str_replace(' ', '_', $symbol);
                    // DB::enableQueryLog();
                    $record = EmosiNseIndexPePbDivYield::where(['record_date'=> $convert_unix_timestamps_to_date,'symbol' =>$symbol, 'status' => 1])->first();
                    // x(DB::getQueryLog());
                    if(isset($record) && !empty($record)){
                        $value['P/E'] = round($value['P/E'], 4);
                        if($value['P/E'] == $record->pe){
                            $flag_is_it_existing_record = true;
                        }
                        else{
                            $updated = EmosiNseIndexPePbDivYield::where(['record_date'=> $convert_unix_timestamps_to_date,'symbol' =>$symbol])->update(array('status'=> 0));
                        }
                    }
                    if(!$flag_is_it_existing_record){
                        $created =  EmosiNseIndexPePbDivYield::create(['symbol' => $symbol, 'record_date' => $convert_unix_timestamps_to_date,'pe' => $value['P/E'],'pb' => $value['P/B'],'div_yield' => $value['Div Yield'],'status' => 1]);
                    }
                    unset($flag_is_it_existing_record);
                }
                $output_arr[] = array("message"=>"NSE Historical Data Inserted/Updated Successfully!","status"=>"success");
            }
        }
        else{
            $output_arr[] = array("message"=>$err_msg,"status"=>"error");
        }
        return $output_arr;
    }
    public function investiong_bonding_details($symbol='',$start_date='',$end_date=''){
        $err_flag = 0;
        $output_arr= array();
        $err_msg = array();
        if(empty($start_date) || strtotime($start_date) === FALSE){
            $err_flag = 1;
            $err_msg[] = "Start date is required!";
        }
        if(empty($end_date) || strtotime($end_date) === FALSE){
            $err_flag = 1;
            $err_msg[] = "End date is required!";
        }
        if(!isset($symbol) || empty($symbol)){
            $err_flag = 1;
            $err_msg[] = "Symbol is required!";
        }

        if($err_flag == 0){
            $start_date = str_replace('-', '/', $start_date);
            $start_date = date("d/m/Y",strtotime($start_date));
            $end_date = str_replace('-', '/', $end_date);
            $end_date = date("d/m/Y",strtotime($end_date));
            $script_directory = get_server_document_root(true);
            if(empty($script_directory)){
                $script_directory = getcwd();
            }
            $status = exec($script_directory.'/vendor/shell_scripts/python_script/nseenv/bin/python '. $script_directory . '/vendor/shell_scripts/python_script/investing_bond.py "'.urldecode($symbol).'" "'.$start_date.'" "'.$end_date.'"', $return_var, $exit_code);
            if($exit_code != 0){
                $err_flag = 1;
                $err_msg[] = "Error!.Please check with the paramerter passing in the URL";
            }
            else{
                $status = json_decode($status, true);
            }
        }
  
        if($err_flag == 0){
            if(isset($status) && is_array($status) && !empty($status) && isset($status['historical']) && !empty($status['historical'])){
                foreach($status['historical'] as $key => $value){
                $flag_is_it_existing_record = false;
                $convert_date = str_replace('/', '-', $value['date']);
                $convert_date = date("Y-m-d",strtotime($convert_date));
                $symbol = strtolower($symbol);
                $symbol = str_replace(' ', '_', $symbol);

                $record = EmosiBondDataHistory::where(['record_date'=> $convert_date,'symbol'=> $symbol, 'status' => 1])->first();
                if(isset($record) && !empty($record)){
                    $value['close'] = round($value['close'], 4);
                    if($value['close'] == $record->close){
                        $flag_is_it_existing_record = true;
                    }
                    else{
                        $updated = EmosiBondDataHistory::where(['record_date'=> $convert_date,'symbol'=> $symbol])->update(array('status'=> 0));
                    }
                }
                if(!$flag_is_it_existing_record){
                    $created =  EmosiBondDataHistory::create(['symbol' => $symbol, 'record_date' => $convert_date,'open' => $value['open'],'high' => $value['high'],'low' => $value['low'],'close' => $value['close'],'status' => 1]);
                }
                unset($flag_is_it_existing_record);
                }
                $output_arr[] = array("message"=>"Bond Historical Data Inserted/Updated Successfully!","status"=>"success");
            }
        }
        else{
            $output_arr[] = array("message"=>$err_msg,"status"=>"error");
        }
        return $output_arr;
    }
    public function nse_historical_index($symbol='', $start_date='',$end_date=''){
        $err_flag = 0;
        $output_arr= array();
        $err_msg = array();
        if(empty($start_date) || strtotime($start_date) === FALSE){
            $err_flag = 1;
            $err_msg[] = "Start date is required!";
        }
        if(empty($end_date) || strtotime($end_date) === FALSE){
            $err_flag = 1;
            $err_msg[] = "End date is required!";
        }
        if(!isset($symbol) || empty($symbol)){
            $err_flag = 1;
            $err_msg[] = "Symbol is required!";
        }

        if($err_flag == 0){
            $script_directory = get_server_document_root(true);
            if(empty($script_directory)){
                $script_directory = getcwd();
            }
            $status = exec($script_directory.'/vendor/shell_scripts/python_script/nseenv/bin/python '. $script_directory . '/vendor/shell_scripts/python_script/nse_historical_index.py "'.urldecode($symbol).'" "'.$start_date.'" "'.$end_date.'"', $return_var, $exit_code);
            if($exit_code != 0){
                $err_flag = 1;
                $err_msg[] = "Error!.Please check with the paramerter passing in the URL";
            }
            else{
                $status = json_decode($status, true);
            }
        }

        if($err_flag == 0){
            if(isset($status) && is_array($status) && !empty($status)){
                foreach($status as $key => $value){
                    $convert_unix_timestamps_to_date = date('Y-m-d', strtotime($key));
                    $symbol = strtolower($symbol);
                    $symbol = str_replace(' ', '_', $symbol);
                    if($symbol == "nifty_50"){
                        $symbol = -21;
                    }
                    // DB::enableQueryLog();
                    $record = QuoteDataIndexHistory::where(['index_date'=> $convert_unix_timestamps_to_date,'symbol' =>$symbol])->first();
                    // x(DB::getQueryLog());
                    if(isset($record) && !empty($record)){
                    $updated = QuoteDataIndexHistory::where(['index_date'=> $convert_unix_timestamps_to_date,'symbol' =>$symbol])->update(['symbol' => $symbol, 'index_date' => $convert_unix_timestamps_to_date,'open' => $value['Open'],'high' => $value['High'],'low' => $value['Low'],'close' => $value['Close']]);
                    }
                    else{
                        $created =  QuoteDataIndexHistory::create(['symbol' => $symbol, 'index_date' => $convert_unix_timestamps_to_date,'open' => $value['Open'],'high' => $value['High'],'low' => $value['Low'],'close' => $value['Close']]);
                    }
                }
                $output_arr[] = array("message"=>"NSE Historical Index Data Inserted/Updated Successfully!","status"=>"success");
            }
        }
        else{
            $output_arr[] = array("message"=>$err_msg,"status"=>"error");
        }
        return $output_arr;
    }
    public function get_equity_stock_indices($symbol=''){
        $err_flag = 0;
        $output_arr= array();
        $err_msg = array();

        if(!isset($symbol) || empty($symbol)){
            $err_flag = 1;
            $err_msg[] = "Symbol is required!";
        }

        if($err_flag == 0){
            $curlSession = curl_init();
            curl_setopt($curlSession, CURLOPT_URL, 'https://www.nseindia.com/api/allIndices');
            curl_setopt($curlSession, CURLOPT_BINARYTRANSFER, true);
            curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
        
            $jsonData = json_decode(curl_exec($curlSession),true);
            curl_close($curlSession);

            // x($jsonData);
            if(!isset($jsonData) || empty($jsonData)){
                $err_flag = 1;
                $err_msg[] = "Error!.No Data found";
            }
            else{
                $status = $jsonData;
            }
        }

        if($err_flag == 0){
            if(isset($status) && is_array($status) && !empty($status) && isset($status['data']) && !empty($status['data'])){
                    $nse_index_records = array_column($status['data'], NULL, 'index');
                    $nse_index_array = array();
                    
                    if(isset($nse_index_records[$symbol])){
                        $nse_index_array = $nse_index_records[$symbol];
                    }
                    if(isset($nse_index_array) && is_array($nse_index_array) && !empty($nse_index_array)){
                        $convert_unix_timestamps_to_date = date('Y-m-d', strtotime($status['timestamp']));
                        $symbol = strtolower($symbol);
                        $symbol = str_replace(' ', '_', $symbol);
                        $flag_is_it_existing_record = false;

                        //checking emosi_nse_index_pe_pb_divyield table record and inserting PE , PB , Values
                        // DB::enableQueryLog();
                        $Nse_pepb_record = EmosiNseIndexPePbDivYield::where(['record_date'=> $convert_unix_timestamps_to_date,'symbol' =>$symbol, 'status' => 1])->first();
                        // x(DB::getQueryLog());
                        if(isset($Nse_pepb_record) && !empty($Nse_pepb_record)){
                            $nse_index_array['pe'] = round($nse_index_array['pe'], 4);
                            if($nse_index_array['pe'] == $Nse_pepb_record->pe){
                                $flag_is_it_existing_record = true;
                            }
                            else{
                                $updated = EmosiNseIndexPePbDivYield::where(['record_date'=> $convert_unix_timestamps_to_date,'symbol' =>$symbol])->update(array('status'=> 0));
                            }
                        }
                        if(!$flag_is_it_existing_record){
                            $created =  EmosiNseIndexPePbDivYield::create(['symbol' => $symbol, 'record_date' => $convert_unix_timestamps_to_date,'pe' => $nse_index_array['pe'],'pb' => $nse_index_array['pb'],'div_yield' => $nse_index_array['dy'],'status' => 1]);
                        }
                        unset($flag_is_it_existing_record);
                        //end of NSE PE PB RECORD
                        if($symbol == "nifty_50"){
                            $symbol = -21;
                        }
                        // DB::enableQueryLog();
                        $record = QuoteDataIndexHistory::where(['index_date'=> $convert_unix_timestamps_to_date,'symbol' =>$symbol])->first();
                        // x(DB::getQueryLog());
                        if(isset($record) && !empty($record)){
                            $updated = QuoteDataIndexHistory::where(['index_date'=> $convert_unix_timestamps_to_date,'symbol' =>$symbol])->update(['symbol' => $symbol, 'index_date' => $convert_unix_timestamps_to_date,'open' => $nse_index_array['open'],'high' => $nse_index_array['high'],'low' => $nse_index_array['low'],'close' => $nse_index_array['last']]);
                        }
                        else{
                            $created =  QuoteDataIndexHistory::create(['symbol' => $symbol, 'index_date' => $convert_unix_timestamps_to_date,'open' => $nse_index_array['open'],'high' => $nse_index_array['high'],'low' => $nse_index_array['low'],'close' => $nse_index_array['last']]);
                        }
                        $output_arr[] = array("message"=>"NSE Historical Index Data and PE,PB,DY Value Inserted/Updated Successfully!","status"=>"success");
                    }
                    else{
                        $output_arr[] = array("message"=>"Error!.No Data found for the symbol","status"=>"Error");
                    }
            }
        }
        else{
            $output_arr[] = array("message"=>$err_msg,"status"=>"error");
        }
        return $output_arr;
    }
    public function save_emosi_value_details_to_kfin($symbol='',$start_date='',$end_date=''){
        $err_flag = 0;
        $output_arr= array();
        $err_msg = array();
        if(empty($start_date) || strtotime($start_date) === FALSE){
            $err_flag = 1;
            $err_msg[] = "Start date is required!";
        }
        if(empty($end_date) || strtotime($end_date) === FALSE){
            $err_flag = 1;
            $err_msg[] = "End date is required!";
        }
        if(!isset($symbol) || empty($symbol)){
            $err_flag = 1;
            $err_msg[] = "Symbol is required!";
        }

        if($err_flag == 0){
            $startTime = strtotime( $start_date );
            $endTime = strtotime( $end_date );
            $arr_emosi_record = array();
            // Loop between timestamps, 24 hours at a time
            for ( $i = $startTime; $i <= $endTime; $i = $i + 86400 ) {
              $thisDate = date( 'Y-m-d', $i ); // 2010-05-01, 2010-05-02, etc
              $record = EmosiHistoryModel::where(['record_date'=> $thisDate,'index_symbol'=>$symbol,'status' => 1])->first();

              if(isset($record) && !empty($record)){
                // y($thisDate,"thisDate");
                $emosi_controller_obj = new EmosiData;
                $insert_emosi_values = $emosi_controller_obj->GetSTPEmosiSave($thisDate,intval($record->rounded_emosi));

                if(isset($insert_emosi_values['err_flag']) && ($insert_emosi_values['err_flag'] == 1)){
                    $json_response = implode("<br>", ($insert_emosi_values['err_msg']??array()));
                }
                else{
                    $json_response = json_encode($insert_emosi_values['response']);
                }

                $arr_emosi_record[] = array('record_date' => $thisDate,'response' => $json_response,'emosi_value' => number_format($record->rounded_emosi, 0));
              }
              else{
                $arr_emosi_record[] = array('record_date' => $thisDate,'response' => 'No Record Found.','emosi_value' => 'NA');
              }   
            }

            $table = "<table border=1 width='100%' style='border-collapse: collapse;' cellpadding='5'>";
            $table .= "<tr> <th colspan=14>CALCULATED EMOSI Records (Calculatd Date: ".date('d/m/Y', strtotime($start_date))." - ".date('d/m/Y', strtotime($end_date)).")</th></tr>";
            $table .= "<tr>";
            $table .= "<th>Record Date</th>";
            $table .= "<th>Emosi Value</th>";
            $table .= "<th>Response</th>";
            $table .= "</tr>";


            foreach($arr_emosi_record as $_key => $_value){
                $table .= "<tr>";
                $table .= "<td>".$_value['record_date']."</td>";
                $table .= "<td>".$_value['emosi_value']."</td>";
                $table .= "<td>".$_value['response']."</td>";
                $table .= "</tr>";
            }
            $table .= "</table>";

            $to_mail = getSettingsTableValue('EMOSI_DATA_EMAIL_NOTIFY_TO');
            if(isset($to_mail) && !empty($to_mail)){
                $to_mail = explode(',',$to_mail);
                $expload_to_mail = array();
                foreach($to_mail as $v){
                    $expload_to_mail[] = array($v);
                }
            }

            $kfin_mail = getSettingsTableValue('EMOSI_DATA_EMAIL_NOTIFY_TO_KFIN');
            if(isset($kfin_mail) && !empty($kfin_mail)){
                $kfin_mail = explode(',',$kfin_mail);
                foreach($kfin_mail as $v){
                    $expload_to_mail[] = array($v);
                }
            }

            $save_emosi_kfin_production_api = getSettingsTableValue('SAVE_EMOSI_KFIN_PRODUCTION_API');
            $mailer = new PhpMailer();
            $params = [];
            $template = "SAMCOMF-GENERAL-NOTIFICATION";
            $params['templateName'] = $template;
            $params['channel']      = $template;
            $params['from_email']   = "alerts@samcomf.com";
            $params['to']           = $expload_to_mail;
            $params['merge_vars'] = array('MAIL_BODY' => $table);
            $params['subject'] = '['. date('d M Y H:i:s') . ']: CALCULATED EMOSI DATA SAVED TO KFIN'. (($save_emosi_kfin_production_api == 1)?' Production ':' UAT ') .'API';
            $email_send = $mailer->mandrill_send($params);
            $output_arr[] = array("message"=>"EMOSI value Inserted into ". (($save_emosi_kfin_production_api == 1)?" Production ":" UAT ") ."KFIN API and Mail Sent!","status"=>"success");
        }
        else{
            $output_arr[] = array("message"=>$err_msg,"status"=>"error");
        }
        return $output_arr;
    }

    public function Bond_Yield_History_data(){
        $err_flag = 0;
        $output_arr= array();
        $err_msg = array();
        if($err_flag == 0){
            $script_directory = get_server_document_root(true);
            if(empty($script_directory)){
                $script_directory = getcwd();
            }
            $status = exec($script_directory.'/vendor/shell_scripts/python_script/nseenv/bin/python '. $script_directory . '/vendor/shell_scripts/python_script/bond_history.py', $return_var, $exit_code);
            // y($return_var,'Return VAR');
            // y($exit_code,'exit_code');
            // x($status,'status');
            if($exit_code != 0){
                $err_flag = 1;
                $err_msg[] = "Error!.Please check with the paramerter passing in the URL";
            }
            else{
                $status = json_decode($status, true);
            }
        }
  
        if($err_flag == 0){
            if(isset($status) && is_array($status) && !empty($status)){
                foreach($status as $key => $value){
                    $flag_is_it_existing_record = false;
                    $convert_date = date("Y-m-d",strtotime($value['Date']));

                    $record = EmosiBondDataHistory::where(['record_date'=> $convert_date,'symbol'=> 'india_10y', 'status' => 1])->first();
                    if(isset($record) && !empty($record)){
                        $value['Price'] = round($value['Price'], 4);
                        if($value['Price'] == $record->close){
                            $flag_is_it_existing_record = true;
                        }
                        else{
                            $updated = EmosiBondDataHistory::where(['record_date'=> $convert_date,'symbol'=> 'india_10y'])->update(array('status'=> 0));
                        }
                    }
                    if(!$flag_is_it_existing_record){
                        $created =  EmosiBondDataHistory::create(['symbol' => 'india_10y', 'record_date' => $convert_date,'open' => $value['Open'],'high' => $value['High'],'low' => $value['Low'],'close' => $value['Price'],'status' => 1]);
                    }
                    unset($flag_is_it_existing_record);
                }
                $output_arr[] = array("message"=>"Bond Historical Data Inserted/Updated Successfully!","status"=>"success");
            }
        }
        else{
            $output_arr[] = array("message"=>$err_msg,"status"=>"error");
        }
        return $output_arr;
    }
}
