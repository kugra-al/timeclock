<html>
	<head>
		<title>JWS TimeClock</title>
		<link rel="stylesheet" href="{{asset('css/app.css')}}">
		<link rel="stylesheet" href="{{asset('css/time.css')}}">
			<meta name="csrf-token" content="{{ csrf_token() }}">
	</head>
	<body>
		<style>
			#adminLink {
				position: absolute;
			    top: 10px;
			    right: 20px;
			}
			#refreshLink {
				position:absolute;
				top: 0px;
				left: 5px;
				font-size: 32px;
				float:left;
			}
			#flashMsg {     
				position:absolute;
				left: 20px;
    			/* font-size: 32px; */
    			text-align: text;
    			display:none;
     		}
     		#signinList li { padding:3px; margin:2px; }
     		#flashMsgContainer{height:20px;}
     		#staffLoginForm{width:100%; position: absolute; top:30px;}
     		@yield('css')

     		body { background: #585858; color: #FFF; }
			a { color: #ff7777; }
			.modal {color: #000;}
			.form-control { font-size:20px; }
		</style>
		<div class="container">
			<div class="row">
				<div class="col-sm-12" style="padding-top:50px;">
				@if (Session::has('success'))
					<div class="alert alert-success">
						<p>{{ Session::get('success') }}</p>
					</div>
				@endif

				
		</div>
		<div class="container">
		@yield('content')

		</div>
		<div id="flashMsgContainer"><div id="flashMsg">You have successfully started your break</div></div>
		<a href="/admin" id="adminLink">Admin</a><br/>
		<a href="#" onClick="location:reload();return false;" id="refreshLink">Press * to Refresh</a>
		

	</div>
		<script src="{{ asset('js/app.js') }}"></script>
		<script>
			@yield('javascript')
		 	@yield('javascript-ready')

		</script>
		<script>

		 	</script>
	</body>
</html>
