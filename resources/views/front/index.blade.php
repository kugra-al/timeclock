@extends('layouts.default')

@section('css')
#actionRow{display:none;}

@endsection

@section('content')
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<style>body{padding-left: 20px;}</style>
<form method="post" action="" id="staff-login-form">
	<div class="row">
		<table width="100%">
			<tr>
				<td>
		
			<div class="row">
				<div class="col-md-4"></div>
				<div class="form-group col-cm-4">
					<label for="Staff">Staff ID</label>
					<input type="number" class="form-control" name="staff_id">
				</div>
			</div>
			<div class="row">
				<div class="col-md-4"></div>
				<div class="form-group col-cm-4">
					<label for="Pin">Pin</label>
					<input type="password" class="form-control" name="pin">
				</div>
			</div>
			</td>
		<td>
	
			<div class="row">
		
					<ul id="signinList" style="list-style-type:none;padding:0px">

					
					</ul>


			</div>
		</td>
	</tr>
</table>
</div>
	<input type="hidden" name="action">
</form>
	<div class="row" id="actionRow">

	    <label for="action">Action</label>
		    <table width="100%" id="actionBlock">
		    	<thead></thead>
		    	<tr>
		    		<td class="clockin">
		    			<h4>1</h4>
		    			<p>Clock In</p>
		    		</td>
		    		<td class="clockout">
		    			<h4>2</h4>
		    			<p>Clock Out</p>
		    		</td>
		    		<td class="breakstart">
		    			<h4>3</h4>
		    			<p>Start Break</p>
		    		</td>
		    		<td class="breakend">
		    			<h4>4</h4>
		    			<p>End Break</p>
		    		</td>
		    	</tr>
		    </table>
		    <table width="100%" id="actionBlock">
		    	<thead></thead>
		    	<tr>
		    		<td class="clockinfo">
		    			<h4>5</h4>
		    			<p>Status</p>
		    		</td>
		    		<td class="clockcancel">
		    			<h4>0</h4>
		    			<p>Cancel/Refresh</p>
		    		</td>
		    	</tr>
		    </table>

	</div>

<div class="modal" tabindex="-1" role="dialog" id="actionModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-body-footer" style="position: relative;
    -webkit-box-flex: 1;
    -ms-flex: 1 1 auto;
    flex: 1 1 auto;
    padding: 1rem;">
        <p>Press 1 for YES or 2 for NO</p>
        <input type="hidden" name="action"> 
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary">1 - YES</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">2 - NO</button>
      </div>
    </div>
  </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="infoModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-body-footer" style="position: relative;-webkit-box-flex: 1;-ms-flex: 1 1 auto;flex: 1 1 auto;padding: 1rem;">
        <p>Press 1 to DISMISS</p>
        <input type="hidden" name="action"> 
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary">1 - DISMISS</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('javascript')
	function reloadSigninList() {
		$('#signinList').html("");
		$.ajax({
           type:'POST',
           url:'/ajax/front',
           data:{'action':'getSigninList'},
           success:function(data){
           		console.log(data);
           		var staff = null;
           		if (data.staff) {
           			staff = data['staff'];
           		}
           		if (staff) {
           			$('#signinList').html("");
					$.each(staff,function(id,member){
						var cls = '';
						var msg = '';
						var time = member['time'];
						var name = member['name'];
						if (member['type'] == "break") {
							cls = 'breakstart';
							msg = 'break';
						}
						else {
							cls = 'clockin';
							msg = 'in';
						}

						$('#signinList').append("<li class='"+cls+"'>"+name+" "+msg+" "+time+"</li>");

					});
				}
			}
		});
	}

	function flashMsg(msg,reload = false,type = 'success') {
		console.log(msg);
		$('#flashMsg').removeClass();
		$('#flashMsg').addClass('alert alert-'+type);
		$('#flashMsg').text(msg);
		$('#flashMsg').show();
		$('#flashMsg').shake({
	          		 interval: 100,
	           		 distance: 20,
	          		  times: 2
	       		 });
		if (reload) {
			setTimeout(function(){ $('#flashMsg').slideUp(); }, 1000);
			setTimeout(function(){ location.reload(); }, 1500);
		}

	}

	function playSound(type = 'error') {
		var audioFile = '';
		switch (type) {
			case "error"	:	audioFile = 'error';
								break;
			case "clockin"	:
			case "clockout" :   audioFile = 'clock';
								break;
			case "button"	:	audioFile = 'button';
								break;
			case "break"	:
			case "ding"		:	audioFile = 'ding2';
								break;
			default 		: 	audioFile = 'error';
								break;
		}
		var audio = new Audio('/audio/'+audioFile+'.mp3');
		audio.play();
	}

@endsection
@section('javascript-ready')
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });


    function getAvailableEvents() {
       	var form = $('#staff-login-form');
        var staffid = $(form).find("input[name=staff_id]").val();
        var pin = $(form).find("input[name=pin]").val();
        var action = $(form).find("input[name=action]").val();

        $.ajax({
           type:'POST',
           url:'/ajax/front',
           data:{'staff_id':staffid, 'pin':pin, 'check':true},
           success:function(data){
           	var events;
	           if (data.success && data.success.length) {
	           		events = data.success[0];
	           		captureKeys(events);
	           } else {
	           	  if (data.errors && data.errors.length) {
	           	  	alert(data.errors);
	           	  	playSound('error');
	           	  	location.reload();

	       		}
	       	}
           
          // 	return data;
           	console.log(data);
       	   }
       	});
	}



    function submitActionForm () {
       	var form = $('#staff-login-form');
        var staffid = $(form).find("input[name=staff_id]").val();
        var pin = $(form).find("input[name=pin]").val();
        var action = $(form).find("input[name=action]").val();

        $.ajax({
           type:'POST',
           url:'/ajax/front',
           data:{'staff_id':staffid, 'pin':pin, 'action':action},
           success:function(data){
           console.log(data);
         //  alert(JSON.stringify(data));
       			var msg = "";
       			var type;
           		if (data.errors) {
           			type = 'danger';
           			msg = "Errors:\n ";
           			playSound('error');
           			$.each(data.errors,function(id,error){
           				msg += error+"\n";    
           			});
           		}
           	 	if (data.success) {
           	 		if (action == 1)
           	 			playSound('clockin');
           	 		if (action == 2)
           	 			playSound('clockout');
           	 		if (action == 3 || action == 4)
           	 			playSound('break');
           	 		type = 'success';

           	 		msg += "\nSuccess "+JSON.stringify(data.success);
           		
           	 	}
           	 	//alert(msg);
       	 		clearForms();
           	 	flashMsg(msg,true,type);

           	 	
             
           }
        });
    }

clearForms();

function clearForms() {
	reloadSigninList();
	$('#actionRow').hide();
	$('#staff-login-form').show();
	$('#refreshLink').show();
	$('#staff-login-form input').val("");
	$("input[name='staff_id']").focus();
	$('body').off('keypress');
	$('#staff-login-form input').off('keypress');
	// Bind * to refresh
	$('body').keypress(function(event){
	    if (event.keyCode === 42) { 
		 	location.reload();
		}
	});
	$('#staff-login-form input').keypress(function(event){

	    if (event.keyCode === 10 || event.keyCode === 13) { 
	        event.preventDefault();
	         var index = $('#staff-login-form input').index(this) + 1;
	         console.log(index);
	         if (index !== 2) {
	         	$('#staff-login-form input').eq(index).focus();
	         	$('#staff-login-form input').eq(index).shake({
	          		 interval: 100,
	           		 distance: 20,
	          		  times: 2
	       		 });
	         } 
	         if (index === 2) {
	         console.log('ok');
	         $(this).blur();
			 
			 getAvailableEvents();


	      	
	     	}
	     }
	});
}


function captureKeys(events) {
	    
	//$('#actionBlock').find('.col-sm-3').removeClass('disabled');
    $('#actionBlock').shake({
        interval: 100,
        distance: 20,
        times: 2
    });

console.log(events);

	

	$('#actionRow').slideDown();

	$('#staff-login-form').slideUp();
	$('#refreshLink').hide();
	if (!events['isClockedIn']) {
		$('#actionBlock .clockout').addClass('disabled');
		$('#actionBlock .breakstart').addClass('disabled');
		$('#actionBlock .breakend').addClass('disabled');
	} else {
		$('#actionBlock .clockin').addClass('disabled');
		if (events['isOnBreak']) {
			$('#actionBlock .breakstart').addClass('disabled');
		} else {
				$('#actionBlock .breakend').addClass('disabled');
		}
	}
	$('body').keypress(function(event){

		if ($("#actionModal").is(':hidden') && $('#infoModal').is(':hidden')) {
			// 1 and numpad 1
			if (event.keyCode === 49 || event.keyCode == 97) {
				if (!events['isClockedIn'])
					actionConfirm(1);
			
			}
			// 2 and numpad 2
			if (event.keyCode === 50 || event.keyCode == 98) {
				if (events['isClockedIn'])
					actionConfirm(2);
				
			}
			// 3 and numpad 3
			if (event.keyCode === 51 || event.keyCode == 99) {
				if (!events['isOnBreak'] && events['isClockedIn'])
					actionConfirm(3);
				
			}
			// 4 and numpad 4
			if (event.keyCode === 52 || event.keyCode == 100) {
				if (events['isOnBreak'] && events['isClockedIn'])
					actionConfirm(4);
				
			}
			// 5 and numpad 5
			if (event.keyCode === 53 || event.keyCode == 101) {
				showStaffInfo();
			}
			// 6 and numpad 6
//			if (event.keyCode === 54 || event.keyCode == 102) {
//				actionConfirm(6);
//			}
			// 0 and numpad 0
			if (event.keyCode === 48 || event.keyCode == 96) {
				//$('body').fadeOut('slow');
				location.reload()
				//setTimeout(function(){location.reload()},1000);
			}
		} else {
			$("#actionModal").modal('hide');
			// 1 and numpad 1
			if (event.keyCode === 49 || event.keyCode == 97 || event.keyCode === 13) {
				var action = $("#actionModal").find("input[name='action']").val();
				$('#staff-login-form').find('input[name=action]').val(action);
				submitActionForm();
			}
			// 2 and numpad 2
			if (event.keyCode === 50 || event.keyCode == 98) {
				alert('lets not do that then');
				playSound('error');
				location.reload();
			}
		}
	});
}

function showStaffInfo() {
   	var form = $('#staff-login-form');
    var staffid = $(form).find("input[name=staff_id]").val();
    var pin = $(form).find("input[name=pin]").val();
    //var action = $(form).find("input[name=action]").val();

    $.ajax({
       type:'POST',
       url:'/ajax/front',
       data:{'staff_id':staffid, 'pin':pin, 'action':5},
       success:function(data){
       		console.log(data);
	     //  alert(JSON.stringify(data));
				var msg = "";
	   		if (data.errors) {
	   			msg = "Errors "+JSON.stringify(data.errors);      
	   		}
	   	 	if (data.success) {

	   	 		clearForms();
	   	 		if (data.success.length)
	   	 			msg += data.success[0];
	   		
	   	 	}
			var modal = $('#infoModal');
			if (modal.length) {
				var title = "Info";
				$(modal).find('.modal-body').html("<p>"+msg+"</p>");
				$(modal).find('.modal-title').text(title);
				
				$(modal).modal();
			}
	//           	 	location.reload();
			$('body').keypress(function(event){
				if (event.keyCode === 49 || event.keyCode == 97) {
					location.reload();
				}
			});         
    	}
	});

}
function actionConfirm (action) {
	var msg, title;

	switch(action) {
		case 1 		:	title = "Clock In";
						msg = "Are you sure you want to Clock In?";	
						break;
		case 2 		:	msg = "Are you sure you want to Clock Out?";
						title = "Clock Out";
						break;
		case 3		:	msg = "Are you sure you want to Start Break?";
						title = "Start Break";
						break;
		case 4 		:	msg = "Are you sure to want to End Break?";
						title = "End Break";
						break;
//		case 6		:	msg = "Are you sure to want to Cancel Last TimeClock Event?";
//						title = "Cancel Event";
//						break;
		default 	:	alert("Unknown action: "+action);
						playSound('error');
						break;
	}	
	if (msg) {
		var modal = $("#actionModal");
		if (modal.length) {
			$(modal).find('.modal-body').html("<p>"+msg+"</p>");
			$(modal).find('.modal-title').text(title);
			$(modal).find("input[name='action']").val(action);
			$(modal).modal();
		}
	}
	console.log(msg);
}

(function($){
    $.fn.shake = function(settings) {
        if(typeof settings.interval == 'undefined'){
            settings.interval = 100;
        }

        if(typeof settings.distance == 'undefined'){
            settings.distance = 10;
        }

        if(typeof settings.times == 'undefined'){
            settings.times = 4;
        }

        if(typeof settings.complete == 'undefined'){
            settings.complete = function(){};
        }

        $(this).css('position','relative');

        for(var iter=0; iter<(settings.times+1); iter++){
            $(this).animate({ left:((iter%2 == 0 ? settings.distance : settings.distance * -1)) }, settings.interval);
        }

        $(this).animate({ left: 0}, settings.interval, settings.complete);  
    }; 
    $.fn.bounce = function(settings) {
        if(typeof settings.interval == 'undefined'){
            settings.interval = 100;
        }

        if(typeof settings.distance == 'undefined'){
            settings.distance = 10;
        }

        if(typeof settings.times == 'undefined'){
            settings.times = 4;
        }

        if(typeof settings.complete == 'undefined'){
            settings.complete = function(){};
        }

        $(this).css('position','relative');

        for(var iter=0; iter<(settings.times+1); iter++){
            $(this).animate({ top:((iter%2 == 0 ? settings.distance : settings.distance * -1)) }, settings.interval);
        }

        $(this).animate({ top: 0}, settings.interval, settings.complete);  
    };
})(jQuery);
        
$("input[name='staff_id']").shake({
           interval: 100,
            distance: 20,
            times: 2
        });




console.log('ok');
@endsection
