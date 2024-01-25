#!/bin/bash
############################################################
# Help                                                     #
############################################################
Help()
{
   # Display Help
   echo "Script helps to export CSV file."
   echo
   echo "Syntax: scriptTemplate [-i|h|v]"
   echo "options:"
   echo "i     Exports csv file data from MySQL table."
   echo "            Three parameters are required for execution of a script"
   echo "            First parameter is table name"
   echo "            Second parameter is where conditions"
   echo "            Third parameter is order by clause"
   echo "h     Print this Help."
   echo
}

PATH=/opt/homebrew/opt/php@7.2/sbin:/opt/homebrew/opt/php@7.2/bin:/opt/homebrew/opt/php@7.2/sbin:/opt/homebrew/opt/php@7.2/bin:/opt/homebrew/bin:/opt/homebrew/sbin:/usr/local/bin:/System/Cryptexes/App/usr/bin:/usr/bin:/bin:/usr/sbin:/sbin:/var/run/com.apple.security.cryptexd/codex.system/bootstrap/usr/local/bin:/var/run/com.apple.security.cryptexd/codex.system/bootstrap/usr/bin:/var/run/com.apple.security.cryptexd/codex.system/bootstrap/usr/appleinternal/bin:$PATH

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
      i) # execute a script and export data
         # checking number of arguments equals to 3 or not
         if [ "$#" -ne "3" ]; then
            echo "3 input parameters are required to execute the script"
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
         LOG_FILEPATH="${ENV_DIRECTORY_PATH}/public/storage/logs/export_csv_data_${CURRENT_MYSQL_FORMAT_DATE}.txt"

         touch "${LOG_FILEPATH}"
         echo "$(date '+%Y-%m-%d %H:%M:%S')\t----------------------------------------------------" >> "${LOG_FILEPATH}"

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

        #  echo "MYSQL_USERNAME=$MYSQL_USERNAME"
        #  echo "MYSQL_PASSWORD=$MYSQL_PASSWORD"
        #  echo "MYSQL_DBNAME=$MYSQL_DBNAME"
        #  echo "MYSQL_DB_HOST=$MYSQL_DB_HOST"
         
         # EXPORT_EXPORT_CSV_STORED_FILE_PATH="/tmp"
         EXPORT_EXPORT_CSV_STORED_FILE_PATH="$ENV_DIRECTORY_PATH/public/storage/arn_exported_data"
         # echo "EXPORT_EXPORT_CSV_STORED_FILE_PATH=$EXPORT_EXPORT_CSV_STORED_FILE_PATH"

         # Get current date
         CURRENT_DATETIME="$(date '+%Y-%m-%d_%H-%M-%S')"

         # 1st argument will be table name
         TABLE_NAME=$OPTARG
         # 2nd argument will be where conditions
         WHERE_CONDITIONS=$2
         # 3rd argument will be order by clause
         ORDER_BY_CLAUSE=$3

         echo "$(date '+%Y-%m-%d %H:%M:%S')\tCURRENT_DATETIME=$CURRENT_DATETIME" >> "${LOG_FILEPATH}"
         echo "$(date '+%Y-%m-%d %H:%M:%S')\tTABLE_NAME: $TABLE_NAME" >> "${LOG_FILEPATH}"
         echo "$(date '+%Y-%m-%d %H:%M:%S')\tWHERE_CONDITIONS: $WHERE_CONDITIONS" >> "${LOG_FILEPATH}"
         echo "$(date '+%Y-%m-%d %H:%M:%S')\tORDER_BY_CLAUSE: $ORDER_BY_CLAUSE" >> "${LOG_FILEPATH}"

         if [ "$TABLE_NAME" = "drm_distributor_master" ]; then
            # query to export data from MySQL table: drm_distributor_master

            MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "SELECT 
		IFNULL(drm_distributor_master.ARN, '') AS 'AMFI - ARN', 
		IFNULL(drm_distributor_master.arn_holders_name, '') AS 'AMFI - ARN Holder\'s Name', 
		IFNULL(drm_distributor_master.arn_email, '') AS 'AMFI - Email', 
		IFNULL(drm_distributor_master.arn_telephone_r, '') AS 'AMFI - Telephone (R)', 
		IFNULL(drm_distributor_master.arn_telephone_o, '') AS 'AMFI - Telephone (O)', 
		IFNULL(REPLACE(drm_distributor_master.arn_address, '\"', ''), '') AS 'AMFI - Address', 
		IFNULL(drm_distributor_master.arn_city, '') AS 'AMFI - City', 
		IFNULL(drm_distributor_master.pincode_city, '') AS 'City', 
		IFNULL(drm_distributor_master.arn_pincode, '') AS 'AMFI - Pin', 
		IFNULL(drm_distributor_master.arn_kyd_compliant, '') AS 'AMFI - KYD Compliant', 
		IFNULL(drm_distributor_master.arn_euin, '') AS 'AMFI - EUIN', 
		IFNULL(CONCAT(DATE_FORMAT(drm_distributor_master.arn_valid_from, '%d/%m/%Y')), '') AS 'AMFI - ARN Valid From', 
		IFNULL(CONCAT(DATE_FORMAT(drm_distributor_master.arn_valid_till, '%d/%m/%Y')), '') AS 'AMFI - ARN Valid Till', 
		IFNULL(drm_distributor_master.distributor_category, '') AS 'Distributor Category', 
		IFNULL(reporting_to_tbl.name, '') AS 'Reporting Manager Name', 
		IFNULL(drm_distributor_master.alternate_mobile_1, '') AS 'Alternate Mobile Number - 1', 
		IFNULL(drm_distributor_master.alternate_mobile_2, '') AS 'Alternate Mobile Number - 2', 
		IFNULL(drm_distributor_master.alternate_mobile_3, '') AS 'Alternate Mobile Number - 3', 
		IFNULL(drm_distributor_master.alternate_email_1, '') AS 'Alternate Email ID - 1', 
		IFNULL(drm_distributor_master.alternate_email_2, '') AS 'Alternate Email ID - 2', 
		IFNULL(drm_distributor_master.alternate_email_3, '') AS 'Alternate Email ID - 3', 
		IFNULL(drm_distributor_master.arn_avg_aum, '') AS 'ARN Average AUM - Last Reported' 
		FROM drm_distributor_master 
		LEFT JOIN users ON (drm_distributor_master.direct_relationship_user_id = users.id) 
		LEFT JOIN users_details ON (users.id = users_details.user_id) 
		LEFT JOIN users AS reporting_to_tbl ON (users_details.reporting_to = reporting_to_tbl.id) 
		LEFT JOIN users_details AS reporting_to_tbl_details ON (reporting_to_tbl.id = reporting_to_tbl_details.user_id) 
		${WHERE_CONDITIONS} 
		${ORDER_BY_CLAUSE};" | sed 's/\t/","/g;s/^/"/;s/$/"/;s/\n//g' > "${EXPORT_EXPORT_CSV_STORED_FILE_PATH}/distributor_master_data_${CURRENT_DATETIME}.csv"
            echo "${EXPORT_EXPORT_CSV_STORED_FILE_PATH}/distributor_master_data_${CURRENT_DATETIME}.csv"
            EXPORT_FILE_SIZE=`du -s ${EXPORT_EXPORT_CSV_STORED_FILE_PATH}/distributor_master_data_${CURRENT_DATETIME}.csv | cut -f1`
            echo "$EXPORT_FILE_SIZE"
         elif [ "$TABLE_NAME" = "drm_uploaded_arn_average_aum_total_commission_data" ]; then
            # query to export data from MySQL table: drm_uploaded_arn_average_aum_total_commission_data

            MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "SELECT IFNULL(aumdata.ARN, '') AS 'ARN', IFNULL(aumdata.arn_avg_aum, '') AS 'ARN Average AUM - Last Reported', IFNULL(aumdata.arn_total_commission, '') AS 'ARN Total Commission - Last Reported', IFNULL(aumdata.arn_yield, '') AS 'ARN Yield', IFNULL(aumdata.arn_business_focus_type, '') AS 'ARN Business Focus Type', IFNULL(CONCAT(DATE_FORMAT(aumdata.created_at, '%d/%m/%Y')), '') AS 'Record Created Date' FROM drm_uploaded_arn_average_aum_total_commission_data AS aumdata ${WHERE_CONDITIONS} ${ORDER_BY_CLAUSE};" | sed 's/\t/","/g;s/^/"/;s/$/"/;s/\n//g' > "${EXPORT_EXPORT_CSV_STORED_FILE_PATH}/consolidated_arn_wise_aum_and_commission_${CURRENT_DATETIME}.csv"
            echo "${EXPORT_EXPORT_CSV_STORED_FILE_PATH}/consolidated_arn_wise_aum_and_commission_${CURRENT_DATETIME}.csv"
            EXPORT_FILE_SIZE=`du -s ${EXPORT_EXPORT_CSV_STORED_FILE_PATH}/consolidated_arn_wise_aum_and_commission_${CURRENT_DATETIME}.csv | cut -f1`
            echo "$EXPORT_FILE_SIZE"
         elif [ "$TABLE_NAME" = "drm_project_focus_amc_wise_details" ]; then
            # query to export data from MySQL table: drm_project_focus_amc_wise_details

            MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "SELECT IFNULL(arnamcdata.ARN, '') AS 'ARN', IFNULL(arnamcdata.amc_name, '') AS 'AMC Name', IFNULL(arnamcdata.total_commission_expenses_paid, '') AS 'Total Commission & Expenses Paid', IFNULL(arnamcdata.gross_inflows, '') AS 'Gross Inflows', IFNULL(arnamcdata.net_inflows, '') AS 'Net Inflows', IFNULL(arnamcdata.avg_aum_for_last_reported_year, '') AS 'Average AUM for Last Reported Year', IFNULL(arnamcdata.closing_aum_for_last_financial_year, '') AS 'Closing AUM at last FY', IFNULL(arnamcdata.effective_yield, '') AS 'Effective Yield', IFNULL(arnamcdata.nature_of_aum, '') AS 'Nature of AUM', IFNULL(arnamcdata.reported_year, '') AS 'Reported Year', IFNULL(CONCAT(DATE_FORMAT(arnamcdata.created_at, '%d/%m/%Y')), '') AS 'Record Created Date' FROM drm_project_focus_amc_wise_details AS arnamcdata ${WHERE_CONDITIONS} ${ORDER_BY_CLAUSE};" | sed 's/\t/","/g;s/^/"/;s/$/"/;s/\n//g' > "${EXPORT_EXPORT_CSV_STORED_FILE_PATH}/AMC_and_ARN_wise_inflows_aum_${CURRENT_DATETIME}.csv"
            echo "${EXPORT_EXPORT_CSV_STORED_FILE_PATH}/AMC_and_ARN_wise_inflows_aum_${CURRENT_DATETIME}.csv"
            EXPORT_FILE_SIZE=`du -s ${EXPORT_EXPORT_CSV_STORED_FILE_PATH}/AMC_and_ARN_wise_inflows_aum_${CURRENT_DATETIME}.csv | cut -f1`
            echo "$EXPORT_FILE_SIZE"
         elif [ "$TABLE_NAME" = "drm_uploaded_arn_distributor_category" ]; then
            # query to export data from MySQL table: drm_uploaded_arn_distributor_category

            MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "SELECT IFNULL(arndistributorcategory.ARN, '') AS 'ARN', IFNULL(arndistributorcategory.distributor_category, '') AS 'Distributor Category', IFNULL(CONCAT(DATE_FORMAT(arndistributorcategory.created_at, '%d/%m/%Y')), '') AS 'Record Created Date' FROM drm_uploaded_arn_distributor_category AS arndistributorcategory ${WHERE_CONDITIONS} ${ORDER_BY_CLAUSE};" | sed 's/\t/","/g;s/^/"/;s/$/"/;s/\n//g' > "${EXPORT_EXPORT_CSV_STORED_FILE_PATH}/arn_wise_distributor_category_${CURRENT_DATETIME}.csv"
            echo "${EXPORT_EXPORT_CSV_STORED_FILE_PATH}/arn_wise_distributor_category_${CURRENT_DATETIME}.csv"
            EXPORT_FILE_SIZE=`du -s ${EXPORT_EXPORT_CSV_STORED_FILE_PATH}/arn_wise_distributor_category_${CURRENT_DATETIME}.csv | cut -f1`
            echo "$EXPORT_FILE_SIZE"
         elif [ "$TABLE_NAME" = "drm_uploaded_arn_project_focus_yes_no" ]; then
            # query to export data from MySQL table: drm_uploaded_arn_project_focus_yes_no

            MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "SELECT IFNULL(arnprojectfocus.ARN, '') AS 'ARN', IFNULL(arnprojectfocus.project_focus, '') AS 'Project Focus', IFNULL(CONCAT(DATE_FORMAT(arnprojectfocus.created_at, '%d/%m/%Y')), '') AS 'Record Created Date' FROM drm_uploaded_arn_project_focus_yes_no AS arnprojectfocus ${WHERE_CONDITIONS} ${ORDER_BY_CLAUSE};" | sed 's/\t/","/g;s/^/"/;s/$/"/;s/\n//g' > "${EXPORT_EXPORT_CSV_STORED_FILE_PATH}/arn_wise_project_focus_data_${CURRENT_DATETIME}.csv"
            echo "${EXPORT_EXPORT_CSV_STORED_FILE_PATH}/arn_wise_project_focus_data_${CURRENT_DATETIME}.csv"
            EXPORT_FILE_SIZE=`du -s ${EXPORT_EXPORT_CSV_STORED_FILE_PATH}/arn_wise_project_focus_data_${CURRENT_DATETIME}.csv | cut -f1`
            echo "$EXPORT_FILE_SIZE"
         elif [ "$TABLE_NAME" = "drm_uploaded_pincode_city_state" ]; then
            # query to export data from MySQL table: drm_uploaded_pincode_city_state

            MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "SELECT IFNULL(pincode.pincode, '') AS 'Pincode', IFNULL(pincode.city, '') AS 'City', IFNULL(pincode.state, '') AS 'State', IFNULL(CONCAT(DATE_FORMAT(pincode.created_at, '%d/%m/%Y')), '') AS 'Record Created Date' FROM drm_uploaded_pincode_city_state AS pincode ${WHERE_CONDITIONS} ${ORDER_BY_CLAUSE};" | sed 's/\t/","/g;s/^/"/;s/$/"/;s/\n//g' > "${EXPORT_EXPORT_CSV_STORED_FILE_PATH}/pincode_master_data_${CURRENT_DATETIME}.csv"
            echo "${EXPORT_EXPORT_CSV_STORED_FILE_PATH}/pincode_master_data_${CURRENT_DATETIME}.csv"
            EXPORT_FILE_SIZE=`du -s ${EXPORT_EXPORT_CSV_STORED_FILE_PATH}/pincode_master_data_${CURRENT_DATETIME}.csv | cut -f1`
            echo "$EXPORT_FILE_SIZE"
         elif [ "$TABLE_NAME" = "drm_uploaded_arn_ind_aum_data" ]; then
            # query to export data from MySQL table: drm_uploaded_arn_ind_aum_data

            MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "SELECT IFNULL(arnindaum.ARN, '') AS 'ARN', IFNULL(arnindaum.total_ind_aum, '') AS 'Total Industry AUM', IFNULL(CONCAT(DATE_FORMAT(arnindaum.ind_aum_as_on_date, '%d/%m/%Y')), '') AS 'AUM as on date', IFNULL(CONCAT(DATE_FORMAT(arnindaum.created_at, '%d/%m/%Y')), '') AS 'Record Created Date' FROM drm_uploaded_arn_ind_aum_data AS arnindaum ${WHERE_CONDITIONS} ${ORDER_BY_CLAUSE};" | sed 's/\t/","/g;s/^/"/;s/$/"/;s/\n//g' > "${EXPORT_EXPORT_CSV_STORED_FILE_PATH}/arn_ind_aum_data_${CURRENT_DATETIME}.csv"
            echo "${EXPORT_EXPORT_CSV_STORED_FILE_PATH}/arn_ind_aum_data_${CURRENT_DATETIME}.csv"
            EXPORT_FILE_SIZE=`du -s ${EXPORT_EXPORT_CSV_STORED_FILE_PATH}/arn_ind_aum_data_${CURRENT_DATETIME}.csv | cut -f1`
            echo "$EXPORT_FILE_SIZE"
         elif [ "$TABLE_NAME" = "drm_uploaded_arn_bdm_mapping" ]; then
            # query to export data from MySQL table: drm_uploaded_arn_bdm_mapping

            MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "SELECT IFNULL(bdmmapping.ARN, '') AS 'ARN', IFNULL(bdmmapping.bdm_email, '') AS 'BDM Email', IFNULL(users.name, '') AS 'BDM Name', IFNULL(bdmmapping.rm_relationship, '') AS 'RM Relationship Flag', IFNULL(CONCAT(DATE_FORMAT(bdmmapping.created_at, '%d/%m/%Y')), '') AS 'Record Created Date' FROM drm_uploaded_arn_bdm_mapping AS bdmmapping LEFT JOIN users ON (bdmmapping.bdm_user_id = users.id) ${WHERE_CONDITIONS} ${ORDER_BY_CLAUSE};" | sed 's/\t/","/g;s/^/"/;s/$/"/;s/\n//g' > "${EXPORT_EXPORT_CSV_STORED_FILE_PATH}/arn_bdm_mapping_data_${CURRENT_DATETIME}.csv"
            echo "${EXPORT_EXPORT_CSV_STORED_FILE_PATH}/arn_bdm_mapping_data_${CURRENT_DATETIME}.csv"
            EXPORT_FILE_SIZE=`du -s ${EXPORT_EXPORT_CSV_STORED_FILE_PATH}/arn_bdm_mapping_data_${CURRENT_DATETIME}.csv | cut -f1`
            echo "$EXPORT_FILE_SIZE"
         elif [ "$TABLE_NAME" = "drm_uploaded_arn_project_emerging_stars_yes_no" ]; then
            # query to export data from MySQL table: drm_uploaded_arn_project_emerging_stars_yes_no

            MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "SELECT IFNULL(arnprojectemerging.ARN, '') AS 'ARN', IFNULL(arnprojectemerging.project_emerging_stars, '') AS 'Project Emerging Stars', IFNULL(CONCAT(DATE_FORMAT(arnprojectemerging.created_at, '%d/%m/%Y')), '') AS 'Record Created Date' FROM drm_uploaded_arn_project_emerging_stars_yes_no AS arnprojectemerging ${WHERE_CONDITIONS} ${ORDER_BY_CLAUSE};" | sed 's/\t/","/g;s/^/"/;s/$/"/;s/\n//g' > "${EXPORT_EXPORT_CSV_STORED_FILE_PATH}/arn_wise_project_emerging_stars_data_${CURRENT_DATETIME}.csv"
            echo "${EXPORT_EXPORT_CSV_STORED_FILE_PATH}/arn_wise_project_emerging_stars_data_${CURRENT_DATETIME}.csv"
            EXPORT_FILE_SIZE=`du -s ${EXPORT_EXPORT_CSV_STORED_FILE_PATH}/arn_wise_project_emerging_stars_data_${CURRENT_DATETIME}.csv | cut -f1`
            echo "$EXPORT_FILE_SIZE"
         elif [ "$TABLE_NAME" = "drm_uploaded_project_green_shoots_yes_no" ]; then
            # query to export data from MySQL table: drm_uploaded_project_green_shoots_yes_no

            MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "SELECT IFNULL(arnprojectgreenshoots.ARN, '') AS 'ARN', IFNULL(arnprojectgreenshoots.project_green_shoots, '') AS 'Project Green Shoots', IFNULL(CONCAT(DATE_FORMAT(arnprojectgreenshoots.created_at, '%d/%m/%Y')), '') AS 'Record Created Date' FROM drm_uploaded_project_green_shoots_yes_no AS arnprojectgreenshoots ${WHERE_CONDITIONS} ${ORDER_BY_CLAUSE};" | sed 's/\t/","/g;s/^/"/;s/$/"/;s/\n//g' > "${EXPORT_EXPORT_CSV_STORED_FILE_PATH}/arn_wise_project_green_shoots_${CURRENT_DATETIME}.csv"
            echo "${EXPORT_EXPORT_CSV_STORED_FILE_PATH}/arn_wise_project_green_shoots_${CURRENT_DATETIME}.csv"
            EXPORT_FILE_SIZE=`du -s ${EXPORT_EXPORT_CSV_STORED_FILE_PATH}/arn_wise_project_green_shoots_${CURRENT_DATETIME}.csv | cut -f1`
            echo "$EXPORT_FILE_SIZE"
         elif [ "$TABLE_NAME" = "drm_uploaded_amfi_city_zone_mapping" ]; then
            # query to export data from MySQL table: drm_uploaded_amfi_city_zone_mapping

            MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "SELECT IFNULL(amficityzones.amfi_city, '') AS 'AMFI City', IFNULL(amficityzones.mapped_zone, '') AS 'Zone', IFNULL(CONCAT(DATE_FORMAT(amficityzones.created_at, '%d/%m/%Y')), '') AS 'Record Created Date' FROM drm_uploaded_amfi_city_zone_mapping AS amficityzones ${WHERE_CONDITIONS} ${ORDER_BY_CLAUSE};" | sed 's/\t/","/g;s/^/"/;s/$/"/;s/\n//g' > "${EXPORT_EXPORT_CSV_STORED_FILE_PATH}/amficity_zone_mapping_${CURRENT_DATETIME}.csv"
            echo "${EXPORT_EXPORT_CSV_STORED_FILE_PATH}/amficity_zone_mapping_${CURRENT_DATETIME}.csv"
            EXPORT_FILE_SIZE=`du -s ${EXPORT_EXPORT_CSV_STORED_FILE_PATH}/amficity_zone_mapping_${CURRENT_DATETIME}.csv | cut -f1`
            echo "$EXPORT_FILE_SIZE"
         elif [ "$TABLE_NAME" = "arn_alternate_details" ]; then
            # query to export data from MySQL table: arn_alternate_details

            MYSQL_PWD="${MYSQL_PASSWORD}" mysql --local-infile=1 --user="${MYSQL_USERNAME}" --host="${MYSQL_DB_HOST}" --database="${MYSQL_DBNAME}" -e "SELECT IFNULL(arnalternatedata.ARN, '') AS 'ARN', IFNULL(arnalternatedata.alternate_mobile_1, '') AS 'Phone 1', IFNULL(arnalternatedata.alternate_mobile_2, '') AS 'Phone 2', IFNULL(arnalternatedata.alternate_mobile_3, '') AS 'Phone 3', IFNULL(arnalternatedata.alternate_mobile_4, '') AS 'Phone 4', IFNULL(arnalternatedata.alternate_mobile_5, '') AS 'Phone 5', IFNULL(arnalternatedata.alternate_email_1, '') AS 'Email 1', IFNULL(arnalternatedata.alternate_email_2, '') AS 'Email 2', IFNULL(arnalternatedata.alternate_email_3, '') AS 'Email 3', IFNULL(arnalternatedata.alternate_email_4, '') AS 'Email 4', IFNULL(arnalternatedata.alternate_email_5, '') AS 'Email 5', IFNULL(CONCAT(DATE_FORMAT(arnalternatedata.created_at, '%d/%m/%Y')), '') AS 'Record Created Date' FROM arn_alternate_details AS arnalternatedata ${WHERE_CONDITIONS} ${ORDER_BY_CLAUSE};" | sed 's/\t/","/g;s/^/"/;s/$/"/;s/\n//g' > "${EXPORT_EXPORT_CSV_STORED_FILE_PATH}/alternate_mobile_email_data_${CURRENT_DATETIME}.csv"
            echo "${EXPORT_EXPORT_CSV_STORED_FILE_PATH}/alternate_mobile_email_data_${CURRENT_DATETIME}.csv"
            EXPORT_FILE_SIZE=`du -s ${EXPORT_EXPORT_CSV_STORED_FILE_PATH}/alternate_mobile_email_data_${CURRENT_DATETIME}.csv | cut -f1`
            echo "$EXPORT_FILE_SIZE"
         fi
         ;;
      \?) # Invalid option
         echo "Error: Invalid option"
         exit;;
   esac
done
