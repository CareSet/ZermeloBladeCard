<div class="container-fluid">
	<div>
		<h1> {{ $report->GetReportName()  }}</h1>
	</div>
	<div>
		{!! $report->GetReportDescription() !!}
	</div>

	<div style='display: none' id='json_error_message' class="alert alert-danger" role="alert"></div>
	<div style='display: none' id='json_info_message' class="alert alert-info" role="alert"></div>

		@if ($report->is_fluid())
			<div class='container-fluid'>
				@else
					<div class='container'>
						@endif

						<div id='div_for_cards'>





						</div>



					</div>


					<div id="bottom_locator" style="
    position: fixed;
    bottom: 10px;
"></div>


					<script type="text/javascript" src="/vendor/CareSet/zermelobladecard/js/jquery-3.4.1.min.js"></script>
					<script type="text/javascript" src="/vendor/CareSet/zermelobladecard/js/popper.min.js"></script>
					<script type="text/javascript" src="/vendor/CareSet/zermelobladecard/js/bootstrap.min.js"></script>
					<script type="text/javascript" src="/vendor/CareSet/zermelobladecard/js/datatables.min.js"></script>
					<script type="text/javascript" src="/vendor/CareSet/zermelobladecard/js/jquery.dataTables.yadcf.js"></script>
					<script type="text/javascript" src="/vendor/CareSet/zermelobladecard/js/moment.min.js"></script>
					<script type="text/javascript" src="/vendor/CareSet/zermelobladecard/js/daterangepicker.js"></script>
					<script type="text/javascript" src="/vendor/CareSet/zermelobladecard/js/d3.v4.min.js"></script>
					<script type="text/javascript" src="/vendor/CareSet/zermelobladecard/js/datatables.fixedcolumns.destroy.js"></script>
					<script type="text/javascript" src="/vendor/CareSet/zermelobladecard/js/jquery.doubleScroll.js"></script>

					<script type="text/javascript">



						function doh_ajax_failed(jqxhr, textStatus, error){

							var is_admin = true; //this should be set via a call to the report

							if(is_admin){
								if(typeof jqxhr.responseJSON.message !== 'undefined'){
									$('#json_error_message').html("<h1> You had a error </h1> <p> " + jqxhr.responseJSON.message + "</p>");
								}else{
									$('#json_error_message').html("<h1> You had a error, bad enough that there was no JSON  </h1>");
								}
							}else{
								$('#json_error_message').html("<h1> There was an error generating this report</h1>");
							}
							$('#json_error_message').show();

						}



						$(function() {

							var columnMap = [];
							var fixedColumns = null;

							$.getJSON('{{ $summary_uri }}',
									{
										'token': '{{ $report->getToken() }}',
										'request-form-input': '{!! urlencode($report->getRequestFormInput(true)) !!}',
									}).fail(function(jqxhr, textStatus, error) {
								doh_ajax_failed(jqxhr, textStatus, error);
							})
									.done(function(header_data) { //this means I have clean results in the data variable...

										var columns = header_data.columns;
										var order = header_data.order;
										var searches = [];



										/*
                                            Support multi column ordering
                                        */
										var callbackOrder = [];

										var passthrough_params = {!! $report->getRequestFormInput( true ) !!};
										var merge_get_params = {
											'token': '{{ $report->getToken() }}',
											'page': (header_data.start / header_data.length) + 1,
											"order": callbackOrder,
											"length": header_data.length,
											"filter": searches,
										};
										var merge = $.extend({}, passthrough_params, merge_get_params)
										localStorage.setItem("Zermelo_defaultPlageLength",header_data.length);

										var merge_clone = $.extend({},merge);
										delete merge_clone['token'];

										var param = decodeURIComponent( $.param(merge) );

										//now lets get the actual data...
										$.getJSON('{{ $report_uri }}', param
										).fail(function (jqxhr, textStatus, error){
													doh_ajax_failed(jqxhr, textStatus, error);
													console.log('I get to this fail');
												}
										)
												.done(function(data) {

													var cards_html = "<div class='row justify-content-left'>";
													var i = 0;
													var block_count = 1;
													var new_row = false;
													var is_empty = true;

													var card_width = '{{ $report->cardWidth() }}';
													var real_card_new_row = '';
													var real_card_group_label = '';
													var block_class = '';

													data.data.forEach(function(this_card) {
														is_empty = false; //we hqve at least one.


														//we use several isset commands to ensure that when data is not set..
														//that those sections simply do not show up...
														//making the card gracefully simplify as data reduces...

														if(isset(this_card.card_img_top)){

															//sometimes images have alttext something they have links..
															//this sorts all of this out

															//by default do nothing..
															card_alt_text = '';
															card_anchor_open = '';
															card_anchor_close = '';

															//if there is altext, use it
															if(isset(this_card.card_img_top_alttext)){
																card_alt_text = ` alt="${this_card.card_img_top_alttext}" `;
															}
															//if there an an anchor use it
															if(isset(this_card.card_img_top_anchor)){
																card_anchor_open  = `<a target='_blank' href="${this_card.card_img_top_anchor}"> `;
																card_anchor_close  = `<\a>`;
															}

															//this could be complex or simple depending on the above logic
															card_img_top = `${card_anchor_open} <img ${card_alt_text} style="width: ${card_width}" class="card-img-top" src="${this_card.card_img_top}"> ${card_anchor_close}`;
														}else{
															card_img_top = '';
														}


														if(isset(this_card.card_img_bottom)){

															//sometimes images have alttext something they have links..
															//this sorts all of this out

															//by default do nothing..
															card_alt_text = '';
															card_anchor_open = '';
															card_anchor_close = '';

															//if there is altext, use it
															if(isset(this_card.card_img_bottom_alttext)){
																card_alt_text = ` alt="${this_card.card_img_bottom_alttext}" `;
															}
															//if there an an anchor use it
															if(isset(this_card.card_img_bottom_anchor)){
																card_anchor_open  = `<a target='_blank' href="${this_card.card_img_bottom_anchor}"> `;
																card_anchor_close  = `<\a>`;
															}

															//this could be complex or simple depending on the above logic
															card_img_bottom = `${card_anchor_open} <img ${card_alt_text} style="width: ${card_width}" class="card-img-bottom" src="${this_card.card_img_bottom}"> ${card_anchor_close}`;
														}else{
															card_img_bottom = '';
														}

														if(isset(this_card.card_header)){
															real_card_header = `<div class="card-header text-center">${this_card.card_header}</div>`;
														}else{
															real_card_header = '';
														}

														if(isset(this_card.card_footer)){
															real_card_footer = `<div class="card-header text-center">${this_card.card_footer}</div>`;
														}else{
															real_card_footer = '';
														}

														if(isset(this_card.card_title)){
															real_card_title = `<h5 class='card-title'> ${this_card.card_title}</h5>`;
														}else{
															real_card_title = '';
														}

														if(isset(this_card.card_text)){
															real_card_text = `<p class="card-text">${this_card.card_text}</p>`;
														}else{
															real_card_text = '';
														}

														//card_body is better for direct html... card_text is better for just text...
														if(isset(this_card.card_body)){
															real_card_body = `${this_card.card_body}`;
														}else{
															real_card_body = '';
														}

														if(isset(this_card.card_body_class)){

															card_body_class = `${this_card.card_body_class}`;
														}else{
															card_body_class = 'p-1'; //pretty compact by default
														}
		

														text_plus = real_card_text + real_card_title + real_card_body;
														text_plus = text_plus.trim(); //should make an empty string if they are both blank..
														if(text_plus.length == 0){
															//then we do not need the card-body at all..
															merged_card_body = '';
														}else{
															merged_card_body = `<div class="card-body ${card_body_class}"> ${real_card_title} ${real_card_text} ${real_card_body}  </div>`;
														}

														if(isset(this_card.card_layout_block_id)){
															//then we are shifting the background color of the cards...
															//and the 'newline' of the groups of cards to delinate a grouping of cards...

															if(i != 0){ // then this is not the first card.
																if(last_block_id == this_card.card_layout_block_id){
																	//great! there is nothing to do...
																	//but we do need to potentially reset some things that were done before the last card..
																	real_card_new_row = '';
																	real_card_group_label = '';



																	//we keep the current block class !!
																	//so we do not touch that here...
																}else{

																	block_count++; //this is a new block!!

																	//well now a change has occured... we need a newline for sure and possibly a new label..
																	//real_card_new_row = `<div class="w-100"></div>`;
																	real_card_new_row = `</div> <!-- end row --> <div class="row"> `;
							if(isset(this_card.card_layout_block_label)){ //we have a label... so we will use it to seperate the card blocks
								//is there a link or not?
								if(isset(this_card.card_layout_block_url)){ //then we also have a url for the label
									group_label_open_a = `<a target='_blank' href='${this_card.card_layout_block_url}'>`;
									group_label_close_a = `</a>`;
																}else{ //we have the label but no url here...
																	group_label_open_a = '';
																	group_label_close_a = '';
																}
																//we have a label, with or without a link..
																real_card_group_label = `
</div> <!-- end the row -->	<div class="zermelo-card-group-label">
																<h3> ${group_label_open_a} ${this_card.card_layout_block_label} ${group_label_close_a} </h3>
																</div> <!-- end the big column -->
																<div class='row'>
																`;
							}else{
								//there was no label... so we just need to have a newline between the rows...
								real_card_group_label = '';
							}



							//reset the last block id to this new one
							last_block_id = this_card.card_layout_block_id;

							if( block_count % 2 == 0){
								//this is an 'even' row and needs to be colored differently..
								block_class = ' text-white bg-secondary ';
							}else{
								//change it back!!
								block_class = ' bg-light ';
							}

						}
					}else{ //then this is the very first card.. lets setup our variables...
						last_block_id = this_card.card_layout_block_id;


						//no newline to start
						real_card_new_row = '';

						//but we do want to have a label
						if(isset(this_card.card_layout_block_label)){
							if(isset(this_card.card_layout_block_url)){ //then we also have a url for the label
								group_label_open_a = `<a target='_blank' href='${this_card.card_layout_block_url}'>`;
								group_label_close_a = `</a>`;
															}else{ //we have the label but no url here...
																group_label_open_a = '';
																group_label_close_a = '';
															}
															//we have a label, with or without a link..
															real_card_group_label = `
</div><!-- end the row -->	<div class="zermelo-card-group-label">
															<h3> ${group_label_open_a} ${this_card.card_layout_block_label} ${group_label_close_a} </h3>
															</div> <!-- end the big column -->
															<div class='row'>
															`;
						}else{
							real_card_group_label = '';
						}
						//and we want to alternate style of the card... but we start with the light setting..
						block_class = ' bg-light ';

					}

				}


				cards_html += `
																	${real_card_new_row}
																	${real_card_group_label}

															<div class="col-auto mb-3">
															<div style='width: ${card_width}' class="card ${block_class} " >
																	${card_img_top}
																	${real_card_header}
																	${merged_card_body}
																	${real_card_footer}
																	${card_img_bottom}
															</div>
															</div>

															`;
				i++;

			})


			cards_html += "</div>";

			$('#div_for_cards').html(cards_html);

			// If the result is empty, lets tell the user by showing alert message
			if (is_empty === true) {
				$('#json_info_message').html("<h1> Your report query returned no results.  </h1>");
				$('#json_info_message').show();
			}

                    }); // End of .done()


        }); /* end always on get Summary */


    });

function isset () {
  //  discuss at: http://locutus.io/php/isset/
  // original by: Kevin van Zonneveld (http://kvz.io)
  // improved by: FremyCompany
  // improved by: Onno Marsman (https://twitter.com/onnomarsman)
  // improved by: Rafał Kukawski (http://blog.kukawski.pl)
  //   example 1: isset( undefined, true)
  //   returns 1: false
  //   example 2: isset( 'Kevin van Zonneveld' )
  //   returns 2: true

  var a = arguments
  var l = a.length
  var i = 0
  var undef


  if (l === 0) {
    throw new Error('Empty isset')
  }

  while (i !== l) {
    if (a[i] === undef || a[i] === null || a[i] === '') {
      return false
    }
    i++
  }

  return true
}




</script>


