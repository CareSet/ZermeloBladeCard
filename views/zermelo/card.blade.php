<div class="container-fluid">
	<div>
		<h1> {{ $report->GetReportName()  }}</h1>
	</div>
	<div>
		{!! $report->GetReportDescription() !!}
	</div>

	<div style='display: none' id='json_error_message' class="alert alert-danger" role="alert">

	</div>

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


<script type="text/javascript" src="/vendor/CareSet/js/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="/vendor/CareSet/js/popper.min.js"></script>
<script type="text/javascript" src="/vendor/CareSet/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/vendor/CareSet/js/datatables.min.js"></script>
<script type="text/javascript" src="/vendor/CareSet/js/jquery.dataTables.yadcf.js"></script>
<script type="text/javascript" src="/vendor/CareSet/js/moment.min.js"></script>
<script type="text/javascript" src="/vendor/CareSet/js/daterangepicker.js"></script>
<script type="text/javascript" src="/vendor/CareSet/js/d3.v4.min.js"></script>
<script type="text/javascript" src="/vendor/CareSet/js/datatables.fixedcolumns.destroy.js"></script>
<script type="text/javascript" src="/vendor/CareSet/js/jquery.doubleScroll.js"></script>

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
			var new_row = false;
			var is_empty = true;

			var card_width = '{{ $report->cardWidth() }}';

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
	

				if(isset(this_card.card_bottom)){

					//sometimes images have alttext something they have links..
					//this sorts all of this out

					//by default do nothing..					
					card_alt_text = '';
					card_anchor_open = '';
					card_anchor_close = '';

					//if there is altext, use it
					if(isset(this_card.card_img_bottom_alttext)){
						card_alt_text = ` alt="${this_card.card_img_top_alttext}" `;
					}
					//if there an an anchor use it
					if(isset(this_card.card_img_bottom_anchor)){
						card_anchor_open  = `<a target='_blank' href="${this_card.card_img_top_anchor}"> `;
						card_anchor_close  = `<\a>`;
					}

					//this could be complex or simple depending on the above logic
					card_img_bottom = `${card_anchor_open} <img ${card_alt_text} style="width: ${card_width}" class="card-img-top" src="${this_card.card_img_top}"> ${card_anchor_close}`;
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

				text_plus_title = real_card_text + real_card_title;
				text_plus_title = text_plus_title.trim(); //should make an empty string if they are both blank..
				if(text_plus_title.length == 0){
					//then we do not need the card-body at all..
					real_card_body = '';
				}else{
					real_card_body = `<div class="card-body"> ${real_card_title} ${real_card_text}  </div>`;
				}

				cards_html += `
<div class="col-auto mb-3">
	<div style='width: ${card_width}' class="card" >
		${card_img_top}
  		${real_card_header}
		${real_card_body}
    		${real_card_footer}
		${card_img_bottom}
	</div>
</div>
`;
				i++;
	
			})

			cards_html += "</div>";

			$('#div_for_cards').html(cards_html);

                    });


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


