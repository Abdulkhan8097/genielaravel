#!/bin/bash

PATH=/opt/homebrew/opt/php@8.1/sbin:/opt/homebrew/opt/php@8.1/bin:/opt/homebrew/opt/php@8.1/sbin:/opt/homebrew/opt/php@8.1/bin:/opt/homebrew/bin:/opt/homebrew/sbin:/usr/local/bin:/System/Cryptexes/App/usr/bin:/usr/bin:/bin:/usr/sbin:/sbin:/var/run/com.apple.security.cryptexd/codex.system/bootstrap/usr/local/bin:/var/run/com.apple.security.cryptexd/codex.system/bootstrap/usr/bin:/var/run/com.apple.security.cryptexd/codex.system/bootstrap/usr/appleinternal/bin:$PATH

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

# IMPORT_EXPORT_CSV_STORED_FILE_PATH="/tmp"
IMPORT_EXPORT_CSV_STORED_FILE_PATH="$ENV_DIRECTORY_PATH/public/storage/arn_exported_data"
# echo "IMPORT_EXPORT_CSV_STORED_FILE_PATH=$IMPORT_EXPORT_CSV_STORED_FILE_PATH"

CURRENT_MYSQL_FORMAT_DATE="$(date '+%Y-%m-%d')"
LOG_FILEPATH="${ENV_DIRECTORY_PATH}/public/storage/logs/read_arn_data_from_partners_mongodb_${CURRENT_MYSQL_FORMAT_DATE}.txt"

touch "${LOG_FILEPATH}"
echo "$(date '+%Y-%m-%d %H:%M:%S')\t----------------------------------------------------" >> "${LOG_FILEPATH}"
echo "$(date '+%Y-%m-%d %H:%M:%S')\tCURRENT_MYSQL_FORMAT_DATE=$CURRENT_MYSQL_FORMAT_DATE" >> "${LOG_FILEPATH}"

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


PARTNERS_MYSQL_DB_USERNAME="$(grep -m1 "PARTNERS_MYSQL_DB_USERNAME" ${ENV_FILE_PATH})"
# replacing the unnecessary word and just retrieving actual DB username
PARTNERS_MYSQL_DB_USERNAME=$(echo "$PARTNERS_MYSQL_DB_USERNAME" | sed "s/PARTNERS_MYSQL_DB_USERNAME=//")

PARTNERS_MYSQL_DB_PASSWORD="$(grep -m1 "PARTNERS_MYSQL_DB_PASSWORD" ${ENV_FILE_PATH})"
# replacing the unnecessary word and just retrieving actual DB password
PARTNERS_MYSQL_DB_PASSWORD=$(echo "$PARTNERS_MYSQL_DB_PASSWORD" | sed "s/PARTNERS_MYSQL_DB_PASSWORD=//")

PARTNERS_MYSQL_DB_DATABASE="$(grep -m1 "PARTNERS_MYSQL_DB_DATABASE" ${ENV_FILE_PATH})"
# replacing the unnecessary word and just retrieving actual DB name
PARTNERS_MYSQL_DB_DATABASE=$(echo "$PARTNERS_MYSQL_DB_DATABASE" | sed "s/PARTNERS_MYSQL_DB_DATABASE=//")

PARTNERS_MYSQL_DB_HOST="$(grep -m1 "PARTNERS_MYSQL_DB_HOST" ${ENV_FILE_PATH})"
# replacing the unnecessary word and just retrieving actual DB host
PARTNERS_MYSQL_DB_HOST=$(echo "$PARTNERS_MYSQL_DB_HOST" | sed "s/PARTNERS_MYSQL_DB_HOST=//")

PARTNERS_MYSQL_DB_PORT="$(grep -m1 "PARTNERS_MYSQL_DB_PORT" ${ENV_FILE_PATH})"
# replacing the unnecessary word and just retrieving actual DB host
PARTNERS_MYSQL_DB_PORT=$(echo "$PARTNERS_MYSQL_DB_PORT" | sed "s/PARTNERS_MYSQL_DB_PORT=//")

# echo "PARTNERS_MYSQL_DB_USERNAME=$PARTNERS_MYSQL_DB_USERNAME"
# echo "PARTNERS_MYSQL_DB_PASSWORD=$PARTNERS_MYSQL_DB_PASSWORD"
# echo "PARTNERS_MYSQL_DB_DATABASE=$PARTNERS_MYSQL_DB_DATABASE"
# echo "PARTNERS_MYSQL_DB_HOST=$PARTNERS_MYSQL_DB_HOST"
# echo "PARTNERS_MYSQL_DB_PORT=$PARTNERS_MYSQL_DB_PORT"

RANKMF_MYSQL_DB_USERNAME="$(grep -m1 "RANKMF_MYSQL_DB_USERNAME" ${ENV_FILE_PATH})"
# replacing the unnecessary word and just retrieving actual DB username
RANKMF_MYSQL_DB_USERNAME=$(echo "$RANKMF_MYSQL_DB_USERNAME" | sed "s/RANKMF_MYSQL_DB_USERNAME=//")

RANKMF_MYSQL_DB_PASSWORD="$(grep -m1 "RANKMF_MYSQL_DB_PASSWORD" ${ENV_FILE_PATH})"
# replacing the unnecessary word and just retrieving actual DB password
RANKMF_MYSQL_DB_PASSWORD=$(echo "$RANKMF_MYSQL_DB_PASSWORD" | sed "s/RANKMF_MYSQL_DB_PASSWORD=//")

RANKMF_MYSQL_DB_DATABASE="$(grep -m1 "RANKMF_MYSQL_DB_DATABASE" ${ENV_FILE_PATH})"
# replacing the unnecessary word and just retrieving actual DB name
RANKMF_MYSQL_DB_DATABASE=$(echo "$RANKMF_MYSQL_DB_DATABASE" | sed "s/RANKMF_MYSQL_DB_DATABASE=//")

RANKMF_MYSQL_DB_HOST="$(grep -m1 "RANKMF_MYSQL_DB_HOST" ${ENV_FILE_PATH})"
# replacing the unnecessary word and just retrieving actual DB host
RANKMF_MYSQL_DB_HOST=$(echo "$RANKMF_MYSQL_DB_HOST" | sed "s/RANKMF_MYSQL_DB_HOST=//")

RANKMF_MYSQL_DB_PORT="$(grep -m1 "RANKMF_MYSQL_DB_PORT" ${ENV_FILE_PATH})"
# replacing the unnecessary word and just retrieving actual DB host
RANKMF_MYSQL_DB_PORT=$(echo "$RANKMF_MYSQL_DB_PORT" | sed "s/RANKMF_MYSQL_DB_PORT=//")

# echo "RANKMF_MYSQL_DB_USERNAME=$RANKMF_MYSQL_DB_USERNAME"
# echo "RANKMF_MYSQL_DB_PASSWORD=$RANKMF_MYSQL_DB_PASSWORD"
# echo "RANKMF_MYSQL_DB_DATABASE=$RANKMF_MYSQL_DB_DATABASE"
# echo "RANKMF_MYSQL_DB_HOST=$RANKMF_MYSQL_DB_HOST"
# echo "RANKMF_MYSQL_DB_PORT=$RANKMF_MYSQL_DB_PORT"

# MongoDB config parameters. GREP command helps to find a particular text from the given file and -m1 stops after 1st matching of text
MONGODB_HOST="$(grep -m1 "MONGO_DB_HOST" ${ENV_FILE_PATH})"
# replacing the unnecessary word and just retrieving actual DB hostname
MONGODB_HOST=$(echo "$MONGODB_HOST" | sed "s/MONGO_DB_HOST=//")

MONGODB_PORT="$(grep -m1 "MONGO_DB_PORT=" ${ENV_FILE_PATH})"
# replacing the unnecessary word and just retrieving actual DB port
MONGODB_PORT=$(echo "$MONGODB_PORT" | sed "s/MONGO_DB_PORT=//")

MONGODB_DBNAME="$(grep -m1 "MONGO_DB_DATABASE" ${ENV_FILE_PATH})"
# replacing the unnecessary word and just retrieving actual DB name
MONGODB_DBNAME=$(echo "$MONGODB_DBNAME" | sed "s/MONGO_DB_DATABASE=//")

MONGODB_USERNAME="$(grep -m1 "MONGO_DB_USERNAME" ${ENV_FILE_PATH})"
# replacing the unnecessary word and just retrieving actual DB username
MONGODB_USERNAME=$(echo "$MONGODB_USERNAME" | sed "s/MONGO_DB_USERNAME=//")

MONGODB_PASSWORD="$(grep -m1 "MONGO_DB_PASSWORD" ${ENV_FILE_PATH})"
# replacing the unnecessary word and just retrieving actual DB password
MONGODB_PASSWORD=$(echo "$MONGODB_PASSWORD" | sed "s/MONGO_DB_PASSWORD=//")

# echo "MONGODB_HOST=$MONGODB_HOST"
# echo "MONGODB_PORT=$MONGODB_PORT"
# echo "MONGODB_DBNAME=$MONGODB_DBNAME"
# echo "MONGODB_USERNAME=$MONGODB_USERNAME"
# echo "MONGODB_PASSWORD=$MONGODB_PASSWORD"

# exporting ARN data from MongoDB "mutual_fund_partners" > table: mf_arn_data
# echo "mongodb://${MONGODB_USERNAME}:${MONGODB_PASSWORD}@${MONGODB_HOST}:${MONGODB_PORT}/mutual_fund_partners"
echo "$(date '+%Y-%m-%d %H:%M:%S')\texporting ARN data from MongoDB mutual_fund_partners > table: mf_arn_data" >> "${LOG_FILEPATH}"
mongoexport --forceTableScan --uri="mongodb://${MONGODB_USERNAME}:${MONGODB_PASSWORD}@${MONGODB_HOST}:${MONGODB_PORT}/${MONGODB_DBNAME}" -c mf_arn_data --type=csv --fields=City,Pin,"Telephone (R)","Telephone (O)","ARN Valid From","KYD Compliant","Address","ARN Holder's Name","ARN Valid Till",Email,ARN,EUIN -o "${IMPORT_EXPORT_CSV_STORED_FILE_PATH}/mf_arn_data_${CURRENT_MYSQL_FORMAT_DATE}.csv"

TABLE_DISTRIBUTOR_MASTER="drm_distributor_master"

# importing ARN data into MySQL table: drm_distributor_master
echo "$(date '+%Y-%m-%d %H:%M:%S')\timporting ARN data into MySQL table: drm_distributor_master" >> "${LOG_FILEPATH}"
sh "${ENV_DIRECTORY_PATH}"/vendor/shell_scripts/import_csv_data.sh -i"${IMPORT_EXPORT_CSV_STORED_FILE_PATH}/mf_arn_data_${CURRENT_MYSQL_FORMAT_DATE}.csv" "${TABLE_DISTRIBUTOR_MASTER}" "IGNORE 1 LINES (@arn_city, @arn_pincode, @arn_telephone_r, @arn_telephone_o, @arn_valid_from, @arn_kyd_compliant, @arn_address, @arn_holders_name, @arn_valid_till, @arn_email, @ARN, @arn_euin) SET arn_city = TRIM(@arn_city), arn_pincode = TRIM(@arn_pincode), arn_telephone_r = TRIM(@arn_telephone_r), arn_telephone_o = TRIM(@arn_telephone_o), arn_valid_from = STR_TO_DATE(@arn_valid_from,'%e-%b-%Y'), arn_kyd_compliant = TRIM(@arn_kyd_compliant), arn_address = TRIM(@arn_address), arn_holders_name = REPLACE(REPLACE(REPLACE(TRIM(@arn_holders_name), ' ', '<>'),'><',''),'<>',' '), arn_valid_till = STR_TO_DATE(@arn_valid_till,'%e-%b-%Y'), arn_email = TRIM(@arn_email), ARN = TRIM(@ARN), arn_euin = TRIM(@arn_euin);" "no" >> "${LOG_FILEPATH}"

# exporting data from MongoDB "mutual_fund_partners" > table: curent_aum_log
echo "$(date '+%Y-%m-%d %H:%M:%S')\texporting data from MongoDB mutual_fund_partners > table: curent_aum_log" >> "${LOG_FILEPATH}"
mongoexport --forceTableScan --uri="mongodb://${MONGODB_USERNAME}:${MONGODB_PASSWORD}@${MONGODB_HOST}:${MONGODB_PORT}/${MONGODB_DBNAME}" -c curent_aum_log --type=csv --fields=client,broker_id,total_value,equity_aum,debt_aum,hybrid_aum,others_amu,commodity_amu -o "${IMPORT_EXPORT_CSV_STORED_FILE_PATH}/curent_aum_log_${CURRENT_MYSQL_FORMAT_DATE}.csv"

# importing current AUM data into MySQL table: drm_partners_rankmf_current_aum
# once records got imported, details of RANKMF total aum will get updpated in MySQL table: drm_distributor_master from the shell script mentioned below
echo "$(date '+%Y-%m-%d %H:%M:%S')\timporting current AUM data into MySQL table: drm_partners_rankmf_current_aum" >> "${LOG_FILEPATH}"
sh "${ENV_DIRECTORY_PATH}"/vendor/shell_scripts/import_csv_data.sh -i"${IMPORT_EXPORT_CSV_STORED_FILE_PATH}/curent_aum_log_${CURRENT_MYSQL_FORMAT_DATE}.csv" "drm_partners_rankmf_current_aum" "IGNORE 1 LINES (client_id, broker_id, total_aum, equity_aum, debt_aum, hybrid_aum, others_aum, commodity_aum, @status) SET status = 1;" "no" >> "${LOG_FILEPATH}"

# exporting data from MySQL DB "mutual_fund_partners" > table mfp_partner_login_master
echo "$(date '+%Y-%m-%d %H:%M:%S')\texporting data from MySQL DB mutual_fund_partners > table mfp_partner_login_master" >> "${LOG_FILEPATH}"

MYSQL_PWD="${PARTNERS_MYSQL_DB_PASSWORD}" mysql --local-infile=1 --user="${PARTNERS_MYSQL_DB_USERNAME}" --ssl-mode=disabled --host="${PARTNERS_MYSQL_DB_HOST}" --database="${PARTNERS_MYSQL_DB_DATABASE}" -e "SELECT id, role_name, email, password, name, mobile, pan, address, b_login_id, a_login_id, c_login_id, n_login_id, status, employee_code, unit_code, branch_code, title, marital_status, pincode, city, dob FROM mfp_partner_login_master;" | sed 's/\t/","/g;s/^/"/;s/$/"/;s/\n//g' > "${IMPORT_EXPORT_CSV_STORED_FILE_PATH}/mfp_partner_login_master_${CURRENT_MYSQL_FORMAT_DATE}.csv"

# importing rankmf partners users data into MySQL table: drm_partners_rankmf_bdm_list
echo "$(date '+%Y-%m-%d %H:%M:%S')\timporting rankmf partners users data into MySQL table: drm_partners_rankmf_bdm_list" >> "${LOG_FILEPATH}"
sh "${ENV_DIRECTORY_PATH}"/vendor/shell_scripts/import_csv_data.sh -i"${IMPORT_EXPORT_CSV_STORED_FILE_PATH}/mfp_partner_login_master_${CURRENT_MYSQL_FORMAT_DATE}.csv" "drm_partners_rankmf_bdm_list" "IGNORE 1 LINES (login_master_sr_id, role_name, email, password, name, mobile, pan, address, branch_manager, area_manager, circle_manager, national_manager, status, employee_code, unit_code, branch_code, title, marital_status, pincode, city, @dob) SET dob = REPLACE(@dob,'0000-00-00','1800-01-01');" "no" >> "${LOG_FILEPATH}"

# updating the user(s) DOB which were having value as 1800-01-01
echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating the user(s) DOB which were having value as 1800-01-01" >> "${LOG_FILEPATH}"
MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE drm_partners_rankmf_bdm_list SET dob = NULL, created_at = created_at, updated_at = updated_at WHERE dob='1800-01-01';" >> "${LOG_FILEPATH}"

# exporting data from MySQL DB "mutual_fund_partners" > table mfp_partner_registration
echo "$(date '+%Y-%m-%d %H:%M:%S')\texporting data from MySQL DB mutual_fund_partners > table mfp_partner_registration" >> "${LOG_FILEPATH}"

MYSQL_PWD="${PARTNERS_MYSQL_DB_PASSWORD}" mysql --local-infile=1 --user="${PARTNERS_MYSQL_DB_USERNAME}" --ssl-mode=disabled --host="${PARTNERS_MYSQL_DB_HOST}" --database="${PARTNERS_MYSQL_DB_DATABASE}" -e "SELECT IFNULL(partner.ARN, '') AS ARN, IFNULL(partner.arn_name, '') AS arn_name, IFNULL(partner.partner_code, '') AS partner_code, CASE WHEN (partner.status = 1) THEN 'created' WHEN (partner.status = 2) THEN 'approved' WHEN (partner.status = 3) THEN 'activated' WHEN (partner.status = 4) THEN 'deactivated' ELSE partner.status END AS status, CASE WHEN (partner.form_status = 1) THEN 'pan' WHEN (partner.form_status = 2) THEN 'personal' WHEN (partner.form_status = 3) THEN 'communication' WHEN (partner.form_status = 4) THEN 'mobile verification' WHEN (partner.form_status = 5) THEN 'email verification' WHEN (partner.form_status = 6) THEN 'bank' WHEN (partner.form_status = 7) THEN 'arn' WHEN (partner.form_status = 8) THEN 'upload' WHEN (partner.form_status = 9) THEN 'business_detail' WHEN (partner.form_status = 10) THEN 'thank you' ELSE partner.form_status END AS form_status, IFNULL(partner.unit_counsellor, '') AS bdm_id, IFNULL(partner.mobile, '') AS mobile, IFNULL(partner.email, '') AS email,IFNULL(partner.dob, '') AS dob FROM mfp_partner_registration AS partner WHERE 1;" | sed 's/\t/","/g;s/^/"/;s/$/"/;s/\n//g' > "${IMPORT_EXPORT_CSV_STORED_FILE_PATH}/mfp_partner_registration_${CURRENT_MYSQL_FORMAT_DATE}.csv"

# importing rankmf registered partners data into MySQL table: drm_rankmf_partner_registration
# once records got imported, details of RANKMF partner code, form status, email, mobile and rankmf record status(i.e. activated/created etc.) will get updpated in MySQL table: drm_distributor_master from the shell script mentioned below
echo "$(date '+%Y-%m-%d %H:%M:%S')\timporting rankmf registered partners data into MySQL table: drm_rankmf_partner_registration" >> "${LOG_FILEPATH}"
sh "${ENV_DIRECTORY_PATH}"/vendor/shell_scripts/import_csv_data.sh -i"${IMPORT_EXPORT_CSV_STORED_FILE_PATH}/mfp_partner_registration_${CURRENT_MYSQL_FORMAT_DATE}.csv" "drm_rankmf_partner_registration" "IGNORE 1 LINES (ARN, arn_name, partner_code, status, form_status, unit_counsellor, mobile, email, dob);" "no" >> "${LOG_FILEPATH}"

# updating average aum, total commission, arn yield & business focus type against ARN from MySQL table: drm_uploaded_arn_average_aum_total_commission_data
echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating average aum, total commission, arn yield & business focus type against ARN from MySQL table: drm_uploaded_arn_average_aum_total_commission_data" >> "${LOG_FILEPATH}"
MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE drm_distributor_master AS a INNER JOIN drm_uploaded_arn_average_aum_total_commission_data AS b ON (a.ARN = b.ARN) SET a.arn_avg_aum = b.arn_avg_aum, a.arn_total_commission = b.arn_total_commission, a.arn_yield = b.arn_yield, a.arn_business_focus_type = b.arn_business_focus_type, a.record_last_available_in_amfi = a.record_last_available_in_amfi WHERE 1;" >> "${LOG_FILEPATH}"

# updating distributor category against ARN from MySQL table: drm_uploaded_arn_distributor_category
echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating distributor category against ARN from MySQL table: drm_uploaded_arn_distributor_category" >> "${LOG_FILEPATH}"
MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE drm_distributor_master AS a INNER JOIN drm_uploaded_arn_distributor_category AS b ON (a.ARN = b.ARN) SET a.distributor_category = b.distributor_category, a.record_last_available_in_amfi = a.record_last_available_in_amfi WHERE 1;" >> "${LOG_FILEPATH}"

# updating project focus(yes/no) against ARN from MySQL table: drm_uploaded_arn_project_focus_yes_no
echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating project focus(yes/no) against ARN from MySQL table: drm_uploaded_arn_project_focus_yes_no" >> "${LOG_FILEPATH}"
MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE drm_distributor_master AS a INNER JOIN drm_uploaded_arn_project_focus_yes_no AS b ON (a.ARN = b.ARN) SET a.project_focus = b.project_focus, a.record_last_available_in_amfi = a.record_last_available_in_amfi WHERE 1;" >> "${LOG_FILEPATH}"

# updating total industry aum and aum available as on date against ARN from MySQL table: drm_uploaded_arn_ind_aum_data
echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating total industry aum and aum available as on date against ARN from MySQL table: drm_uploaded_arn_ind_aum_data" >> "${LOG_FILEPATH}"
MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE drm_distributor_master AS a INNER JOIN drm_uploaded_arn_ind_aum_data AS b ON (a.ARN = b.ARN) SET a.total_ind_aum = b.total_ind_aum, a.ind_aum_as_on_date = b.ind_aum_as_on_date, a.record_last_available_in_amfi = a.record_last_available_in_amfi WHERE 1;" >> "${LOG_FILEPATH}"

# updating bdm mapping and rm relationship against ARN from MySQL table: drm_uploaded_arn_bdm_mapping
echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating bdm mapping and rm relationship against ARN from MySQL table: drm_uploaded_arn_bdm_mapping" >> "${LOG_FILEPATH}"
MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE drm_distributor_master AS a INNER JOIN drm_uploaded_arn_bdm_mapping AS b ON (a.ARN = b.ARN) SET a.rm_relationship = b.rm_relationship, a.direct_relationship_user_id = b.bdm_user_id, a.record_last_available_in_amfi = a.record_last_available_in_amfi WHERE (a.rm_relationship IS NULL OR a.rm_relationship = '' OR a.rm_relationship = 'provisional');" >> "${LOG_FILEPATH}"

# updating SAMCOMF details against ARN from MySQL table: user_account
echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating SAMCOMF details against ARN from MySQL table: user_account" >> "${LOG_FILEPATH}"
MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE drm_distributor_master AS a INNER JOIN user_account AS b ON (a.ARN = b.ARN) SET a.is_samcomf_partner = IF(b.status='2',1,0), a.samcomf_partner_code = b.distributor_id, a.samcomf_stage_of_prospect = CASE WHEN (b.form_status = 1) THEN 'Verification' WHEN (b.form_status = 2) THEN 'Upload Documents' WHEN (b.form_status = 3) THEN 'Consent' WHEN (b.form_status = 4) THEN 'Thank You' WHEN (b.form_status = 5) THEN 'Add Signatories' WHEN (b.form_status = 6) THEN 'E-sign & Verify' WHEN (b.form_status = 7) THEN 'Add Bank Details' WHEN (b.form_status = 8) THEN 'Nominee Details' WHEN (b.form_status = 8) THEN 'Upload ARN' ELSE b.form_status END, a.samcomf_email = b.email, a.samcomf_mobile = b.mobile, a.record_last_available_in_amfi = a.record_last_available_in_amfi WHERE 1;" >> "${LOG_FILEPATH}"

# updating alternate email and mobile details against ARN from MySQL table: arn_alternate_details
echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating alternate email and mobile details against ARN from MySQL table: arn_alternate_details" >> "${LOG_FILEPATH}"
MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE drm_distributor_master AS a INNER JOIN arn_alternate_details AS b ON (a.ARN = b.arn) SET a.alternate_mobile_1 = b.alternate_mobile_1, a.alternate_mobile_2 = b.alternate_mobile_2, a.alternate_mobile_3 = b.alternate_mobile_3, a.alternate_mobile_4 = b.alternate_mobile_4, a.alternate_mobile_5 = b.alternate_mobile_5, a.alternate_email_1 = b.alternate_email_1, a.alternate_email_2 = b.alternate_email_2, a.alternate_email_3 = b.alternate_email_3, a.alternate_email_4 = b.alternate_email_4, a.alternate_email_5 = b.alternate_email_5, a.record_last_available_in_amfi = a.record_last_available_in_amfi WHERE 1;" >> "${LOG_FILEPATH}"

# updating state details against ARN based on pincode from MySQL table: drm_uploaded_pincode_city_state
echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating state details against ARN based on pincode from MySQL table: drm_uploaded_pincode_city_state" >> "${LOG_FILEPATH}"
MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE drm_distributor_master AS a INNER JOIN drm_uploaded_pincode_city_state AS b ON (a.arn_pincode = b.pincode) SET a.arn_state = b.state, a.pincode_city = b.city, a.record_last_available_in_amfi = a.record_last_available_in_amfi WHERE 1;" >> "${LOG_FILEPATH}"

# updating state details against ARN based on city if pincode is blank in drm_distributor_master table from MySQL table: drm_uploaded_pincode_city_state
echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating state details against ARN based on city if pincode is blank in drm_distributor_master table from MySQL table: drm_uploaded_pincode_city_state" >> "${LOG_FILEPATH}"
MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE drm_distributor_master AS a INNER JOIN drm_uploaded_pincode_city_state AS b ON (a.arn_city = b.city) SET a.arn_state = b.state, a.record_last_available_in_amfi = a.record_last_available_in_amfi WHERE (a.arn_pincode IS NULL OR a.arn_pincode = '') AND (a.arn_state IS NULL OR a.arn_state = '');" >> "${LOG_FILEPATH}"

# updating the field direct_relationship_user_id from MySQL table: drm_distributor_master for those ARN holders who have BDM assigned in RANKMF and that BDM entry is available in MySQL table: users
echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating the field direct_relationship_user_id from MySQL table: drm_distributor_master for those ARN holders who have BDM assigned in RANKMF and that BDM entry is available in MySQL table: users" >> "${LOG_FILEPATH}"
MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE
    drm_rankmf_partner_registration AS a
INNER JOIN drm_partners_rankmf_bdm_list AS b
ON
    (
        a.unit_counsellor = b.login_master_sr_id
    )
INNER JOIN users AS c
ON
    (
        b.email = c.email AND c.is_drm_user = 1
    )
INNER JOIN users_details AS e
ON
    (
        e.user_id = c.id AND e.is_Old = 0 AND e.is_deleted = 0
    )
INNER JOIN drm_distributor_master AS d
ON
    (a.ARN = d.ARN)
SET
    d.direct_relationship_user_id = c.id
WHERE
    (
        d.direct_relationship_user_id IS NULL OR d.direct_relationship_user_id = '' AND e.is_Old = 0 AND e.is_deleted = 0
    );" >> "${LOG_FILEPATH}"

# updating project focus(yes/no) against ARN from MySQL table: drm_uploaded_arn_project_emerging_stars_yes_no
echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating project focus(yes/no) against ARN from MySQL table: drm_uploaded_arn_project_emerging_stars_yes_no" >> "${LOG_FILEPATH}"
MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE drm_distributor_master AS a INNER JOIN drm_uploaded_arn_project_emerging_stars_yes_no AS b ON (a.ARN = b.ARN) SET a.project_emerging_stars = b.project_emerging_stars, a.record_last_available_in_amfi = a.record_last_available_in_amfi WHERE 1;" >> "${LOG_FILEPATH}"

# updating project green shoots(yes/no) against ARN from MySQL table: drm_uploaded_project_green_shoots_yes_no
echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating project focus(yes/no) against ARN from MySQL table: drm_uploaded_project_green_shoots_yes_no" >> "${LOG_FILEPATH}"
MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE drm_distributor_master AS a INNER JOIN drm_uploaded_project_green_shoots_yes_no AS b ON (a.ARN = b.ARN) SET a.project_green_shoots = b.project_green_shoots, a.record_last_available_in_amfi = a.record_last_available_in_amfi WHERE 1;" >> "${LOG_FILEPATH}"

# updating zone based on amfi city from MySQL table: drm_uploaded_amfi_city_zone_mapping
echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating project focus(yes/no) against ARN from MySQL table: drm_uploaded_amfi_city_zone_mapping" >> "${LOG_FILEPATH}"
MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE drm_distributor_master AS a INNER JOIN drm_uploaded_amfi_city_zone_mapping AS b ON (a.arn_city = b.amfi_city) SET a.arn_zone = b.mapped_zone, a.record_last_available_in_amfi = a.record_last_available_in_amfi WHERE 1;" >> "${LOG_FILEPATH}"

# updating zone as OTHERS based on those amfi city whose details not found from MySQL table: drm_uploaded_amfi_city_zone_mapping
echo "$(date '+%Y-%m-%d %H:%M:%S')\tupdating project focus(yes/no) against ARN from MySQL table: drm_uploaded_amfi_city_zone_mapping" >> "${LOG_FILEPATH}"
MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE drm_distributor_master AS a LEFT JOIN drm_uploaded_amfi_city_zone_mapping AS b ON (a.arn_city = b.amfi_city) SET a.arn_zone = 'Others', a.created_at = a.created_at, a.updated_at = a.updated_at, a.record_last_available_in_amfi = a.record_last_available_in_amfi WHERE b.amfi_city IS NULL;" >> "${LOG_FILEPATH}"

# inserting users details which are not present in Appointments DB ea_users table but present in SAMCOMF DB users table
echo "$(date '+%Y-%m-%d %H:%M:%S')\tinserting users details which are not present in Appointments DB ea_users table but present in SAMCOMF DB users table" >> "${LOG_FILEPATH}"
MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "INSERT INTO appointments.ea_users(id, first_name, last_name, email, mobile_number, phone_number, address, city, state, zip_code, notes, clientID, id_roles) SELECT NULL AS id, TRIM(IF(LOCATE(' ', users.name) > 0, SUBSTR(users.name, 1, (LOCATE(' ', users.name) - 1)), users.name)) AS first_name, TRIM(IF(LOCATE(' ', users.name) > 0, SUBSTR(users.name, (LOCATE(' ', users.name) + 1)), '')) AS last_name, users.email, users_details.mobile_number, users_details.mobile_number, '' AS address, users_details.city, '' AS state, '' AS zip_code, '' AS notes, '' AS clientID, 2 AS id_roles FROM users INNER JOIN users_details ON (users.id = users_details.user_id) LEFT JOIN appointments.ea_users ON (users.email = ea_users.email) WHERE ea_users.email IS NULL AND users.is_drm_user = 1;" >> "${LOG_FILEPATH}"

# Adding those details into Appointments DB ea_user_settings table which are present in Appointments DB ea_users table but not present in ea_user_setting table
echo "$(date '+%Y-%m-%d %H:%M:%S')\tAdding those details into Appointments DB ea_user_settings table which are present in Appointments DB ea_users table but not present in ea_user_setting table" >> "${LOG_FILEPATH}"
MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "INSERT INTO appointments.ea_user_settings(id_users, username, password, salt, working_plan, notifications, google_sync, google_token, google_calendar, sync_past_days, sync_future_days, calendar_view) SELECT ea_users.id AS id_users, SUBSTR(ea_users.email, 1, LOCATE('@', ea_users.email) - 1) AS username, 'dbf093f65269d24adfadeb6b43958278beba48a398ef12f465a597b02a860928' AS password, 'df136ad606279c59c3e28d14d47a36973ff73f6f6d7162b9dac8db3047991e4d' AS salt, '{\"sunday\":{\"start\":\"09:00\",\"end\":\"21:30\",\"breaks\":[]},\"monday\":{\"start\":\"09:00\",\"end\":\"21:30\",\"breaks\":[]},\"tuesday\":{\"start\":\"09:00\",\"end\":\"21:30\",\"breaks\":[]},\"wednesday\":{\"start\":\"09:00\",\"end\":\"21:30\",\"breaks\":[]},\"thursday\":{\"start\":\"09:00\",\"end\":\"21:30\",\"breaks\":[]},\"friday\":{\"start\":\"09:00\",\"end\":\"21:30\",\"breaks\":[]},\"saturday\":{\"start\":\"09:00\",\"end\":\"21:30\",\"breaks\":[]}}' AS working_plan, 0 AS notifications, 0 AS google_sync, '' AS google_token, '' AS google_calendar, 5 AS sync_past_days, '5' AS sync_future_days, 'default' AS calendar_view FROM appointments.ea_users LEFT JOIN appointments.ea_user_settings ON (ea_users.id = ea_user_settings.id_users) WHERE ea_user_settings.id_users IS NULL AND ea_users.id_roles = 2;" >> "${LOG_FILEPATH}"

# Removing Empanelled data from old BDMS
#echo "$(date '+%Y-%m-%d %H:%M:%S')\tRemoving Empanelled data from old BDMS" >> "${LOG_FILEPATH}"
#MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "UPDATE drm_distributor_master SET direct_relationship_user_id = NULL WHERE is_samcomf_partner = 0 AND direct_relationship_user_id IS NOT NULL;" >> "${LOG_FILEPATH}"

##------------------------------------------------------------------------------------------------

# exporting data from MySQL DB "mutual_funds" > table mf_transaction_data
echo "$(date '+%Y-%m-%d %H:%M:%S')\texporting data from MySQL DB mutual_funds > table mf_transaction_data" >> "${LOG_FILEPATH}"

MYSQL_PWD="${RANKMF_MYSQL_DB_PASSWORD}" mysql --local-infile=1 --user="${RANKMF_MYSQL_DB_USERNAME}" --ssl-mode=disabled --host="${RANKMF_MYSQL_DB_HOST}" --database="${RANKMF_MYSQL_DB_DATABASE}" -e "select x.trans_date ,x.PURCHASE as PURCHASE ,x.REDEMPTION as REDEMPTION,(x.PURCHASE - x.REDEMPTION) AS net_sales, x.available_units,x.agentcode
from
(SELECT 

	sum(CASE WHEN trxn_type_name = 'REDEMPTION' OR trxn_type_name = 'DIVIDEND PAYOUT' OR trxn_type_name = 'SWITCH OUT' OR trxn_type_name = 'TRANFER OUT'
	AND sub_trxntype_name = '' THEN ROUND(amount,2) else 0 END ) REDEMPTION,
	sum(CASE WHEN trxn_type_name = 'PURCHASE' OR trxn_type_name = 'DIVIDEND REINVESTMENT' OR trxn_type_name = 'SWITCH IN' OR trxn_type_name = 'TRANSFER IN'  
	OR trxn_type_name = 'NFO' THEN ROUND(amount,2) else 0 END ) PURCHASE, 
	DATE_FORMAT(trxn_date, '%Y-%m') AS trans_date, sum(signed_units) as available_units,agentcode
	FROM mf_transaction_data
	where agentcode not in ('sam_99999')
	GROUP BY DATE_FORMAT(trxn_date, '%Y-%m'),agentcode
	ORDER BY trans_date ASC
	
)x;" | sed 's/\t/","/g;s/^/"/;s/$/"/;s/\n//g' > "${IMPORT_EXPORT_CSV_STORED_FILE_PATH}/mf_transaction_monthwise_partnerwise_${CURRENT_MYSQL_FORMAT_DATE}.csv"

# importing rankmf registered partners data into MySQL table: drm_aum_data

echo "$(date '+%Y-%m-%d %H:%M:%S')\timporting rankmf aum data into MySQL table: drm_aum_data" >> "${LOG_FILEPATH}"
sh "${ENV_DIRECTORY_PATH}"/vendor/shell_scripts/import_csv_data.sh -i"${IMPORT_EXPORT_CSV_STORED_FILE_PATH}/mf_transaction_monthwise_partnerwise_${CURRENT_MYSQL_FORMAT_DATE}.csv" "drm_aum_data" "IGNORE 1 LINES (trans_date,purchase,redemption,net_sales,available_units,agentcode);" "no" >> "${LOG_FILEPATH}"

##------------------------------------------------------------------------------------------------
## Importing AUM and brokerage commission data from rankmf mongo database to drm database
php artisan Aumdata:getAumdata
##------------------------------------------------------------------------------------------------
## To asign ARN
php $ENV_DIRECTORY_PATH/artisan assignusertoarn:map >> "${LOG_FILEPATH}"

##------------------------------------------------------------------------------------------------

# # exporting data from MySQL DB "mutual_funds" > table mf_transaction_data for drm_client_aum_data

# echo "$(date '+%Y-%m-%d %H:%M:%S')\texporting data from MySQL DB mutual_funds > table mf_transaction_data" >> "${LOG_FILEPATH}"

# MYSQL_PWD="${RANKMF_MYSQL_DB_PASSWORD}" mysql --local-infile=1 --user="${RANKMF_MYSQL_DB_USERNAME}" --ssl-mode=disabled --host="${RANKMF_MYSQL_DB_HOST}" --database="${RANKMF_MYSQL_DB_DATABASE}" -e "select x.trans_date ,x.PURCHASE as PURCHASE ,x.REDEMPTION as REDEMPTION,(x.PURCHASE - x.REDEMPTION) AS net_sales, x.available_units,x.client_code
# from
# (SELECT 

# 	sum(CASE WHEN trxn_type_name = 'REDEMPTION' OR trxn_type_name = 'DIVIDEND PAYOUT' OR trxn_type_name = 'SWITCH OUT' OR trxn_type_name = 'TRANFER OUT'
# 	AND sub_trxntype_name = '' THEN ROUND(amount,2) else 0 END ) REDEMPTION,
# 	sum(CASE WHEN trxn_type_name = 'PURCHASE' OR trxn_type_name = 'DIVIDEND REINVESTMENT' OR trxn_type_name = 'SWITCH IN' OR trxn_type_name = 'TRANSFER IN'  
# 	OR trxn_type_name = 'NFO' THEN ROUND(amount,2) else 0 END ) PURCHASE, 
# 	DATE_FORMAT(trxn_date, '%Y-%m') AS trans_date, sum(signed_units) as available_units,client_code

# 	FROM mf_transaction_data
# 	GROUP BY DATE_FORMAT(trxn_date, '%Y-%m'),client_code
# 	ORDER BY trans_date ASC
	
# )x;" | sed 's/\t/","/g;s/^/"/;s/$/"/;s/\n//g' > "${IMPORT_EXPORT_CSV_STORED_FILE_PATH}/mf_transaction_monthwise_clientwise_${CURRENT_MYSQL_FORMAT_DATE}.csv"

# # importing rankmf registered partners data into MySQL table: drm_client_aum_data

# echo "$(date '+%Y-%m-%d %H:%M:%S')\timporting rankmf aum data into MySQL table: drm_client_aum_data" >> "${LOG_FILEPATH}"
# sh "${ENV_DIRECTORY_PATH}"/vendor/shell_scripts/import_csv_data.sh -i"${IMPORT_EXPORT_CSV_STORED_FILE_PATH}/mf_transaction_monthwise_clientwise_${CURRENT_MYSQL_FORMAT_DATE}.csv" "drm_client_aum_data" "IGNORE 1 LINES (trans_date,purchase,redemption,net_sales,available_units,client_code);" "no" >> "${LOG_FILEPATH}"

echo "Completed.\n";
