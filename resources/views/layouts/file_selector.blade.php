<?php
	/* Provided variables
		$result => The result tab this selector is in
		$withControl => Boolean. True if this result tab shows control files
		$runId => The id of the run
		$runHash => The hash that serves as the run dir name
	 */
	

	$mapping = \File::get(storage_path("runs/$runHash/fileMap.json"));
	$mapping = json_decode($mapping,true);
	$files = [];
	if($withControl){
		$files = $files + $mapping['control'];
	}
	$files = $files + $mapping['treatment'];

	$beforeSelectorRow = $beforeSelectorRow ?? "";
	$afterSelectorRow = $afterSelectorRow ?? "";
	$afterSelectorColumn = $afterSelectorColumn ?? "";
?>
{!! $beforeSelectorRow !!}
<div class="row">
	<div class="column">
		<select id="{{ $result }}_selector">
			@foreach ($files as $fileName => $fileProperties)
				@php $prefix = $fileProperties['condition'].'_'.$fileProperties['index']; @endphp
				<option value="{{ $fileName }}" data-prefix="{{ $prefix }}">
					{{ $prefix }} ({{ $fileName }})
				</option>
			@endforeach
		</select>
	</div>
	{!!$afterSelectorColumn !!}
</div>
{!! $afterSelectorRow !!}
@foreach ($files as $fileName => $fileProperties)
	<div class="row align-center" id="{{ $fileName }}_{{ $result }}" @if (!$loop->first) style="display:none;" @endif>
		<div class="column shrink">
			@include("results.".$result."_component", 
				[ 'prefix'=>$fileProperties['condition'].'_'.$fileProperties['index'],
					'fileName'=>$fileName, 'fileProperties'=>$fileProperties,
					'runHash'=>$runHash
				])
		</div>
	</div>
@endforeach

<script>
	$("#{{ $result }}_selector").change(function () {
		fileSelectorChange("{{ $result }}")
	});
	function fileSelectorChange(param) {
		var selected = $("#"+param+"_selector").val();
		$("[id$=_"+param+"]").hide();
		$("[id='"+selected+"_"+param+"']").show();
	}
</script>