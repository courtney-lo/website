@php
	$title = "Step1: Upload your sequence files (fastq.gz)";
	$description = 
			"<ol>
				<li>Drag and drop your sequencing data files (*.fastq.gz)</li>
				<li>Click \"Enter Sample Information\" when finished.</li>  
			</ol>";
@endphp
@extends('layouts.master',["title"=>$title, "description"=>$description])


@section('content')
	<div class="row align-justify">
		<div class="columns ">
			
		</div>
	</div>
	<div id="drop-box" class="callout">
		<div class="row">
				<div class="columns medium-12">
					<div id="file-list">
					</div>
					<div id="drop-box-tips" class="center">
						<h4>Drag & drop FASTQ files here</h4>
					</div>
				</div>
			</div>
	</div>
	<div class="row align-right">
		<div class="columns shrink"><a id="done-uploading-button" class="button">Enter Sample Information</a></div>
	</div>

		
	
@stop
@section('customCSS')
@parent
	<link rel="stylesheet" type="text/css" href="/css/upload.css">
@stop

@section('customScripts')
@parent
<script>
	var runDirectory = "{{$dir}}";
	var userDoneUploading = false;
	var koTransPort = "{{ config('kotrans.port') }}";
	var appHost = "{{ config('kotrans.host') }}";
	var token = "{{csrf_token()}}";
</script>
<script type="text/javascript" src="/js/kotrans/kotrans.client.js"></script>
<script type="text/javascript" src="/js/binary.min.js"></script>
<script type="text/javascript" src="/js/upload.js"></script>
<script>
	
	var uploadManager = new uploader('#drop-box',runDirectory, koTransPort, appHost, token);
	uploadManager.setDoneUploadingCallBack (function (){
			 $.post(
			'/done-uploading/{{ $hash }}',{_token : token},
			function (data) {
				location.replace('/files/{{ $hash }}');
			}
		);
	});
	function doneUploadingClicked() {
		$('#done-uploading-button').addClass('disabled')
		uploadManager.setDoneUploading();
		// if (sending==true) {
		// 	userDoneUploading = true;
		// }
		// else{
		// 		redirectToNext();
		// }
	}
	$('#done-uploading-button').on('click', doneUploadingClicked);
		$(document).ready(function() {	
		uploadManager.initilize();
	});

	@if ($noEmail)
		$('document').ready(function(){$('#no-email').foundation('open');});
		function copyUrl() {
			var $temp = $("<input>");
			$("body").append($temp);
			$temp.val($('#run-url').text()).select();
			document.execCommand("copy");
			$temp.remove();
		}
	@endif
</script>
@stop

@section('customCSS')
@parent
<link rel="stylesheet" type="text/css" href="/css/upload.css">
@stop


{{-- No Email Modal --}}
<div class="reveal large" id="no-email" data-reveal>
	<h1>You have not provided an email address</h1>
	<p class="lead">
		Please save this url
	</p>
	<div class="input-group">
		<a class="input-group-label" id='run-url'href="{{ url("/run/$hash") }}">{{ url("/run/$hash") }}</a>
		<div class="input-group-button">
			<button type="button" class="button" onclick="copyUrl()">Copy</button>
		</div>
	</div>
	<p>If you close this page, and lose the url, you will have no way to get back to your run.</p>
	<button class="button float-right" data-close aria-label="Close modal" type="button">Ok</button>
	<button class="close-button" data-close aria-label="Close modal" type="button">
		<span aria-hidden="true">&times;</span>
	</button>
</div>