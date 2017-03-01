@extends('layouts.master')
<?php
	$filePlaceHolders = [
		"ToxA",
		"ToxB",
		"Subject_13",
		"Hair_From_Steve",
		"that_guy_two_cubicles_down"
	];
?>
@section('content')
<div class="row">
	<div class="column medium-5"><strong>Filename</strong></div>
	<div class="column medium-2"><strong title="Select a group for each file">File Group</strong></div>
	<div class="column medium-5"><strong title="Rename the file to make it easier on yourself. Invalid characters will be replaced with underscores">Condition</strong></div>
</div>
<form action="/configure-files/{{ $run->id }}" method="POST">
<input type="hidden" name="_token" value="{{ csrf_token() }}">
@foreach ($files as $file)
	<?php $file = basename($file); ?>
	<div class="row" id="{{ $file }}">
		<div class="column medium-5 file-name"><span title="{{ $file }}">{{ $file }}</span></div>
		<div class="column medium-2">
			<select name="group-{{$file}}" required>
				<option value="" hidden></option>
				<option value="control">Control</option>
				<option value="treatment">Treatment</option>
			</select>
		</div>
		<div class="column medium-5"><input type="text" name="rename-{{$file}}" placeholder="{{ $filePlaceHolders[array_rand($filePlaceHolders)] }}"></div>
	</div>
@endforeach
<div class="row align-right">
	<div class="column shrink"><input type="submit" class="button success" value="Configure Parameters"></div>
</div>
</form>
@stop
@section('customCSS')
<style type="text/css">
	.file-name {
		overflow: hidden;
		text-overflow: ellipsis;
	}
</style>
@stop
@section('customScripts')
@stop