#!/bin/bash
############################################################
# Help                                                     #
############################################################
PATH=/opt/homebrew/opt/php@7.2/sbin:/opt/homebrew/opt/php@7.2/bin:/opt/homebrew/opt/php@7.2/sbin:/opt/homebrew/opt/php@7.2/bin:/opt/homebrew/bin:/opt/homebrew/sbin:/usr/local/bin:/System/Cryptexes/App/usr/bin:/usr/bin:/bin:/usr/sbin:/sbin:/var/run/com.apple.security.cryptexd/codex.system/bootstrap/usr/local/bin:/var/run/com.apple.security.cryptexd/codex.system/bootstrap/usr/bin:/var/run/com.apple.security.cryptexd/codex.system/bootstrap/usr/appleinternal/bin:$PATH
Help()
{
   # Display Help
   echo "Script helps to import CSV file."
   echo
   echo "Syntax: scriptTemplate [-i|h|v]"
   echo "options:"
   echo "i     Imports csv file data into MySQL table."
   echo "            Four parameters are required for execution of a script"
   echo "            First parameter is CSV file path"
   echo "            Second parameter is MySQL table name in which data will get stored"
   echo "            Third parameter is query string which will be concatenated with command LOAD DATA LOCAL INFILE"
   echo "            Fourth parameter have values like YES/NO."
   echo "            YES means directly insert the data into main table."
   echo "            NO means insert the data into backup table & from that table perform INSERT/UPDATE operation in main table."
   echo "h     Print this Help."
   echo
}
############################################################
############################################################
# Main program                                             #
############################################################
############################################################

############################################################
# Process the input options. Add options as needed.        #
############################################################
# Get the options
while getopts ":hi:" option; do
   case $option in
      h) # display Help
         Help
         exit;;
      i) # execute a script and import data
         # checking number of arguments equals to 3 or not
         if [ "$#" -lt "4" ]; then
            echo "4 input parameters are required to execute the script"
            exit 1
         fi

         # getting directory path of currently executing script
         SCRIPT_DIRECTORY_PATH="$(dirname "$0")"
         # getting 1st parent of found directory path
         ENV_DIRECTORY_PATH="$(dirname ${SCRIPT_DIRECTORY_PATH})"
         # getting 2nd parent of found directory path
         ENV_DIRECTORY_PATH="$(dirname ${ENV_DIRECTORY_PATH})"
         # echo "SCRIPT_DIRECTORY_PATH=$SCRIPT_DIRECTORY_PATH"
         # echo "ENV_DIRECTORY_PATH=$ENV_DIRECTORY_PATH"

         ENV_FILE_PATH="${ENV_DIRECTORY_PATH}/.env"
         # echo "ENV_FILE_PATH=$ENV_FILE_PATH"

         CURRENT_MYSQL_FORMAT_DATE="$(date '+%Y-%m-%d')"

         # MySQL config parameters. GREP command helps to find a particular text from the given file and -m1 stops after 1st matching of text
         MYSQL_USERNAME="$(grep -m1 "DB_USERNAME" ${ENV_FILE_PATH})"
         # replacing the unnecessary word and just retrieving actual DB username
         MYSQL_USERNAME=$(echo "$MYSQL_USERNAME" | sed "s/DB_USERNAME=//")

         MYSQL_PASSWORD="$(grep -m1 "DB_PASSWORD" ${ENV_FILE_PATH})"
         # replacing the unnecessary word and just retrieving actual DB password
         MYSQL_PASSWORD=$(echo "$MYSQL_PASSWORD" | sed "s/DB_PASSWORD=//")

         MYSQL_DBNAME="$(grep -m1 "DB_DATABASE" ${ENV_FILE_PATH})"
         # replacing the unnecessary word and just retrieving actual DB name
         MYSQL_DBNAME=$(echo "$MYSQL_DBNAME" | sed "s/DB_DATABASE=//")
         
         MYSQL_DB_HOST="$(grep -m1 "DB_HOST" ${ENV_FILE_PATH})"
         # replacing the unnecessary word and just retrieving actual DB host
         MYSQL_DB_HOST=$(echo "$MYSQL_DB_HOST" | sed "s/DB_HOST=//")

         # echo "MYSQL_USERNAME=$MYSQL_USERNAME"
         # echo "MYSQL_PASSWORD=$MYSQL_PASSWORD"
         # echo "MYSQL_DBNAME=$MYSQL_DBNAME"
         # echo "MYSQL_DB_HOST=$MYSQL_DB_HOST"
         
         # Variable to identify how many records got processed
         PROCESSED_RECORDS_COUNT="0"

         # Get current date
         CURRENT_DATETIME="$(date '+%Y-%m-%d %H:%M:%S')"

         # 1st argument will be csv file full path
         FILE_PATH=$OPTARG
         # 2nd argument will be mysql table name
         TABLE_NAME=$2
         # 3rd argument will be query string
         QUERY_STRING=$3
         # 4th argument will decide whether doing import for 1st time or not
         FIRST_RUN=$4
         # 5th argument used for passing any extra parameters to this script
         EXTRA_PARAMETER=$5

         echo "$(date '+%Y-%m-%d %H:%M:%S')\tCURRENT_DATETIME=$CURRENT_DATETIME"
         echo "$(date '+%Y-%m-%d %H:%M:%S')\tFILE_PATH: $FILE_PATH"
         echo "$(date '+%Y-%m-%d %H:%M:%S')\tTABLE_NAME: $TABLE_NAME"
         echo "$(date '+%Y-%m-%d %H:%M:%S')\tQUERY_STRING: $QUERY_STRING"
         echo "$(date '+%Y-%m-%d %H:%M:%S')\tFIRST_RUN: $FIRST_RUN"

         if [ "$FIRST_RUN" = "yes" ]; then
            # after receiving the parameters trying to import the file using mysql LOAD DATA query
            echo "$(date '+%Y-%m-%d %H:%M:%S')\tafter receiving the parameters trying to import the file using mysql LOAD DATA query"
            MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "LOAD DATA LOCAL INFILE '${FILE_PATH}' INTO TABLE ${TABLE_NAME} FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY '\n' ${QUERY_STRING};"
         else
            # truncating the table data before inserting
            echo "$(date '+%Y-%m-%d %H:%M:%S')\ttruncating the table data before inserting"
            MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "TRUNCATE TABLE ${TABLE_NAME}_backup;"

            # after receiving the parameters trying to import the file using mysql LOAD DATA query into backup table
            echo "$(date '+%Y-%m-%d %H:%M:%S')\tafter receiving the parameters trying to import the file using mysql LOAD DATA query into backup table"
            MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "LOAD DATA LOCAL INFILE '${FILE_PATH}' INTO TABLE ${TABLE_NAME}_backup FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY '\n' ${QUERY_STRING};"

            if [ "$TABLE_NAME" = "arn_alternate_details" ]; then
               # updating the records which were already present in both main and backup table
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating the records which were already present in both main and backup table"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE arn_alternate_details_backup AS a INNER JOIN arn_alternate_details AS b ON (a.arn = b.arn) SET b.alternate_email_1 = a.alternate_email_1, b.alternate_email_2 = a.alternate_email_2, b.alternate_email_3 = a.alternate_email_3, b.alternate_email_4 = a.alternate_email_4, b.alternate_email_5 = a.alternate_email_5, b.alternate_mobile_1 = a.alternate_mobile_1, b.alternate_mobile_2 = a.alternate_mobile_2, b.alternate_mobile_3 = a.alternate_mobile_3, b.alternate_mobile_4 = a.alternate_mobile_4, b.alternate_mobile_5 = a.alternate_mobile_5;"

               # inserting the new records which are not present in main but available in backup table
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tinserting the new records which are not present in main but available in backup table"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "INSERT INTO arn_alternate_details(arn, alternate_email_1, alternate_email_2, alternate_email_3, alternate_email_4, alternate_email_5, alternate_mobile_1, alternate_mobile_2, alternate_mobile_3, alternate_mobile_4, alternate_mobile_5, status, created_at) SELECT a.arn, a.alternate_email_1, a.alternate_email_2, a.alternate_email_3, a.alternate_email_4, a.alternate_email_5, a.alternate_mobile_1, a.alternate_mobile_2, a.alternate_mobile_3, a.alternate_mobile_4, a.alternate_mobile_5, 1 AS status, NOW() AS created_at FROM arn_alternate_details_backup AS a LEFT JOIN arn_alternate_details AS b ON (a.arn = b.arn) WHERE b.ARN IS NULL;"

               # updating alternate email and mobile details against ARN from MySQL table: arn_alternate_details
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating alternate email and mobile details against ARN from MySQL table: arn_alternate_details"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE drm_distributor_master AS a INNER JOIN arn_alternate_details AS b ON (a.ARN = b.arn) SET a.alternate_mobile_1 = b.alternate_mobile_1, a.alternate_mobile_2 = b.alternate_mobile_2, a.alternate_mobile_3 = b.alternate_mobile_3, a.alternate_mobile_4 = b.alternate_mobile_4, a.alternate_mobile_5 = b.alternate_mobile_5, a.alternate_email_1 = b.alternate_email_1, a.alternate_email_2 = b.alternate_email_2, a.alternate_email_3 = b.alternate_email_3, a.alternate_email_4 = b.alternate_email_4, a.alternate_email_5 = b.alternate_email_5, a.record_last_available_in_amfi = a.record_last_available_in_amfi WHERE 1;"
            elif [ "$TABLE_NAME" = "drm_distributor_master" ]; then
               # finding ARN list whose details are present in main table but not present in backup table, marking those ARN record status as INACTIVE
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tfinding ARN list whose details are present in main table but not present in backup table, marking those ARN record status as INACTIVE"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE drm_distributor_master AS a LEFT JOIN drm_distributor_master_backup AS b ON (a.ARN = b.ARN) SET a.status = 0, a.record_last_available_in_amfi = a.record_last_available_in_amfi WHERE b.ARN IS NULL;"

               # updating the records which were already present in both main and backup table
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating the records which were already present in both main and backup table"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE drm_distributor_master_backup AS a INNER JOIN drm_distributor_master AS b ON (a.ARN = b.ARN) SET b.arn_holders_name = a.arn_holders_name, b.arn_address = a.arn_address, b.arn_pincode = a.arn_pincode, b.arn_email = a.arn_email, b.arn_city = a.arn_city, b.arn_telephone_r = a.arn_telephone_r, b.arn_telephone_o = a.arn_telephone_o, b.arn_valid_from = a.arn_valid_from, b.arn_valid_till = a.arn_valid_till, b.arn_kyd_compliant = a.arn_kyd_compliant, b.arn_euin = a.arn_euin, b.status = 1, b.record_last_available_in_amfi = b.record_last_available_in_amfi;"

               # inserting the new records which are not present in main but available in backup table
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tinserting the new records which are not present in main but available in backup table"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "INSERT INTO drm_distributor_master(ARN, arn_holders_name, arn_address, arn_pincode, arn_email, arn_city, arn_telephone_r, arn_telephone_o, arn_valid_from, arn_valid_till, arn_kyd_compliant, arn_euin, status, created_at) SELECT a.ARN, a.arn_holders_name, a.arn_address, a.arn_pincode, a.arn_email, a.arn_city, a.arn_telephone_r, a.arn_telephone_o, a.arn_valid_from, a.arn_valid_till, a.arn_kyd_compliant, a.arn_euin, 1 AS status, NOW() AS created_at FROM drm_distributor_master_backup AS a LEFT JOIN drm_distributor_master AS b ON (a.ARN = b.ARN) WHERE b.ARN IS NULL;"
            elif [ "$TABLE_NAME" = "drm_partners_rankmf_bdm_list" ]; then
               # updating the records which were already present in both main and backup table
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating the records which were already present in both main and backup table"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE drm_partners_rankmf_bdm_list_backup AS a INNER JOIN drm_partners_rankmf_bdm_list AS b ON (a.login_master_sr_id = b.login_master_sr_id AND a.email = b.email) SET b.pincode = a.pincode, b.city = a.city, b.employee_code = a.employee_code, b.branch_manager = a.branch_manager, b.area_manager = a.area_manager, b.circle_manager = a.circle_manager, b.national_manager = a.national_manager, b.name = a.name, b.dob = a.dob, b.mobile = a.mobile, b.pan = a.pan, b.address = a.address, b.unit_code = a.unit_code, b.branch_code = a.branch_code, b.title = a.title, b.marital_status = a.marital_status;"

               # inserting the new records which are not present in main but available in backup table
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tinserting the new records which are not present in main but available in backup table"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "INSERT INTO drm_partners_rankmf_bdm_list(login_master_sr_id, role_name, pincode, city, employee_code, branch_manager, area_manager, circle_manager, national_manager, name, dob, email, mobile, pan, address, unit_code, branch_code, title, marital_status, status, created_at) SELECT a.login_master_sr_id, a.role_name, a.pincode, a.city, a.employee_code, a.branch_manager, a.area_manager, a.circle_manager, a.national_manager, a.name, a.dob, a.email, a.mobile, a.pan, a.address, a.unit_code, a.branch_code, a.title, a.marital_status, 1 AS status, NOW() AS created_at FROM drm_partners_rankmf_bdm_list_backup AS a LEFT JOIN drm_partners_rankmf_bdm_list AS b ON (a.login_master_sr_id = b.login_master_sr_id AND a.email = b.email) WHERE b.login_master_sr_id IS NULL AND b.email IS NULL;"

               # updating password field from MySQL table: drm_partners_rankmf_bdm_list with details of partners rankmf user(s) plain text password available in MySQL table: drm_partners_rankmf_bdm_list_backup
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating password field from MySQL table: drm_partners_rankmf_bdm_list with details of partners rankmf user(s) plain text password available in MySQL table: drm_partners_rankmf_bdm_list_backup"
               php artisan encryptedpasswordforbdm:update

               # inserting the rankmf users/bdm records which are not present in MySQL users table but available in table drm_partners_rankmf_bdm_list
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tinserting the rankmf users/bdm records which are not present in MySQL users table but available in table drm_partners_rankmf_bdm_list"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "INSERT INTO users(name, email, password, is_drm_user, updated_at) SELECT a.name, a.email, a.password AS password, 1 AS is_drm_user, NOW() AS updated_at FROM drm_partners_rankmf_bdm_list AS a LEFT JOIN users AS b ON (a.email = b.email AND b.is_drm_user = 1) WHERE b.email IS NULL AND b.is_drm_user IS NULL;"

               # inserting the users records which are not present in MySQL users_details table but available in table users
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tinserting the users records which are not present in MySQL users_details table but available in table users"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "INSERT INTO users_details(user_id, employee_code, designation, mobile_number, city, status, created_at) SELECT b.id AS user_id, a.employee_code, '' AS designation, a.mobile, a.city, 1 AS status, NOW() AS created_at FROM drm_partners_rankmf_bdm_list AS a INNER JOIN users AS b ON (a.email = b.email AND b.is_drm_user = 1) LEFT JOIN users_details AS c ON (b.id = c.user_id) WHERE c.user_id IS NULL;"

               # updating the field direct_relationship_user_id from MySQL table: drm_distributor_master for those ARN holders who have BDM assigned in RANKMF and that BDM entry is available in MySQL table: users
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating the field direct_relationship_user_id from MySQL table: drm_distributor_master for those ARN holders who have BDM assigned in RANKMF and that BDM entry is available in MySQL table: users"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE drm_rankmf_partner_registration AS a INNER JOIN drm_partners_rankmf_bdm_list AS b ON (a.unit_counsellor = b.login_master_sr_id) INNER JOIN users AS c ON (b.email = c.email AND c.is_drm_user = 1) INNER JOIN drm_distributor_master AS d ON (a.ARN = d.ARN) SET d.direct_relationship_user_id = c.id WHERE (d.direct_relationship_user_id IS NULL OR d.direct_relationship_user_id = '');"
            elif [ "$TABLE_NAME" = "drm_partners_rankmf_current_aum" ]; then
               # updating the records which were already present in both main and backup table
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating the records which were already present in both main and backup table"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE drm_partners_rankmf_current_aum_backup AS a INNER JOIN drm_partners_rankmf_current_aum AS b ON (a.client_id = b.client_id AND a.broker_id = b.broker_id) SET b.total_aum = a.total_aum, b.equity_aum = a.equity_aum, b.debt_aum = a.debt_aum, b.hybrid_aum = a.hybrid_aum, b.others_aum = a.others_aum, b.commodity_aum = a.commodity_aum;"

               # inserting the new records which are not present in main but available in backup table
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tinserting the new records which are not present in main but available in backup table"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "INSERT INTO drm_partners_rankmf_current_aum(client_id, broker_id, total_aum, equity_aum, debt_aum, hybrid_aum, others_aum, commodity_aum, status, created_at) SELECT a.client_id, a.broker_id, a.total_aum, a.equity_aum, a.debt_aum, a.hybrid_aum, a.others_aum, a.commodity_aum, 1 AS status, NOW() AS created_at FROM drm_partners_rankmf_current_aum_backup AS a LEFT JOIN drm_partners_rankmf_current_aum AS b ON (a.client_id = b.client_id AND a.broker_id = b.broker_id) WHERE b.client_id IS NULL AND b.broker_id IS NULL;"

               # updating RANKMF AUM data against ARN from MySQL table: drm_partners_rankmf_current_aum
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating RANKMF AUM data against ARN from MySQL table: drm_partners_rankmf_current_aum"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE drm_distributor_master AS a INNER JOIN (SELECT c.ARN, SUM(IFNULL(b.total_aum, 0)) AS total_aum, SUM(IFNULL(b.equity_aum, 0)) AS equity_aum, SUM(IFNULL(b.hybrid_aum, 0)) AS hybrid_aum FROM drm_partners_rankmf_current_aum AS b INNER JOIN drm_rankmf_partner_registration AS c ON (b.broker_id = c.partner_code) WHERE c.ARN IS NOT NULL AND c.ARN != '' GROUP BY c.ARN) AS b ON (a.ARN = b.ARN) SET a.rankmf_partner_aum = b.total_aum, a.rankmf_partner_equity_and_hybrid_aum = (b.equity_aum + b.hybrid_aum), a.is_partner_active_on_rankmf = IF(b.total_aum > 0, 1, 0), a.total_aum = (IFNULL(a.samcomf_partner_aum, 0) + b.total_aum), a.total_equity_and_hybrid_aum = (IFNULL(a.samcomf_partner_equity_and_hybrid_aum, 0) + b.equity_aum + b.hybrid_aum), a.record_last_available_in_amfi = a.record_last_available_in_amfi WHERE 1;"
            elif [ "$TABLE_NAME" = "drm_project_focus_amc_wise_details" ]; then
               # updating the records which were already present in both main and backup table
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating the records which were already present in both main and backup table"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE drm_project_focus_amc_wise_details_backup AS a INNER JOIN drm_project_focus_amc_wise_details AS b ON (a.ARN = b.ARN AND a.amc_name = b.amc_name AND a.reported_year = b.reported_year) SET b.amc_code = a.amc_code, b.total_commission_expenses_paid = a.total_commission_expenses_paid, b.gross_inflows = a.gross_inflows, b.net_inflows = a.net_inflows, b.avg_aum_for_last_reported_year = a.avg_aum_for_last_reported_year, b.closing_aum_for_last_financial_year = a.closing_aum_for_last_financial_year, b.effective_yield = IF(a.avg_aum_for_last_reported_year != 0, ((a.total_commission_expenses_paid / a.avg_aum_for_last_reported_year) * 100), 0), b.nature_of_aum = a.nature_of_aum;"

               # inserting the new records which are not present in main but available in backup table
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tinserting the new records which are not present in main but available in backup table"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "INSERT INTO drm_project_focus_amc_wise_details(ARN, amc_name, amc_code, total_commission_expenses_paid, gross_inflows, net_inflows, avg_aum_for_last_reported_year, closing_aum_for_last_financial_year, effective_yield, nature_of_aum, reported_year, status, created_at) SELECT a.ARN, a.amc_name, a.amc_code, a.total_commission_expenses_paid, a.gross_inflows, a.net_inflows, a.avg_aum_for_last_reported_year, a.closing_aum_for_last_financial_year, IF(a.avg_aum_for_last_reported_year != 0, ((a.total_commission_expenses_paid / a.avg_aum_for_last_reported_year) * 100), 0) AS effective_yield, a.nature_of_aum, a.reported_year, 1 AS status, NOW() AS created_at FROM drm_project_focus_amc_wise_details_backup AS a LEFT JOIN drm_project_focus_amc_wise_details AS b ON (a.ARN = b.ARN AND a.amc_name = b.amc_name AND a.reported_year = b.reported_year) WHERE b.ARN IS NULL;"

               # updating field nature_of_aum from main table which is having effective_yield value less than equal to 0.4 as DEBT
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating field nature_of_aum from main table which is having effective_yield value less than equal to 0.4 as DEBT"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE drm_project_focus_amc_wise_details SET nature_of_aum = 'Debt', created_at = created_at, updated_at = updated_at WHERE nature_of_aum IS NULL AND effective_yield <= 0.4;"

               # updating field nature_of_aum from main table which is having effective_yield value greater than 0.4 and less than 1.0 as Hybrid DEBT & Equity
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating field nature_of_aum from main table which is having effective_yield value greater than 0.4 and less than 1.0 as Hybrid DEBT & Equity"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE drm_project_focus_amc_wise_details SET nature_of_aum = 'Hybrid Debt & Equity', created_at = created_at, updated_at = updated_at WHERE nature_of_aum IS NULL AND effective_yield > 0.4 AND effective_yield <= 1;"

               # updating field nature_of_aum from main table which is having effective_yield value greater than 1.0 as Equity
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating field nature_of_aum from main table which is having effective_yield value greater than 1.0 as Equity"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE drm_project_focus_amc_wise_details SET nature_of_aum = 'Equity', created_at = created_at, updated_at = updated_at WHERE nature_of_aum IS NULL AND effective_yield > 1;"
            elif [ "$TABLE_NAME" = "drm_rankmf_partner_registration" ]; then
               # updating the records which were already present in both main and backup table
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating the records which were already present in both main and backup table"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE drm_rankmf_partner_registration_backup AS a INNER JOIN drm_rankmf_partner_registration AS b ON (a.email = b.email) SET b.mobile = a.mobile, b.ARN = a.ARN, b.arn_name = a.arn_name, b.status = a.status, b.form_status = a.form_status, b.unit_counsellor = a.unit_counsellor, b.dob = a.dob;"

               # inserting the new records which are not present in main but available in backup table
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tinserting the new records which are not present in main but available in backup table"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "INSERT INTO drm_rankmf_partner_registration(partner_code, email, mobile, ARN, arn_name, status, form_status, unit_counsellor, dob, created_at) SELECT a.partner_code, a.email, a.mobile, a.ARN, a.arn_name, a.status, a.form_status, a.unit_counsellor, a.dob, NOW() AS created_at FROM drm_rankmf_partner_registration_backup AS a LEFT JOIN drm_rankmf_partner_registration AS b ON (a.partner_code = b.partner_code AND a.email = b.email) WHERE b.partner_code IS NULL AND b.email IS NULL;"

               # updating RANKMF details against ARN from MySQL table: drm_rankmf_partner_registration
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating RANKMF details against ARN from MySQL table: drm_rankmf_partner_registration"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE drm_distributor_master AS a INNER JOIN drm_rankmf_partner_registration AS b ON (a.ARN = b.ARN) SET a.is_rankmf_partner = IF(b.status='activated',1,0), a.rankmf_partner_code = b.partner_code, a.rankmf_stage_of_prospect = b.form_status, a.rankmf_email = b.email, a.rankmf_mobile = b.mobile, a.record_last_available_in_amfi = a.record_last_available_in_amfi WHERE 1;"
            ################################################################
            #elif [ "$TABLE_NAME" = "drm_uploaded_arn_average_aum_total_commission_data" ]; then
               ### updating the records which were already present in both main and backup table
               #echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating the records which were already present in both main and backup table"
               #MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE drm_uploaded_arn_average_aum_total_commission_data_backup AS a INNER JOIN drm_uploaded_arn_average_aum_total_commission_data AS b ON (a.ARN = b.ARN) SET b.arn_avg_aum = a.arn_avg_aum, b.arn_total_commission = a.arn_total_commission, b.arn_yield = a.arn_yield, b.arn_business_focus_type = a.arn_business_focus_type;"

               ### inserting the new records which are not present in main but available in backup table
               #echo "$(date '+%Y-%m-%d %H:%M:%S')\tinserting the new records which are not present in main but available in backup table"
               #MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "INSERT INTO drm_uploaded_arn_average_aum_total_commission_data(ARN, arn_avg_aum, arn_total_commission, arn_yield, arn_business_focus_type, status, created_at) SELECT a.ARN, a.arn_avg_aum, a.arn_total_commission, a.arn_yield, a.arn_business_focus_type, 1 AS status, NOW() AS created_at FROM drm_uploaded_arn_average_aum_total_commission_data_backup AS a LEFT JOIN drm_uploaded_arn_average_aum_total_commission_data AS b ON (a.ARN = b.ARN) WHERE b.ARN IS NULL;"

               ### updating average aum, total commission, arn yield & business focus type against ARN from MySQL table: drm_uploaded_arn_average_aum_total_commission_data
               #echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating average aum, total commission, arn yield & business focus type against ARN from MySQL table: drm_uploaded_arn_average_aum_total_commission_data"
               #MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE drm_distributor_master AS a INNER JOIN drm_uploaded_arn_average_aum_total_commission_data AS b ON (a.ARN = b.ARN) SET a.arn_avg_aum = b.arn_avg_aum, a.arn_total_commission = b.arn_total_commission, a.arn_yield = b.arn_yield, a.arn_business_focus_type = b.arn_business_focus_type, a.record_last_available_in_amfi = a.record_last_available_in_amfi WHERE 1;"
            elif [ "$TABLE_NAME" = "drm_uploaded_arn_distributor_category" ]; then
               # updating the records which were already present in both main and backup table
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating the records which were already present in both main and backup table"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE drm_uploaded_arn_distributor_category_backup AS a INNER JOIN drm_uploaded_arn_distributor_category AS b ON (a.ARN = b.ARN) SET b.distributor_category = a.distributor_category;"

               # inserting the new records which are not present in main but available in backup table
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tinserting the new records which are not present in main but available in backup table"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "INSERT INTO drm_uploaded_arn_distributor_category(ARN, distributor_category, status, created_at) SELECT a.ARN, a.distributor_category, 1 AS status, NOW() AS created_at FROM drm_uploaded_arn_distributor_category_backup AS a LEFT JOIN drm_uploaded_arn_distributor_category AS b ON (a.ARN = b.ARN) WHERE b.ARN IS NULL;"

               # updating distributor category against ARN from MySQL table: drm_uploaded_arn_distributor_category
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating distributor category against ARN from MySQL table: drm_uploaded_arn_distributor_category"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE drm_distributor_master AS a INNER JOIN drm_uploaded_arn_distributor_category AS b ON (a.ARN = b.ARN) SET a.distributor_category = b.distributor_category, a.record_last_available_in_amfi = a.record_last_available_in_amfi WHERE 1;"
            elif [ "$TABLE_NAME" = "drm_uploaded_arn_project_focus_yes_no" ]; then
               # updating the records which were already present in both main and backup table
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating the records which were already present in both main and backup table"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE drm_uploaded_arn_project_focus_yes_no_backup AS a INNER JOIN drm_uploaded_arn_project_focus_yes_no AS b ON (a.ARN = b.ARN) SET b.project_focus = a.project_focus;"

               # inserting the new records which are not present in main but available in backup table
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tinserting the new records which are not present in main but available in backup table"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "INSERT INTO drm_uploaded_arn_project_focus_yes_no(ARN, project_focus, status, created_at) SELECT a.ARN, a.project_focus, 1 AS status, NOW() AS created_at FROM drm_uploaded_arn_project_focus_yes_no_backup AS a LEFT JOIN drm_uploaded_arn_project_focus_yes_no AS b ON (a.ARN = b.ARN) WHERE b.ARN IS NULL;"

               # updating project focus(yes/no) against ARN from MySQL table: drm_uploaded_arn_project_focus_yes_no
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating project focus(yes/no) against ARN from MySQL table: drm_uploaded_arn_project_focus_yes_no"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE drm_distributor_master AS a INNER JOIN drm_uploaded_arn_project_focus_yes_no AS b ON (a.ARN = b.ARN) SET a.project_focus = b.project_focus, a.record_last_available_in_amfi = a.record_last_available_in_amfi WHERE 1;"
            elif [ "$TABLE_NAME" = "drm_uploaded_pincode_city_state" ]; then
               # updating the records which were already present in both main and backup table
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating the records which were already present in both main and backup table"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE drm_uploaded_pincode_city_state_backup AS a INNER JOIN drm_uploaded_pincode_city_state AS b ON (a.pincode = b.pincode) SET b.city = a.city, b.state = a.state;"

               # inserting the new records which are not present in main but available in backup table
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tinserting the new records which are not present in main but available in backup table"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "INSERT INTO drm_uploaded_pincode_city_state(pincode, city, state, status, created_at) SELECT a.pincode, a.city, a.state, 1 AS status, NOW() AS created_at FROM drm_uploaded_pincode_city_state_backup AS a LEFT JOIN drm_uploaded_pincode_city_state AS b ON (a.pincode = b.pincode) WHERE b.pincode IS NULL;"

               # updating state details against ARN based on pincode from MySQL table: drm_uploaded_pincode_city_state
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating state details against ARN based on pincode from MySQL table: drm_uploaded_pincode_city_state"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE drm_distributor_master AS a INNER JOIN drm_uploaded_pincode_city_state AS b ON (a.arn_pincode = b.pincode) SET a.arn_state = b.state, a.record_last_available_in_amfi = a.record_last_available_in_amfi WHERE 1;"

               # updating state details against ARN based on city if pincode is blank in drm_distributor_master table from MySQL table: drm_uploaded_pincode_city_state
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating state details against ARN based on city if pincode is blank in drm_distributor_master table from MySQL table: drm_uploaded_pincode_city_state"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE drm_distributor_master AS a INNER JOIN drm_uploaded_pincode_city_state AS b ON (a.arn_city = b.city) SET a.arn_state = b.state, a.record_last_available_in_amfi = a.record_last_available_in_amfi WHERE (a.arn_pincode IS NULL OR a.arn_pincode = '') AND (a.arn_state IS NULL OR a.arn_state = '');"
            elif [ "$TABLE_NAME" = "drm_uploaded_arn_ind_aum_data" ]; then
               # updating the records which were already present in both main and backup table
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating the records which were already present in both main and backup table"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE drm_uploaded_arn_ind_aum_data_backup AS a INNER JOIN drm_uploaded_arn_ind_aum_data AS b ON (a.ARN = b.ARN) SET b.total_ind_aum = a.total_ind_aum, b.ind_aum_as_on_date = a.ind_aum_as_on_date;"

               # inserting the new records which are not present in main but available in backup table
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tinserting the new records which are not present in main but available in backup table"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "INSERT INTO drm_uploaded_arn_ind_aum_data(ARN, total_ind_aum, ind_aum_as_on_date, status, created_at) SELECT a.ARN, a.total_ind_aum, a.ind_aum_as_on_date, 1 AS status, NOW() AS created_at FROM drm_uploaded_arn_ind_aum_data_backup AS a LEFT JOIN drm_uploaded_arn_ind_aum_data AS b ON (a.ARN = b.ARN) WHERE b.ARN IS NULL;"

               # updating total industry aum and aum available as on date against ARN from MySQL table: drm_uploaded_arn_ind_aum_data
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating total industry aum and aum available as on date against ARN from MySQL table: drm_uploaded_arn_ind_aum_data"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE drm_distributor_master AS a INNER JOIN drm_uploaded_arn_ind_aum_data AS b ON (a.ARN = b.ARN) SET a.total_ind_aum = b.total_ind_aum, a.ind_aum_as_on_date = b.ind_aum_as_on_date, a.record_last_available_in_amfi = a.record_last_available_in_amfi WHERE 1;"
            elif [ "$TABLE_NAME" = "drm_uploaded_arn_bdm_mapping" ]; then
               # updating the records which were already present in both main and backup table
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating the records which were already present in both main and backup table"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE drm_uploaded_arn_bdm_mapping_backup AS a INNER JOIN drm_uploaded_arn_bdm_mapping AS b ON (a.ARN = b.ARN) SET b.bdm_email = a.bdm_email, b.rm_relationship = a.rm_relationship, b.updated_at = NOW();"

               # inserting the new records which are not present in main but available in backup table
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tinserting the new records which are not present in main but available in backup table"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "INSERT INTO drm_uploaded_arn_bdm_mapping(ARN, bdm_email, rm_relationship, status, created_at) SELECT a.ARN, a.bdm_email, a.rm_relationship, 1 AS status, NOW() AS created_at FROM drm_uploaded_arn_bdm_mapping_backup AS a LEFT JOIN drm_uploaded_arn_bdm_mapping AS b ON (a.ARN = b.ARN) WHERE b.ARN IS NULL;"

               # updating the MySQL table: drm_uploaded_arn_bdm_mapping where field bdm_user_id is NULL or BLANK. It will get populated based on email id available in MySQL table users
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating the MySQL table: drm_uploaded_arn_bdm_mapping where field bdm_user_id is NULL or BLANK. It will get populated based on email id available in MySQL table users"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE drm_uploaded_arn_bdm_mapping AS a INNER JOIN users AS b ON (a.bdm_email = b.email AND b.is_drm_user = 1) SET a.bdm_user_id = b.id WHERE 1;"

               # updating bdm mapping and rm relationship against ARN from MySQL table: drm_uploaded_arn_bdm_mapping
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating bdm mapping and rm relationship against ARN from MySQL table: drm_uploaded_arn_bdm_mapping"
               # here extra_parameter denotes that overwrite arn bdm mapping parameter. if it's value is yes means even though ARN have RM relationship field value as FINAL, overwrite the BDM mapping with data available in the uploaded file
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tdo we need to overwrite the BDM mapping: ${EXTRA_PARAMETER}"
               BDM_OVERWRITE_CONDITION=" AND (a.rm_relationship IS NULL OR a.rm_relationship = '' OR a.rm_relationship = 'provisional') AND b.bdm_user_id IS NOT NULL AND b.bdm_user_id != '' AND b.bdm_user_id != 0"
               if [ "$EXTRA_PARAMETER" = "yes" ]; then
                    BDM_OVERWRITE_CONDITION=""
               fi
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tBDM_OVERWRITE_CONDITION: ${BDM_OVERWRITE_CONDITION}"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE drm_distributor_master AS a INNER JOIN drm_uploaded_arn_bdm_mapping AS b ON (a.ARN = b.ARN) INNER JOIN users AS c ON (b.bdm_user_id = c.id) SET a.rm_relationship = b.rm_relationship, a.direct_relationship_user_id = b.bdm_user_id, a.record_last_available_in_amfi = a.record_last_available_in_amfi WHERE 1${BDM_OVERWRITE_CONDITION};"
            elif [ "$TABLE_NAME" = "drm_uploaded_arn_project_emerging_stars_yes_no" ]; then
               # updating the records which were already present in both main and backup table
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating the records which were already present in both main and backup table"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE drm_uploaded_arn_project_emerging_stars_yes_no_backup AS a INNER JOIN drm_uploaded_arn_project_emerging_stars_yes_no AS b ON (a.ARN = b.ARN) SET b.project_emerging_stars = a.project_emerging_stars;"

               # inserting the new records which are not present in main but available in backup table
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tinserting the new records which are not present in main but available in backup table"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "INSERT INTO drm_uploaded_arn_project_emerging_stars_yes_no(ARN, project_emerging_stars, status, created_at) SELECT a.ARN, a.project_emerging_stars, 1 AS status, NOW() AS created_at FROM drm_uploaded_arn_project_emerging_stars_yes_no_backup AS a LEFT JOIN drm_uploaded_arn_project_emerging_stars_yes_no AS b ON (a.ARN = b.ARN) WHERE b.ARN IS NULL;"

               # updating project focus(yes/no) against ARN from MySQL table: drm_uploaded_arn_project_emerging_stars_yes_no
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating project focus(yes/no) against ARN from MySQL table: drm_uploaded_arn_project_emerging_stars_yes_no"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE drm_distributor_master AS a INNER JOIN drm_uploaded_arn_project_emerging_stars_yes_no AS b ON (a.ARN = b.ARN) SET a.project_emerging_stars = b.project_emerging_stars, a.record_last_available_in_amfi = a.record_last_available_in_amfi WHERE 1;"
            elif [ "$TABLE_NAME" = "drm_uploaded_project_green_shoots_yes_no" ]; then
               # updating the records which were already present in both main and backup table
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating the records which were already present in both main and backup table"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE drm_uploaded_project_green_shoots_yes_no_backup AS a INNER JOIN drm_uploaded_project_green_shoots_yes_no AS b ON (a.ARN = b.ARN) SET b.project_green_shoots = a.project_green_shoots;"

               # inserting the new records which are not present in main but available in backup table
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tinserting the new records which are not present in main but available in backup table"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "INSERT INTO drm_uploaded_project_green_shoots_yes_no(ARN, project_green_shoots, status, created_at) SELECT a.ARN, a.project_green_shoots, 1 AS status, NOW() AS created_at FROM drm_uploaded_project_green_shoots_yes_no_backup AS a LEFT JOIN drm_uploaded_project_green_shoots_yes_no AS b ON (a.ARN = b.ARN) WHERE b.ARN IS NULL;"

               # updating project green shoots(yes/no) against ARN from MySQL table: drm_uploaded_project_green_shoots_yes_no
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating project green shoots(yes/no) against ARN from MySQL table: drm_uploaded_project_green_shoots_yes_no"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE drm_distributor_master AS a INNER JOIN drm_uploaded_project_green_shoots_yes_no AS b ON (a.ARN = b.ARN) SET a.project_green_shoots = b.project_green_shoots, a.record_last_available_in_amfi = a.record_last_available_in_amfi WHERE 1;"
            elif [ "$TABLE_NAME" = "drm_uploaded_amfi_city_zone_mapping" ]; then
               # updating the records which were already present in both main and backup table
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating the records which were already present in both main and backup table"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE drm_uploaded_amfi_city_zone_mapping_backup AS a INNER JOIN drm_uploaded_amfi_city_zone_mapping AS b ON (a.amfi_city = b.amfi_city) SET b.mapped_zone = a.mapped_zone;"

               # inserting the new records which are not present in main but available in backup table
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tinserting the new records which are not present in main but available in backup table"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "INSERT INTO drm_uploaded_amfi_city_zone_mapping(amfi_city, mapped_zone, status, created_at) SELECT a.amfi_city, a.mapped_zone, 1 AS status, NOW() AS created_at FROM drm_uploaded_amfi_city_zone_mapping_backup AS a LEFT JOIN drm_uploaded_amfi_city_zone_mapping AS b ON (a.amfi_city = b.amfi_city) WHERE b.amfi_city IS NULL;"

               # updating zone based on amfi city from MySQL table: drm_uploaded_amfi_city_zone_mapping
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating project focus(yes/no) against ARN from MySQL table: drm_uploaded_amfi_city_zone_mapping"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE drm_distributor_master AS a INNER JOIN drm_uploaded_amfi_city_zone_mapping AS b ON (a.arn_city = b.amfi_city) SET a.arn_zone = b.mapped_zone, a.record_last_available_in_amfi = a.record_last_available_in_amfi WHERE 1;"

               # updating zone as OTHERS based on those amfi city whose details not found from MySQL table: drm_uploaded_amfi_city_zone_mapping
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating project focus(yes/no) against ARN from MySQL table: drm_uploaded_amfi_city_zone_mapping"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE drm_distributor_master AS a LEFT JOIN drm_uploaded_amfi_city_zone_mapping AS b ON (a.arn_city = b.amfi_city) SET a.arn_zone = 'Others', a.created_at = a.created_at, a.updated_at = a.updated_at, a.record_last_available_in_amfi = a.record_last_available_in_amfi WHERE b.amfi_city IS NULL;"
            elif [ "$TABLE_NAME" = "drm_aum_data" ]; then
               # updating the records which were already present in both main and backup table
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating the records which were already present in both main and backup table"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE drm_aum_data AS a INNER JOIN drm_aum_data_backup AS b ON (a.trans_date = b.trans_date and a.agentcode = b.agentcode) SET a.purchase = b.purchase, a.redemption = b.redemption, a.net_sales = b.net_sales, a.available_units = b.available_units, a.agentcode = a.agentcode WHERE 1;"

               # inserting the new records which are not present in main but available in backup table
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tinserting the new records which are not present in main but available in backup table"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "INSERT INTO drm_aum_data(trans_date, purchase, redemption, net_sales, available_units, agentcode) SELECT a.trans_date, a.purchase, a.redemption, a.net_sales, a.available_units, a.agentcode FROM drm_aum_data_backup AS a LEFT JOIN drm_aum_data AS b ON (a.trans_date = b.trans_date and a.agentcode = b.agentcode) WHERE b.trans_date IS NULL and b.agentcode IS NULL;"
            elif [ "$TABLE_NAME" = "drm_client_aum_data" ]; then
               # updating the records which were already present in both main and backup table
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating the records which were already present in both main and backup table"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE drm_client_aum_data AS a INNER JOIN drm_client_aum_data_backup AS b ON (a.trans_date = b.trans_date and a.client_code = b.client_code) SET a.purchase = b.purchase, a.redemption = b.redemption, a.net_sales = b.net_sales, a.available_units = b.available_units WHERE 1;"

               # inserting the new records which are not present in main but available in backup table
               echo "$(date '+%Y-%m-%d %H:%M:%S')\tinserting the new records which are not present in main but available in backup table"
               MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "INSERT INTO drm_client_aum_data(trans_date, purchase, redemption, net_sales, available_units, client_code) SELECT a.trans_date, a.purchase, a.redemption, a.net_sales, a.available_units, a.client_code FROM drm_client_aum_data_backup AS a LEFT JOIN drm_client_aum_data AS b ON (a.trans_date = b.trans_date and a.client_code = b.client_code) WHERE b.trans_date IS NULL and b.client_code IS NULL;"
            fi
         fi

         # query to get list of records being processed after importing data from csv file
         PROCESSED_RECORDS_COUNT=$(MYSQL_PWD="${MYSQL_PASSWORD}" mysql --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "SELECT COUNT(1) AS total FROM ${TABLE_NAME} WHERE (created_at >= '${CURRENT_DATETIME}' OR updated_at >= '${CURRENT_DATETIME}');")

         echo "Number of records processed are $PROCESSED_RECORDS_COUNT"
         ;;
      \?) # Invalid option
         echo "Error: Invalid option"
         exit;;
   esac
done
