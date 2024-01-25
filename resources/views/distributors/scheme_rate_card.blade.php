<?php
//$category_array=array('','CORE','CORE PLUS','PRIME','PRIME PLUS','ELITE','ELITE PLUS');
?>
<?php /*<div class="m-portlet__head-caption">
							<div class="m-portlet__head-title">
								<center><h5 class="m-portlet__head-text">
									Selected Month Group:<?php echo $category_array[$category];?>
								</h5><center>
							</div>
						</div>*/ ?>
<table class="table data-inputs stripe" id="panel_commission">
    <thead>
        <?php if ($gst == 1) { ?>
            <tr>
                <th></th>
                <th></th>
                <th colspan="2">Gross Rate Card</th>

                <th colspan="3">Net Rate Card</th>
            </tr><?php } ?>
        <tr>
            <th>Id</th>
            <th>Scheme Name</th>
            <?php if ($gst == 1) { ?>
                <th>Trail</th>
                <th>Additional Trail / B30</th>
            <?php } ?>
            <th>Trail</th>
            <th>Additional Trail / B30</th>

            <th>From - TO</th>

        </tr>
    </thead>
    <tbody>
        <?php
        if (!empty($limitdata)) {
            $i = 1;
            foreach ($limitdata as $data) {
                switch ($category) {
                    case 1:
                        $category_rating = "Core";
                        break;
                    case 2:
                        $category_rating = "Core Plus";
                        break;
                    case 3:
                        $category_rating = "Prime";
                        break;
                    case 4:
                        $category_rating = "Prime Plus";
                        break;
                    case 5:
                        $category_rating = "Elite";
                        break;
                    case 6:
                        $category_rating = "Elite Plus";
                        break;
                    default:
                        $category_rating = "- - ";
                }
                if (strtolower(trim($data->scheme_name)) != strtolower(trim('SAMCO FLEXI CAP FUND'))) {
                    $t30_trail_1st_year = $data->t30_trail_1st_year;
                    $gstw_trail = ($t30_trail_1st_year / 118) * 100;

                    $additional_incentive_b30 = $data->additional_incentive_b30;
                    $gstw_adtril = ($additional_incentive_b30 / 118) * 100;
                    $add_samco_trail_t30 = ($data->samco_trail_t30 * $data->profit_share_t30) / 100;
                    $samco_trail_addtional = ($data->samco_trail_b30 * 82) / 100;
                } else {
                    $t30_trail_1st_year = $data->samco_trail_t30;
                    $gstw_trail = $t30_trail_1st_year;

                    $additional_incentive_b30 = $data->samco_trail_b30;
                    $gstw_adtril = $additional_incentive_b30;
                    $add_samco_trail_t30 = $data->samco_trail_t30;
                    $samco_trail_addtional = $data->samco_trail_b30;
                }
                if ($gst == 1) {
                    echo '
						<tr>
						<td>' . $i . '</td>
						
						<td>' . $data->scheme_name . '</td>
		                
		                <td>' . sprintf('%0.2f', $add_samco_trail_t30) . '</td>
						<td>' . sprintf('%0.2f', $samco_trail_addtional) . '</td>

						<td>' . sprintf('%0.2f', $gstw_trail) . '</td>
						<td>' . sprintf('%0.2f', $gstw_adtril) . '</td>
						<td>' . $data->from_date . ' - ' . ' ' . $data->to_date . '</td>
						
						</tr>
						';
                } else {
                    echo '
						<tr>
						<td>' . $i . '</td>
						
						<td>' . $data->scheme_name . '</td>
						<td>' . sprintf('%0.2f', $gstw_trail) . '</td>
						<td>' . sprintf('%0.2f', $gstw_adtril) . '</td>
						<td>' . $data->from_date . ' - ' . ' ' . $data->to_date . '</td>
						
						</tr>
						';
                }
                // <td>'.$data->mutual_fund_house.'</td>
                // <td>'.$data->rate_category.'</td>
                // <td>'.sprintf('%0.2f',$data->additional_incentive_b30).'</td>
                //<td>'.sprintf('%0.2f',$gstw_trail).'</td>
                $i++;
            }
        }

        ?>
    </tbody>
</table>

<style>
    .m-widget11__sub {
        display: inline-block !important;
        background: #3a81e0 !important;
        padding: 5px 20px !important;
        color: #fff !important;
        border-radius: 30px !important;
        line-height: normal !important;
    }
</style>