@php
	// menu list array  is present in "config/menulist.php" file
	$arr_menus = config('menulist');

	// arr_merged_vars stores list of variables which needs to be replaced with either MENU LINK or MENU TEXT
	$arr_merged_vars['appointment_url'] = 'javascript:void(0);';
	if(isset($logged_in_user_email) && !empty($logged_in_user_email)){
		// appointment autologin url
		$arr_merged_vars['appointment_url'] = env('SAMCOMF_APPOINTMENT_URL').'/Appointments/auto_login/'. md5($logged_in_user_email);
	}
@endphp
<ul>
	@foreach($arr_menus as $key => $menu)
	@if($menu['shown_in_menu'])
		@php
			$arr_extra_permission_links = array();
			$arr_extra_permissions_related_links = array();
			if(isset($menu['extra_permissions']) && is_array($menu['extra_permissions'])){
				$arr_extra_permission_links = array_column($menu['extra_permissions'], 'link');
				array_walk($arr_extra_permission_links, function(&$_value){
					$_value = URL::to($_value);
				});
				$arr_extra_permission_links = array_unique(array_filter($arr_extra_permission_links));

				$arr_extra_permissions_related_links = array_column($menu['extra_permissions'], 'related_link');
				array_walk($arr_extra_permissions_related_links, function(&$_value){
					$_value = URL::to($_value);
				});
				$arr_extra_permissions_related_links = array_unique(array_filter($arr_extra_permissions_related_links));
			}
			
			// checking whether currently logged in user have access to looping menu route or not
			$flag_menu_accessible = false;
			if((isset($menu['skip_permission']) && $menu['skip_permission']) || (isset($menu['no_url']) && $menu['no_url'])){
				$flag_menu_accessible = true;
			}
			else{
				if(isset($logged_in_user_roles_and_permissions['role_details']) && isset($logged_in_user_roles_and_permissions['role_details']['have_all_permissions']) && (intval($logged_in_user_roles_and_permissions['role_details']['have_all_permissions']) == 1)){
					$flag_menu_accessible = true;
				}
				elseif(isset($logged_in_user_roles_and_permissions['role_permissions']) && is_array($logged_in_user_roles_and_permissions['role_permissions']) && in_array(trim($menu['link'], '/'), $logged_in_user_roles_and_permissions['role_permissions']) !== FALSE){
					$flag_menu_accessible = true;
				}
			}
			if(!$flag_menu_accessible){
				if(isset($menu['parent_stop']) && $menu['parent_stop']){
					echo '</ul>';
				}
				// skipping the current menu loop as menu accessible flag remains FALSE
				continue;
			}

			$append_anchor_target_tag = '';
			if(isset($menu['anchor_target']) && !empty($menu['anchor_target'])){
				$append_anchor_target_tag = ' target="'. $menu['anchor_target'] .'"';
			}

			// storing anchor tag link and checking if any MERGE CODE is present in it or not
			// if MERGE CODE is present then retrieving it's details from variable $arr_merged_vars
			$anchor_link = $menu['link'];
			if(isset($menu['merge_code_in_anchor_link']) && $menu['merge_code_in_anchor_link']){
				$arr_menu_link_text = explode('/', $anchor_link);
				array_walk($arr_menu_link_text, function(&$_link_part) use($arr_merged_vars){
					if(stripos($_link_part, '{') !== FALSE && stripos($_link_part, '}') !== FALSE){
						$new_link_part = str_replace(array('{', '}'), '', $_link_part);
						if(isset($arr_merged_vars[$new_link_part]) && !empty($arr_merged_vars[$new_link_part])){
							$_link_part = $arr_merged_vars[$new_link_part];
						}
						unset($new_link_part);
					}
				});
				$anchor_link = trim(implode('/', $arr_menu_link_text),'/');
				unset($arr_menu_link_text);
			}
			else{
				// adding base url to partial link which we fetch from "config/menulist.php" file
				$anchor_link = URL::to($anchor_link);
			}
			unset($flag_menu_accessible);
		@endphp
	<li class="{{(!empty($current_page_route_url) && ($current_page_route_url == URL::to($menu['link']) || (count($arr_extra_permission_links) > 0 && in_array($current_page_route_url, $arr_extra_permission_links) !== FALSE && in_array(URL::to($menu['link']), $arr_extra_permissions_related_links) !== FALSE)))?'active':''}}">
		<a href="{{(!isset($menu['no_url']) || (isset($menu['no_url']) && !$menu['no_url']))?$anchor_link:$menu['link']}}" class="{{$menu['extra_class']??''}}" {{$append_anchor_target_tag}}><span>{{$menu['text']}}</span></a>
	@if(!isset($menu['parent_start']) || (isset($menu['parent_stop']) && $menu['parent_stop']))
		@if(isset($menu['parent_stop']) && $menu['parent_stop'])
		</ul>
		@endif
	</li>
	@elseif(isset($menu['parent_start']) && $menu['parent_start'])
		<ul class="submenu">
	@endif
	@endif
	@endforeach
</ul>
