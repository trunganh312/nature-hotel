<?php
if(!empty($page_title) || !empty($page_h1))
	$map_title = empty($page_title) ? $page_h1 : $page_title;
else 
	$map_title = '';
?>
<div class="map-view-popup">
	<div class="container view-map-inner">
		<div class="top-filter">
			<ul>
				<li class="map-popup-title">
					<h3 class="title" data-title="<?=$map_title ?>"><?=$map_title ?></h3>
				</li>
				
				<li class="filter-type">
					<div class="form-extra-field dropdown">
						<button class="btn btn-link dropdown" type="button" id="dropdownMenuHotelType" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						Loại hình <i class="fas fa-chevron-down" aria-hidden="true"></i>
						</button>
						<div class="dropdown-menu st-icheck" aria-labelledby="dropdownMenuHotelType" style="overflow: hidden; outline: none;text-transform: capitalize;" tabindex="4">
							<ul>
								<? foreach ($cfg_hotel_type as $id => $name) : ?>
								<li class="st-icheck-item">
									<label>
										<?=$name ?>
										<input type="checkbox" name="type" value="<?=$id ?>" class="filter-item">
										<span class="checkmark fcheckbox"></span>
									</label>
								</li>
								<? endforeach; ?>
							</ul>
						</div>
					</div>
				</li>
				<li class="filter-star">
					<div class="form-extra-field dropdown">
						<button class="btn btn-link dropdown" type="button" id="dropdownMenuHotelStar" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						Hạng sao <i class="fas fa-chevron-down" aria-hidden="true"></i>
						</button>
						<div class="dropdown-menu st-icheck" aria-labelledby="dropdownMenuHotelStar" style="overflow: hidden; outline: none;" tabindex="4">
							<ul>
								<? for($star = 5;$star > 0; $star--): ?>
								<li class="st-icheck-item">
									<label class="wp-stars">
										<? for($i = 0;$i < $star; $i++): ?>
										<i class="fa fa-star"></i> 
										<? endfor; ?>
										<input type="checkbox" name="star" value="<?=$star ?>" class="filter-item">
										<span class="checkmark fcheckbox"></span>
									</label>
								</li>
								<? endfor; ?>
							</ul>
						</div>
					</div>
				</li>
			</ul>
			<span class="close-map-view-popup">
				<i class="input-icon field-icon fa">
					<svg width="20px" height="20px" viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
						<!-- Generator: Sketch 49 (51002) - http://www.bohemiancoding.com/sketch -->
						<defs></defs>
						<g id="Ico_close" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" stroke-linecap="round" stroke-linejoin="round">
							<g id="Group" stroke="#1A2B48" stroke-width="1.5">
								<g id="close">
									<path d="M0.75,23.249 L23.25,0.749" id="Shape"></path>
									<path d="M23.25,23.249 L0.75,0.749" id="Shape"></path>
								</g>
							</g>
						</g>
					</svg>
				</i>
			</span>
		</div>
		<div class="row page-half-map">
			<div class="col-lg-12 col-md-12 col-right-map col-md-push-12">
				<div style="display: none" class="wp-pacinput"></div>
				<div class="services-grid">
					<div class="box-list"></div>
				</div>
				<div id="map-search-form" class="map-full-height">
					<div class="loader-wrapper" style="display: block;">
		                <div class="st-loader"></div>
		            </div>
				</div>
			</div>
		</div>
	</div>
</div>