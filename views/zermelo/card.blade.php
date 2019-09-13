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

                var is_admin = true; //this should be set via a call to the presenter

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
                        'data-option': '',
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
					if(isset(this_card.card_img_top_alttext)){
						card_img_top = `<img style="width: ${card_width}" class="card-img-top" src="${this_card.card_img_top}" alt="${this_card.card_img_top_alttext}">`
					}else{
						card_img_top = `<img style="width: ${card_width}" class="card-img-top" src="${this_card.card_img_top}">`
					}
				}else{
					card_img_top = '';
				}
	
				if(isset(this_card.card_img_bottom)){
					if(isset(this_card.card_img_bottom_alttext)){
						card_img_bottom = `<img style="width: ${card_width}"  class="card-img-top" src="${this_card.card_img_bottom}" alt="${this_card.card_img_bottom_alttext}">`
					}else{
						card_img_bottom = `<img style="width: ${card_width}"  class="card-img-top" src="${this_card.card_img_bottom}">`
					}
				}else{
					card_img_bottom = '';
				}

				if(isset(this_card.card_header)){
					real_card_header = `<div class="card-header text-center">${this_card.card_header}</div>`;
				}else{
					real_card_header = ''
				}

				if(isset(this_card.card_footer)){
					real_card_footer = `<div class="card-header text-center">${this_card.card_footer}</div>`;
				}else{
					real_card_footer = ''
				}


				cards_html += `
<div class="col-auto mb-3">
	<div style='width: ${card_width}' class="card" >
		${card_img_top}
  		${real_card_header}
  		<div class="card-body">
    			<h5 class="card-title">${this_card.card_title}</h5>
    			<p class="card-text">${this_card.card_text}</p>
  		</div>
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
