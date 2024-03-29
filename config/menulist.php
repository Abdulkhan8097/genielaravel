<?php 
    /*
    |-----------------------------------------------------------------------------------------------------------------------------
    | Custom file created for storing list of menus to be displayed in left sidebar & also used for showing it in permission list
    | Parameters description:
    | a) link: page url
    | b) icon: used for showing it left sidebar
    | c) text: link text
    | d) shown_in_menu: parameter helps to decide whether it will be shown in left sidebar or not. possible values are TRUE/FALSE
    | e) shown_in_permission: parameter helps to decide whether it will be shown in permission or not. possible values are TRUE/FALSE
    | f) parent_start: helps to mark a menu as PARENT menu and it will include subsequent menus after it as SUBMENUS until parameter parent_stop is not found
    | g) parent_stop: helps to mark a menu as last submenu of PARENT menu
    | h) extra_class: helps to include list of classes(multiple values separated by SPACE) against a menu item if you wish to add
    | i) no_url: helps to identify whether to add URL::to function or not against this menu in left sidebar. possible values are TRUE/FALSE
    | j) extra_permissions: it's an array of elements which helps to add more entries in permission page.E.G:add/edit/export etc.
    |    possible keys in "extra_permissions" can be "link", "text" & "related_link". Here related_link is used to add ACTIVE class against the value mentioned URL available in the LEFT SIDEBAR MENU.
    | k) anchor_target: helps to create an anchor tag with TARGET attribute
    | l) merge_code_in_anchor_link: helps to create anchor link having MERGE CODE in it, which can be replace while showing menus in left sidebar
    | m) skip_permission: helps to dispaly menu irrespective of checking permission against it
    |-----------------------------------------------------------------------------------------------------------------------------
    */
    return array(
                array('link' => '/',
                      'icon' => 'icons la-home',
                      'text' => 'Dashboard',
                      'shown_in_menu' => true,
                      'skip_permission' => true),
                array('link' => '/distributorslist',
                      'icon' => 'icons la-user',
                      'text' => 'Distributor master',
                      'shown_in_menu' => true,
                      'extra_permissions' => array(array('link' => '/distributor/{arn_number}', 'text' => 'View distributor', 'related_link' => '/distributorslist'),
                                                   array('link' => '/distributor/UpdateByArn', 'text' => 'Update distributor details'),
                                                   array('link' => '/distributor_exportToCSV', 'text' => 'Export ARN & AMC wise data'),
                                                   array('link' => '/distributor/auto-assign-bdm/{arn_number}', 'text' => 'Assign BDM user'),
                                                   array('link' => '/commission_exportToCSV', 'text' => 'Export Commission Structure'),
                                                   array('link' => '/edit-commission-detail', 'text' => 'Edit Commission Structure'),
                                                   array('link' => '/commission-update', 'text' => 'Update Commission Structure'),
                                                   array('link' => '/report-of-aum-transaction-analytics', 'text' => 'AUM & Transaction Analytics'),
                                                   array('link' => '/report-of-sip-analytics', 'text' => 'SIP Analytics'),
                                                   array('link' => '/report-of-client-analytics', 'text' => 'Client Analytics'),
                                                   array('link' => '/report-of-client-monthwise-analytics', 'text' => 'Client Analytics Month Wise'),
                                                   array('link' => '/export-aum-transaction-analytics', 'text' => 'Export AUM Transaction Analytics'),
                                                   array('link' => '/export-sip-analytics', 'text' => 'Export SIP Analytics'),
                                                   array('link' => '/export-client-analytics', 'text' => 'Export Client Analytics'),
                                                ),
                      'shown_in_permission' => true),
                array('link' => '/search-arn',
                      'icon' => 'icons la-landmark',
                      'text' => 'Search ARN',
                      'shown_in_menu' => true,
                      'shown_in_permission' => false,
                      'skip_permission' => true),
                array('link' => '/upload',
                      'icon' => 'icons la-landmark',
                      'text' => 'Upload files',
                      'shown_in_menu' => true,
                      'extra_permissions' => array(array('link' => 'save_uploaded_data', 'text' => 'Save uploaded file')
                                                ),
                      'shown_in_permission' => true),
                array('link' => 'javascript:void(0);',
                      'icon' => 'icons la-user',
                      'text' => 'Masters',
                      'extra_class' => 'nav-treeview',
                      'no_url' => true,
                      'shown_in_menu' => true,
                      'parent_start' => true),
                array('link' => '/usermasterlist',
                      'icon' => 'icons la-user',
                      'text' => 'User management',
                      'shown_in_menu' => true,
                      'extra_permissions' => array(array('link' => '/add-user', 'text' => 'Create a new user'),
                                                   array('link' => '/edit-detail', 'text' => 'Get editing user details'),
                                                   array('link' => '/usermaster-update', 'text' => 'Update an editing user details'),
                                                   array('link' => '/services-pincode', 'text' => 'View servicable pincodes'),
                                                   array('link' => '/show-all-users', 'text' => 'Show all user details'),
                                                ),
                      'shown_in_permission' => true),
				array('link' => '/arntransfer',
					'icon' => 'icons la-user',
					'text' => 'ARN Transfer',
					'shown_in_menu' => true,
					'extra_permissions' => array(
						array('link' => '/arntransfer/getarn', 'text' => 'ARN Transfer ( Ajax to get selected list )', 'related_link' => '/arntransfer'),
						array('link' => '/arntransfer/transferarn', 'text' => 'ARN Transfer ( Ajax to transfer selected list )', 'related_link' => '/arntransfer')
					),
					'shown_in_permission' => true),
                array('link' => '/aumdatalist',
                      'icon' => 'icons la-user',
                      'text' => 'AUM data',
                      'shown_in_menu' => true,
                      'shown_in_permission' => true),
                array('link' => '/arnamcwisedata',
                      'icon' => 'icons la-user',
                      'text' => 'ARN & AMC wise data',
                      'shown_in_menu' => true,
                      'shown_in_permission' => true),
                array('link' => '/arndistributorcategorydata',
                      'icon' => 'icons la-user',
                      'text' => 'ARN wise distributor category data',
                      'shown_in_menu' => true,
                      'shown_in_permission' => true),
                array('link' => '/arnprojectfocus',
                      'icon' => 'icons la-user',
                      'text' => 'ARN wise project focus data',
                      'shown_in_menu' => true,
                      'shown_in_permission' => true),
                array('link' => '/pincodelist',
                      'icon' => 'icons la-user',
                      'text' => 'Pincode master',
                      'shown_in_menu' => true,
                      'shown_in_permission' => true),
                array('link' => '/arnindaumdata',
                      'icon' => 'icons la-user',
                      'text' => 'ARN wise industry AUM',
                      'shown_in_menu' => true,
                      'shown_in_permission' => true),
                array('link' => '/arnbdmmapping',
                      'icon' => 'icons la-user',
                      'text' => 'ARN wise BDM mapping',
                      'shown_in_menu' => true,
                      'shown_in_permission' => true),
                array('link' => '/arnprojectemergingdata',
                      'icon' => 'icons la-user',
                      'text' => 'ARN wise project emerging stars',
                      'shown_in_menu' => true,
                      'shown_in_permission' => true),
                array('link' => '/arnprojectgreenshoots',
                      'icon' => 'icons la-user',
                      'text' => 'ARN wise project green shoots',
                      'shown_in_menu' => true,
                      'shown_in_permission' => true),
                array('link' => '/amficityzone',
                      'icon' => 'icons la-user',
                      'text' => 'AMFI city zones list',
                      'shown_in_menu' => true,
                      'shown_in_permission' => true),
				array('link' => '/arnalternatedata',
					'icon' => 'icons la-user',
					'text' => 'ARN wise alternate mobile and email data',
					'shown_in_menu' => true,
					'shown_in_permission' => true),
				array('link' => '/goal',
					'icon' => 'icons la-user',
					'text' => 'Target Goal',
					'shown_in_menu' => true,
					'shown_in_permission' => true),
                array('link' => '/roles',
                      'icon' => 'icons la-user',
                      'text' => 'Role master',
                      'shown_in_menu' => true,
                      'shown_in_permission' => true,
                      'extra_permissions' => array(array('link' => '/roles/addedit', 'text' => 'Add a role', 'related_link' => '/roles'),
                                                   array('link' => '/roles/addedit/{role_id?}', 'text' => 'Edit a role', 'related_link' => '/roles')
                                                ),
                      'parent_stop' => true),
				array('link' => 'javascript:void(0);',
					'icon' => 'icons la-user',
					'text' => 'Meeting Details',
					'extra_class' => 'nav-treeview',
					'no_url' => true,
					'shown_in_menu' => true,
					'parent_start' => true),
				array('link' => '/meetinglog',
					'icon' => 'icons la-user',
					'text' => 'Meeting log',
					'shown_in_menu' => true,
					'extra_permissions' => array(array('link' => '/meetinglog/create/{arn_number}', 'text' => 'Add meeting page', 'related_link' => '/meetinglog'),
												array('link' => '/save_meeting_data', 'text' => 'Save meeting data'),
												array('link' => '/update_meeting_data', 'text' => 'Update meeting data'),
												array('link' => '/view-detail', 'text' => 'View meeting data'),
												array('link' => '/meeting-feedback-notification', 'text' => 'Send feedback notification'),
												array('link' => '/meetinglog/edit/{logID}', 'text' => 'Edit meeting page'),
											),
					'shown_in_permission' => true),
				array('link' => '/reimbursement',
					'icon' => 'icons la-user',
					'text' => 'Reimbursement',
					'shown_in_menu' => true,
					'extra_permissions' => array(
						array(
							'link' => '/reimbursement/add',
							'text' => 'ADD Reimbursement',
							'related_link' => '/reimbursement'
						),
						array(
							'link' => 'reimbursement/{logid?}',
							'text' => 'Edit Reimbursement',
							'related_link' => '/reimbursement'
						),
						array(
							'link' => '/reimbursement/list',
							'text' => 'Fetch Reimbursement list',
							'related_link' => '/reimbursement'
						),
						array(
							'link' => '/reimbursement/expense_list',
							'text' => 'Fetch Reimbursement expense list',
							'related_link' => '/reimbursement'
						),
						array(
							'link' => '/reimbursement/addRemark',
							'text' => 'Add Reimbursement remark',
							'related_link' => '/reimbursement'
						),
						array(
							'link' => '/reimbursement/status',
							'text' => 'Set Reimbursement status',
							'related_link' => '/reimbursement'
						),
						array(
							'link' => '/reimbursement/getstatus',
							'text' => 'Refresh Reimbursement status',
							'related_link' => '/reimbursement'
						),
						array(
							'link' => '/reimbursement/{logid?}',
							'text' => 'Edit Reimbursement',
							'related_link' => '/reimbursement'
						)
					),
					'shown_in_permission' => true),
				array('link' => '/bdm-meeting-dashboard',
					'icon' => 'icons la-user',
					'text' => 'BDM Analysis Reports',
					'shown_in_menu' => true,
					'parent_stop' => true,
					'extra_permissions' => array(array('link' => '/download-detail-BDM/{bdm_id}/{type}', 'text' => 'download dbm analytics ( Ajax )', 'related_link' => '/bdm-meeting-dashboard'),
					array('link' => '/view-detail-BDM', 'text' => 'BDM Analysis Reports ( Ajax to get Analysis Reports )', 'related_link' => '/bdm-meeting-dashboard')),
					'shown_in_permission' => true),
                array('link' => '/{appointment_url}',
                      'icon' => 'icons la-user',
                      'text' => 'Appointment',
                      'anchor_target' => '_blank',
                      'merge_code_in_anchor_link' => true,
                      'shown_in_menu' => true,
                      'shown_in_permission' => true),
                array('link' => 'javascript:void(0);',
                      'icon' => 'icons la-user',
                      'text' => 'Reports',
                      'extra_class' => 'nav-treeview',
                      'no_url' => true,
                      'shown_in_menu' => true,
                      'parent_start' => true),
                array('link' => '/report-of-project-focus-partner',
                      'icon' => 'icons la-user',
                      'text' => 'Project Focus Partner',
                      'shown_in_menu' => true,
                      'shown_in_permission' => true),
                  array('link' => '/report-of-project-emerge-partner',
                      'icon' => 'icons la-user',
                      'text' => 'Project Emerge Partner',
                      'shown_in_menu' => true,
                      'shown_in_permission' => true),
                  array('link' => '/report-of-partner-aum-no-transactions',
                      'icon' => 'icons la-user',
                      'text' => 'Partner With AUM No Transaction',
                      'shown_in_menu' => true,
                      'shown_in_permission' => true),
                  array('link' => '/report-of-partner-aum-no-active-sip',
                      'icon' => 'icons la-user',
                      'text' => 'Partner With AUM No Active SIP',
                      'shown_in_menu' => true,
                      'shown_in_permission' => true),
                array('link' => '/report-of-partner-aum-unique-client',
                      'icon' => 'icons la-user',
                      'text' => 'Partner With AUM Unique Client',
                      'shown_in_menu' => true,
                      'shown_in_permission' => true),
                array('link' => '/report-of-daywise-transaction-analytics',
                      'icon' => 'icons la-user',
                      'text' => 'Daywise Transaction Analytics',
                      'shown_in_menu' => true,
                      'shown_in_permission' => true),
                array('link' => '/report-of-monthwise-bdmwise-inflows',
                      'icon' => 'icons la-user',
                      'text' => 'Month-wise BDM wise Flows',
                      'shown_in_menu' => true,
                      'shown_in_permission' => true),
                array('link' => '/MasterSipStpTransactionReport',
                      'icon' => 'icons la-user',
                      'text' => 'SIP/STP registration data',
                      'shown_in_menu' => true,
                      'extra_permissions' => array(array('link' => '/MasterSipStpTransactionReport/Detailed', 'text' => 'SIP/STP Page Detailed Reports Tab'),
                                                   array('link' => '/getPredefinedSipStpReport', 'text' => 'SIP/STP Page Predefiend Reports Tab')
                                                ),                      
                      'shown_in_permission' => true,
                      'parent_stop' => true,
                  ),
                array('link' => '/user-hierarchy',
                      'icon' => 'icons la-user',
                      'text' => 'User Hierarchy',
                      'shown_in_menu' => true,
                      'shown_in_permission' => true
                ),
                array('link' => '/download-nse-details',
                      'icon' => 'icons la-user',
                      'text' => 'NSE Free Float Market Cap Data',
                      'shown_in_menu' => true,
                      'shown_in_permission' => true),
                array('link' => '/mos_multiplier_data',
                      'icon' => 'icons la-user',
                      'text' => 'Mos Multiplier Data',
                      'shown_in_menu' => true,
                      'extra_permissions' => array(
                                                    array('link' => '/mos_multiplier_data_add', 'text' => 'Add mos multiplier data '),
                                                    array('link' => '/mos_multiplier_data_edit/{role_id?}', 'text' => 'Edit mos multiplier data '),
                                                    array('link' => '/mos_multiplier_data_delete/{role_id?}', 'text' => 'Delete mos multiplier data ')
                                                ),
                      'shown_in_permission' => true),
                array('link' => 'javascript:void(0);',
                      'icon' => 'icons la-user',
                      'text' => 'EMOSI Timer STP/SIP',
                      'extra_class' => 'nav-treeview',
                      'no_url' => true,
                      'shown_in_menu' => true,
                      'parent_start' => true),
                array('link' => '/booster-stp-sip',
                      'icon' => 'icons la-user',
                      'text' => 'Backtest Result',
                      'shown_in_menu' => true,
                      'shown_in_permission' => true),
                array('link' => '/emosi-data',
                      'icon' => 'icons la-user',
                      'text' => 'EMOSI Data',
                      'shown_in_menu' => true,
                      'shown_in_permission' => true,
                      'parent_stop' => true),
				array('link' => 'javascript:void(0);',
                      'icon' => 'icons la-user',
                      'text' => 'Inter AMC Switch ',
                      'extra_class' => 'nav-treeview',
                      'no_url' => true,
                      'shown_in_menu' => true,
                      'parent_start' => true),
						array(
									'link' => '/InterAMCSwitch',
									'icon' => 'icons la-user',
									'text' => 'Inter Switch Schemes',
									'shown_in_menu' => true,
									'shown_in_permission' => true,
									'extra_permissions' => array(
										array('link' => '/InterAMCSwitch/api/{api_url}', 'text' => 'Inter Switch Schemes ( Ajax )'),
									)
							),
                        array(
                              'link' => '/pending-interswitch-mis',
                              'icon' => 'icons la-user',
                              'text' => 'Pending Interswitch MIS',
                              'shown_in_menu' => true,
                              'shown_in_permission' => true,
                        ),
                        array(
                              'link' => '/mis',
                              'icon' => 'icons la-user',
                              'text' => 'Interswitch MIS',
                              'shown_in_menu' => true,
                              'shown_in_permission' => true,
							  'extra_permissions' => array(
									array('link' => '/get-mis-data', 'text' => 'Interswitch MIS ( Ajax )'),
									array('link' => '/ajax-unlink-file', 'text' => 'Interswitch MIS ( Ajax to unlink generated file )'),
														),
                        ),
                        array(
                              'link' => '/auto-switch-orders',
                              'icon' => 'icons la-user',
                              'text' => 'Auto Switch MIS',
                              'shown_in_menu' => true,
                              'shown_in_permission' => true,
                              'parent_stop' => true
				),
				array('link' => 'deletedusers',
                      'icon' => 'icons la-user',
                      'text' => 'Deleted Users',
                      'extra_class' => 'nav-treeview',
                      'shown_in_menu' => true),
				array('link' => 'oldusers',
					'icon' => 'icons la-user',
					'text' => 'Old Users',
					'extra_class' => 'nav-treeview',
					'shown_in_menu' => true)
						
            /*    array('link' => '/mis',
                      'icon' => 'icons la-user',
                      'text' => 'MIS',
                      'shown_in_menu' => true,
                      'shown_in_permission' => true,
                      'parent_stop' => true), */
            );
