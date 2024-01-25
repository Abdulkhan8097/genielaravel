@php
$data_table_headings_html = '';
if(isset($data_table_headings) && is_array($data_table_headings) && count($data_table_headings) > 0){
    foreach($data_table_headings as $key => $value){
        $data_table_headings_html .= '<th data-column="'. $key .'">'. $value['label'] .'</th>';
    }
    unset($key, $value);
}
$url = env('APP_URL').'/user-hierarchy-json';
@endphp

@extends('../layout')
@section('title', 'User Hierarchy')
@section('breadcrumb_heading', 'User Hierarchy')

@section('custom_head_tags')

@endsection

@section('content')

<style type="text/css">
  .chart-container {
  font-family: Arial;
  height: 320px;
  max-width: 1110px;
  border-radius: 5px;
  overflow: auto;
  text-align: center;
}
.orgchart .node {
  width: auto !important;
}
</style>

<div class="row mt-4">
    <div class="col-lg-12">
      	<div class="mt-2">
          	<div class="row">
              	<div class="col-lg-12">
              		<div class="row form-inline">
              			<div class="col-md-12 col-sm-12">
		            		<table id="userInactiveList" class="display" style="width:100%">
		                		<thead>
				                    <tr>
				                        @php
				                            echo $data_table_headings_html;
				                        @endphp
				                    </tr>
		                		</thead>
				                <tbody>				                    
				                      @foreach($inactiveUsersList as $userData)
				                        <tr>
                                  <td>{{$userData['name']}}</td>
				                          <td>{{$userData['positionName']}}</td>
				                          <td>{{$userData['status']}}</td>
                                </tr>
				                      @endforeach
				                </tbody>
				                <tfoot>
				                    <tr>
				                        @php
				                            echo $data_table_headings_html;
				                        @endphp
				                    </tr>
				                </tfoot>
		            		</table>
		        		</div>
		    		</div>
				</div>
				<div class="col-lg-12">
					<br>
				</div>
				<div class="col-lg-12">
					<div class="chart-container" style="height:auto;margin-bottom: 20px;"></div><!-- background-color : #eeeeee;  -->
          <button id="zoom_in">+</button>
          <button id="zoom_out">-</button>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection

@section('custom_scripts')
<script src="https://d3js.org/d3.v7.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/d3-org-chart@2"></script>
<script src="https://cdn.jsdelivr.net/npm/d3-flextree@2.1.2/build/d3-flextree.js"></script>

<script>
  'use strict';
	 var jsonURL = '<?php echo $userHierarchyJsonURL ?>';
      $('#userInactiveList').DataTable({
        "searching": false,
        "scrollX": true,
        "language": {
          "oPaginate": {
              "sNext": '<i class="icons angle-right"></i>',
              "sPrevious": '<i class="icons angle-left"></i>',
              "sFirst": '<i class="icons step-backward"></i>',
              "sLast": '<i class="icons step-forward"></i>'
          }
        },"order": [[ 0, 'asc' ]]
      });
      let inactive_parent = [];
      var chart;
      d3.json(
        jsonURL
      ).then(data => {
        //console.log(data);
        chart = new d3.OrgChart()
          .container('.chart-container')
          .data(data)
          .rootMargin(50)
          .nodeWidth((d) => 260)
          .initialZoom(0.7)
          .nodeHeight((d) => 140)
          .childrenMargin((d) => 50)
          .compactMarginBetween((d) => 50)
          .compactMarginPair((d) => 50)
          .linkUpdate(function (d, i, arr) {
            //console.log('data ===> ', d.data.id);
            if(d.data.parentStatus == 'Inactive'){
              console.log('data ===> ', d.data.id);
              console.log('totalSubordinates =====> ', d.data._totalSubordinates);
              console.log('directSubordinates =====> ', d.data._directSubordinates);
              if(d.data._directSubordinates == 0 && d.data._totalSubordinates == 0 ){
                  d3.select(this).remove();
              }
              else{
                inactive_parent.push(d.data.id);
              }
            }
            //console.log('inactive_parent =====> ', inactive_parent);

            let uniqueChars = inactive_parent.filter((c, index) => {
              return inactive_parent.indexOf(c) === index;
            });

            console.log('unique inactive_parent 2222 ===> ', uniqueChars);
            

            d3.select(this)
              .attr('stroke', (d) =>
                d.data._upToTheRootHighlighted ? '#152785' : 'lightgray'
              )
              .attr('stroke-width', (d) =>
                d.data._upToTheRootHighlighted ? 5 : 1.5
              )
              .attr('stroke-dasharray', '4,4');

            if (d.data._upToTheRootHighlighted) {
              d3.select(this).raise();
            }
          })
          .nodeContent(function (d, i, arr, state) {
            //console.log('state====> ', state);
            const colors = [
              '#6E6B6F',
              '#18A8B6',
              '#3962ba',
              '#96C62C',
              '#BD7E16',
              '#802F74',
            ];
            var color;
            //color = colors[d.depth % colors.length];
            color = '#18A8B6';
            const imageDim = 80;
            const lightCircleDim = 95;
            const outsideCircleDim = 130;
            var parentStatus, status  = '';

            if(d.data.parentStatus == 'Inactive'){
                color = '#F45754';
                parentStatus = ", "+d.data.parentStatus; 

                if(d.data._directSubordinates == 0 && d.data._totalSubordinates == 0 ){
                    d3.select(this).remove();
                }
            }
            else{
              status = '';
              parentStatus = '';
            }

            return `
              <div style="position:absolute;width:${d.width}px;">
                    <div class="card" style="top:${outsideCircleDim / 2 + 10}px;position:absolute;height:56px;width:${d.width}px;background-color:#3AB6E3;">
                        <div style="background-color:${color};height:28px;text-align:center;padding-top:10px;color:#ffffff;font-weight:bold;font-size:16px">
                          	${d.data.name} ${parentStatus}
                        </div>
                        <div style="background-color:#F0EDEF;height:28px;text-align:center;padding-top:10px;color:#424142;font-size:16px">
                            ${d.data.positionName} 
                        </div>
                    </div>
                </div>`;
          })
          .render();

          var currentZoom = parseFloat($('.chart-container').css('zoom'));

          $('#zoom_out').on('click', function () {
            $('.chart-container').css('zoom', currentZoom -= 0.1);
            $('.chart-container').css('width', '100%');
          });

          $('#zoom_in').on('click', function () {
            $('.chart-container').css('zoom', currentZoom += 0.1);
          });
      });
    </script>
@endsection
